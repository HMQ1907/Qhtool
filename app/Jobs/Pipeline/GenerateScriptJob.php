<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use App\Support\AffiliateCreativeBrief;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GenerateScriptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(public CampaignVideo $video)
    {
    }

    public function handle(): void
    {
        $this->video->update(['status' => 'generating_script']);

        try {
            $duration = max(12, min((int) $this->video->duration_seconds, 35));
            $hashtags = AffiliateCreativeBrief::hashtags();

            $prompt = $this->buildPrompt($duration);

            $response = Http::timeout(120)
                ->withToken(config('evolink.api_key'))
                ->post(rtrim(config('evolink.base_url'), '/') . '/chat/completions', [
                    'model' => config('evolink.text_model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Ban la mot bien kich TikTok Shop Affiliate gioi o Viet Nam. Uu tien retention, niem tin va y dinh bam gio hang. Viet ngan, that, ban hang mem, khong noi qua, khong claim y te, khong hua hen ket qua chac chan.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ],
                    ],
                    'temperature' => $this->temperatureForMode(),
                ]);

            if ($response->failed()) {
                throw new RuntimeException('Evolink API error (Script): ' . $response->body());
            }

            $script = trim((string) ($response->json('choices.0.message.content') ?? ''));
            $script = trim(str_replace(['"', '*', '#'], '', $script));

            if ($script === '') {
                throw new RuntimeException('Text model returned an empty affiliate script.');
            }

            $this->video->update([
                'script_text' => $script,
                'caption' => $this->buildCaption($script, $hashtags),
                'hashtags' => $hashtags,
                'status' => 'generating_voice',
            ]);

            GenerateVoiceJob::dispatch($this->video);
        } catch (\Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->markCampaignFailedIfNeeded();
            throw $e;
        }
    }

    private function buildPrompt(int $duration): string
    {
        $mode = (string) ($this->video->generation_mode ?: 'fast_test');
        $brief = AffiliateCreativeBrief::forVariation($mode, max(0, (int) $this->video->id));
        $targetWords = $this->targetWords($duration);

        return implode("\n", [
            "Hay viet kich ban voice-over tieng Viet dai khoang {$duration} giay, toi da {$targetWords} tu, cho video TikTok Shop Affiliate.",
            "San pham: {$this->video->product_name}",
            "Mo ta/USP nguoi dung nhap: " . ($this->video->product_description ?: 'khong co'),
            "Gia/khoang gia neu co: " . ($this->video->product_price ?: 'khong co'),
            "Noi dau/pain point can cham: " . ($this->video->product_pain_points ?: 'tu suy luan an toan tu ten san pham, khong bia tinh nang cu the'),
            "Review/social proof neu co: " . ($this->video->product_reviews ?: 'khong co'),
            "Thong tin hoa hong noi bo, chi dung de uu tien san pham neu can, khong doc ra video: " . ($this->video->commission_rate ?: 'khong co'),
            "Goc ban hang da chon: {$this->video->sales_angle}",
            "Hook nen tham khao: {$brief['hook']}",
            AffiliateCreativeBrief::modeInstruction($mode),
            "Yeu cau:",
            "1. Cau dau phai la hook duoi 12 tu, cham dung van de hoac su to mo, khong mo dau bang 'hom nay minh review'.",
            "2. Moi cau phai doc duoc bang voice-over, cau ngan, tu nhien nhu review that.",
            "3. Khong noi qua, khong cam ket ket qua, khong claim y te/lam dep/thu nhap neu khong co thong tin.",
            "4. Neu thieu thong tin, hay noi theo kieu 'dang de can nhac', 'co the hop voi', 'nen xem them trong gio hang', khong tu bia thong so.",
            "5. Neu co gia, chi noi mem nhu 'trong tam gia nay' hoac 'neu gia trong gio hang dang tot' va khong doc con so neu nguoi dung khong nhap.",
            "6. Chen 1 cau tao niem tin: noi ro san pham khong phai than ky, nhung giai quyet dung mot viec cu the.",
            "7. CTA cuoi dung y nay, co the bien tau nhe: 'Bam vao gio hang goc trai de xem them san pham.'",
            "8. Khong hashtag, khong markdown, khong bullet, khong tieu de. Chi tra ve loi thoai duy nhat.",
        ]);
    }

    private function targetWords(int $duration): int
    {
        return max(32, min(85, (int) floor($duration * 2.45)));
    }

    private function temperatureForMode(): float
    {
        return match ($this->video->generation_mode) {
            'winner_scale' => 0.7,
            'premium_product' => 0.78,
            default => 0.9,
        };
    }

    private function buildCaption(string $script, array $hashtags): string
    {
        $firstLine = trim(strtok($script, "\n") ?: $script);
        $firstLine = mb_substr($firstLine, 0, 120);

        return $firstLine . "\n\n" . implode(' ', $hashtags);
    }

    private function markCampaignFailedIfNeeded(): void
    {
        $campaign = $this->video->campaign;

        if ($campaign->videos()->where('status', 'failed')->exists()) {
            $campaign->update(['status' => 'failed']);
        }
    }
}

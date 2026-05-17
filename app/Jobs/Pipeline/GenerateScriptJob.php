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
                            'content' => 'Ban la mot bien kich TikTok Shop Affiliate gioi o Viet Nam. Viet ngan, that, ban hang mem, khong noi qua, khong claim y te.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ],
                    ],
                    'temperature' => 0.85,
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
        return implode("\n", [
            "Hay viet kich ban voice-over tieng Viet dai khoang {$duration} giay cho video TikTok Shop Affiliate.",
            "San pham: {$this->video->product_name}",
            "Mo ta nguoi dung nhap: " . ($this->video->product_description ?: 'khong co'),
            "Goc ban hang: {$this->video->sales_angle}",
            "Mode: {$this->video->generation_mode}",
            "Yeu cau:",
            "1. Cau dau phai co hook manh trong 2 giay dau.",
            "2. Giong noi nhu review that, khong qua quang cao, khong noi qua.",
            "3. Neu thong tin san pham thieu, viet an toan va chung vua du, khong tu bia cong dung cu the.",
            "4. Neu la do gia dung/tien ich, tap trung vao su tien, gon, de dung, tiet kiem thoi gian.",
            "5. Khong nhac den gia, hoa hong, link bio. CTA cuoi: 'Bam vao gio hang goc trai de xem them san pham.'",
            "6. Khong hashtag, khong markdown, khong bullet. Chi tra ve loi thoai.",
        ]);
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

<?php

namespace App\Jobs\Pipeline;

use App\Models\CampaignVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class GenerateVoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(public CampaignVideo $video)
    {
    }

    public function handle(): void
    {
        $this->video->update(['status' => 'generating_voice']);

        try {
            if (blank($this->video->script_text)) {
                throw new RuntimeException('Cannot generate voice because script_text is empty.');
            }

            $filename = config('evolink.local_tts_enabled', true)
                ? $this->generateLocalTts($this->scriptForTts($this->video->script_text))
                : $this->generateEvolinkTts($this->scriptForTts($this->video->script_text));

            $this->video->update([
                'voice_audio_url' => Storage::url($filename),
                'status' => 'rendering',
            ]);

            RenderFinalVideoJob::dispatch($this->video);
        } catch (\Exception $e) {
            $this->video->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->markCampaignFailedIfNeeded();
            throw $e;
        }
    }

    private function generateLocalTts(string $script): string
    {
        $filename = 'voices/' . Str::uuid() . '.mp3';
        $outputPath = Storage::disk('public')->path($filename);

        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0775, true);
        }

        $process = new Process([
            'edge-tts',
            '--voice',
            (string) config('evolink.local_tts_voice', 'vi-VN-HoaiMyNeural'),
            '--rate',
            (string) config('evolink.local_tts_rate', '+8%'),
            '--text',
            $script,
            '--write-media',
            $outputPath,
        ]);

        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputPath) || filesize($outputPath) < 1024) {
            throw new RuntimeException('Local Edge TTS failed: ' . trim($process->getErrorOutput() ?: $process->getOutput()));
        }

        return $filename;
    }

    private function generateEvolinkTts(string $script): string
    {
        $task = $this->generateTtsWithRetry($script);
        $audioUrl = $this->extractAudioUrl($task);

        $audioContent = Http::timeout(120)->get($audioUrl);
        if ($audioContent->failed()) {
            throw new RuntimeException('Failed to download audio from: ' . $audioUrl);
        }

        $filename = 'voices/' . Str::uuid() . '.mp3';
        Storage::disk('public')->put($filename, $audioContent->body());

        return $filename;
    }

    private function generateTtsWithRetry(string $script): array
    {
        $voiceId = $this->configuredVoice();

        try {
            return $this->generateTts($script, $voiceId);
        } catch (RuntimeException $e) {
            Log::warning('TTS failed, retrying once with fallback voice.', [
                'video_id' => $this->video->id,
                'voice' => $voiceId,
                'error' => $e->getMessage(),
            ]);

            return $this->generateTts($script, 'male_deep');
        }
    }

    private function generateTts(string $script, string $voiceId): array
    {
        $response = Http::timeout(120)
            ->withToken(config('evolink.api_key'))
            ->post($this->endpoint('/audios/generations'), [
                'model' => config('evolink.voice_model', 'qwen3-tts-vd'),
                'prompt' => $script,
                'voice' => $voiceId,
                'response_format' => 'mp3',
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Evolink API error (Voice TTS Init): ' . $response->body());
        }

        $taskId = $response->json('id');
        if (empty($taskId)) {
            throw new RuntimeException('No task ID returned for TTS generation: ' . $response->body());
        }

        $this->video->update(['external_task_id' => $taskId]);

        return $this->waitForTask($taskId, 'TTS task');
    }

    private function scriptForTts(string $script): string
    {
        $script = trim(preg_replace('/\s+/u', ' ', strip_tags($script)));
        $script = str_replace(['#', '*', '"'], '', $script);

        $words = preg_split('/\s+/u', $script, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $duration = (int) ($this->video->duration_seconds ?: 20);
        $maxWords = max(35, min(75, (int) ceil($duration * 2.6)));

        if (count($words) > $maxWords) {
            $script = implode(' ', array_slice($words, 0, $maxWords));
        }

        return trim($script);
    }

    private function extractAudioUrl(array $task): string
    {
        if (isset($task['results'][0]) && is_string($task['results'][0])) {
            return $task['results'][0];
        }

        $audioUrl = $task['results'][0]['url']
            ?? $task['results'][0]['file_url']
            ?? $task['result_data']['audio_url']
            ?? null;

        if ($audioUrl) {
            return $audioUrl;
        }

        $resultsStr = json_encode($task);
        if (
            preg_match('/"url"\s*:\s*"([^"]+)"/', $resultsStr, $matches)
            || preg_match('/"file_url"\s*:\s*"([^"]+)"/', $resultsStr, $matches)
            || preg_match('/"audio_url"\s*:\s*"([^"]+)"/', $resultsStr, $matches)
        ) {
            return $matches[1];
        }

        throw new RuntimeException('Could not extract audio URL from TTS task: ' . json_encode($task));
    }

    private function waitForTask(string $taskId, string $taskName): array
    {
        $startedAt = time();
        $timeout = 400;
        $pollInterval = 4;

        do {
            $response = Http::timeout(60)
                ->withToken(config('evolink.api_key'))
                ->get($this->endpoint("/tasks/{$taskId}"));

            if ($response->failed()) {
                throw new RuntimeException("Evolink task polling error ({$taskName}): " . $response->body());
            }

            $task = $response->json() ?? [];
            $status = $task['status'] ?? null;

            if ($status === 'completed') {
                return $task;
            }

            if ($status === 'failed') {
                $message = $task['error']['message'] ?? $task['message'] ?? "Evolink {$taskName} failed.";
                Log::warning("Evolink {$taskName} failed", [
                    'task_id' => $taskId,
                    'task' => $task,
                ]);

                throw new RuntimeException("{$message} Task ID: {$taskId}");
            }

            sleep($pollInterval);
        } while ((time() - $startedAt) < $timeout);

        throw new RuntimeException("Evolink {$taskName} timeout.");
    }

    private function configuredVoice(): string
    {
        return (string) config('evolink.tts_voice', 'male_deep');
    }

    private function endpoint(string $path): string
    {
        return rtrim(config('evolink.base_url', 'https://api.evolink.ai/v1'), '/') . '/' . ltrim($path, '/');
    }

    private function markCampaignFailedIfNeeded(): void
    {
        $campaign = $this->video->campaign;

        if ($campaign->videos()->where('status', 'failed')->exists()) {
            $campaign->update(['status' => 'failed']);
        }
    }
}

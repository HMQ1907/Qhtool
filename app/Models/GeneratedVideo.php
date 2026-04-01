<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedVideo extends Model
{
    protected $fillable = [
        'user_id',
        'source_image_url',
        'source_image_title',
        'animation',
        'prompt',
        'model',
        'duration',
        'aspect_ratio',
        'quality',
        'sound',
        'external_task_id',
        'output_video_path',
        'status',
        'error_message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_FAILED = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    public function markAsDone(string $outputPath): void
    {
        $this->update([
            'status' => self::STATUS_DONE,
            'output_video_path' => $outputPath,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }
}
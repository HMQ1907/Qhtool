<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCleanup extends Model
{
    protected $fillable = [
        'user_id',
        'input_video_path',
        'input_original_name',
        'left_pct',
        'top_pct',
        'width_pct',
        'height_pct',
        'status',
        'output_video_path',
        'error_message',
    ];

    protected $casts = [
        'left_pct' => 'float',
        'top_pct' => 'float',
        'width_pct' => 'float',
        'height_pct' => 'float',
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
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'error_message' => null,
        ]);
    }

    public function markAsDone(string $outputPath): void
    {
        $this->update([
            'status' => self::STATUS_DONE,
            'output_video_path' => $outputPath,
            'error_message' => null,
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

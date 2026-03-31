<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho 1 lần generate ảnh AI.
 *
 * Thiết kế mở rộng:
 * - Sau này thêm relationship với User khi có subscription
 * - Thêm relationship với GeneratedVideo khi có video feature
 */
class GeneratedImage extends Model
{
    protected $fillable = [
        'user_id',
        'input_image_path',
        'model_path',
        'background_path',
        'prompt',
        'output_image_path',
        'status',
        'error_message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== Constants ====================

    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE       = 'done';
    const STATUS_FAILED     = 'failed';

    // ==================== Scopes ====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    // ==================== Helpers ====================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark job đang xử lý
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    /**
     * Mark hoàn thành với đường dẫn ảnh output
     */
    public function markAsDone(string $outputPath): void
    {
        $this->update([
            'status'            => self::STATUS_DONE,
            'output_image_path' => $outputPath,
        ]);
    }

    /**
     * Mark thất bại với message lỗi
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status'        => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

return [
    'ffmpeg_bin' => env('FFMPEG_BIN', 'ffmpeg'),
    'ffprobe_bin' => env('FFPROBE_BIN', 'ffprobe'),
    'timeout' => (int) env('VIDEO_CLEANUP_TIMEOUT', 900),
    'default_region' => [
        'left_pct' => (float) env('VIDEO_CLEANUP_LEFT_PCT', 34),
        'top_pct' => (float) env('VIDEO_CLEANUP_TOP_PCT', 86),
        'width_pct' => (float) env('VIDEO_CLEANUP_WIDTH_PCT', 31),
        'height_pct' => (float) env('VIDEO_CLEANUP_HEIGHT_PCT', 9),
        'band' => (int) env('VIDEO_CLEANUP_BAND', 14),
    ],
];

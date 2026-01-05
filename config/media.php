<?php

declare(strict_types=1);

return [
    'max_upload_kilobytes' => (int) env('MEDIA_MAX_UPLOAD_KB', 102400), // 100 MB

    'max_video_duration_seconds' => (int) env('MEDIA_MAX_VIDEO_DURATION_SECONDS', 600), // 10 minutes

    'ffprobe_timeout_seconds' => (int) env('MEDIA_FFPROBE_TIMEOUT_SECONDS', 10),

    'ffmpeg_timeout_seconds' => (int) env('MEDIA_FFMPEG_TIMEOUT_SECONDS', 300),

    // H.264 quality: lower CRF = higher quality/bigger file.
    'h264_crf' => (int) env('MEDIA_H264_CRF', 23),

    'audio_bitrate' => (string) env('MEDIA_AUDIO_BITRATE', '128k'),

    // Used for deduping uploads and stable media URLs.
    'hash_algorithm' => (string) env('MEDIA_HASH_ALGORITHM', 'sha256'),
];

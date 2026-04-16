<?php

return [
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 30),

    'mysqldump_path' => env('MYSQLDUMP_PATH', 'mysqldump'),

    'include_public_files' => (bool) env('BACKUP_INCLUDE_PUBLIC_FILES', false),

    'public_files_max_size_mb' => (float) env('BACKUP_PUBLIC_FILES_MAX_SIZE_MB', 300),

    'excluded_extensions' => env('BACKUP_EXCLUDED_EXTENSIONS', 'mp4,mov,avi,mkv,zip,rar,7z,iso'),
];

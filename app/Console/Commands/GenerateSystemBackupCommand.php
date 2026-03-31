<?php

namespace App\Console\Commands;

use App\Services\SystemBackup\SystemBackupService;
use Illuminate\Console\Command;
use Throwable;

class GenerateSystemBackupCommand extends Command
{
    protected $signature = 'backup:run {type=scheduled} {--cleanup-days=0}';
    protected $description = 'Generate a system backup and save a history record';

    public function handle(SystemBackupService $systemBackupService): int
    {
        try {
            $type = (string) $this->argument('type');

            if (! in_array($type, ['manual', 'scheduled'], true)) {
                $type = 'scheduled';
            }

            $backup = $systemBackupService->create($type);

            $cleanupDays = (int) $this->option('cleanup-days');

            if ($cleanupDays > 0) {
                $deleted = $systemBackupService->cleanupOldBackups($cleanupDays);
                $this->info("Old backups cleaned: {$deleted}");
            }

            $this->info('Backup completed successfully. ID: ' . $backup->id);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}

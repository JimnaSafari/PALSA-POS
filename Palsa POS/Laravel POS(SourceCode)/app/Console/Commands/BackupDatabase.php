<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--type=daily : Type of backup (daily, weekly, monthly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$type}_{$timestamp}.sql";

        $this->info("Starting {$type} database backup...");

        try {
            // Get database connection details
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Create backup directory if it doesn't exist
            $backupPath = storage_path('backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filePath = $backupPath . '/' . $filename;

            // Use mysqldump command
            $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$filePath}";

            $returnVar = null;
            $output = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('mysqldump command failed');
            }

            // Compress the backup
            $compressedFile = $filePath . '.gz';
            $compressionCommand = "gzip {$filePath}";
            exec($compressionCommand, $output, $returnVar);

            if ($returnVar === 0) {
                $filename .= '.gz';
                $filePath = $compressedFile;
            }

            // Calculate file size
            $fileSize = filesize($filePath);
            $fileSizeFormatted = $this->formatBytes($fileSize);

            $this->info("✅ Database backup completed successfully!");
            $this->info("📁 File: {$filename}");
            $this->info("📊 Size: {$fileSizeFormatted}");
            $this->info("📍 Location: storage/backups/{$filename}");

            // Clean up old backups (keep last 30 daily, 12 weekly, 12 monthly)
            $this->cleanupOldBackups($type);

            // Log backup completion
            \Log::info("Database backup completed: {$filename} ({$fileSizeFormatted})");

        } catch (\Exception $e) {
            $this->error("❌ Database backup failed: " . $e->getMessage());
            \Log::error("Database backup failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function cleanupOldBackups($type)
    {
        $backupPath = storage_path('backups');
        $pattern = $backupPath . "/backup_{$type}_*.sql*";

        $files = glob($pattern);
        if (empty($files)) {
            return;
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Keep limits based on type
        $keepCount = match($type) {
            'daily' => 30,
            'weekly' => 12,
            'monthly' => 12,
            default => 7
        };

        if (count($files) > $keepCount) {
            $filesToDelete = array_slice($files, $keepCount);
            foreach ($filesToDelete as $file) {
                unlink($file);
                $this->info("🗑️  Deleted old backup: " . basename($file));
            }
        }
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

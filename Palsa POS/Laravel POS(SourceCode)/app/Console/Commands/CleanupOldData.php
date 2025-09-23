<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupOldData extends Command
{
    protected $signature = 'pos:cleanup-old-data {--days=90 : Number of days to keep data}';
    protected $description = 'Clean up old data to maintain database performance';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning up data older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");

        if (!$this->confirm('This will permanently delete old data. Are you sure?')) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($cutoffDate) {
            // Clean up old sessions
            $deletedSessions = DB::table('sessions')
                ->where('last_activity', '<', $cutoffDate->timestamp)
                ->delete();
            
            $this->info("Deleted {$deletedSessions} old sessions");

            // Clean up old cache entries
            $deletedCache = DB::table('cache')
                ->where('expiration', '<', now()->timestamp)
                ->delete();
            
            $this->info("Deleted {$deletedCache} expired cache entries");

            // Clean up old failed jobs
            $deletedJobs = DB::table('failed_jobs')
                ->where('failed_at', '<', $cutoffDate)
                ->delete();
            
            $this->info("Deleted {$deletedJobs} old failed jobs");

            // Archive old completed orders (don't delete, just mark as archived)
            $archivedOrders = Order::where('status', Order::STATUS_DELIVERED)
                ->where('created_at', '<', $cutoffDate)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);
            
            $this->info("Archived {$archivedOrders} old completed orders");
        });

        $this->info('Data cleanup completed successfully.');
        return self::SUCCESS;
    }
}
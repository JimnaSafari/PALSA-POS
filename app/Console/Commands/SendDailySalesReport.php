<?php

namespace App\Console\Commands;

use App\Services\DashboardService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDailySalesReport extends Command
{
    protected $signature = 'pos:daily-sales-report {--date= : Date for the report (Y-m-d format)}';
    protected $description = 'Send daily sales report to administrators';

    public function handle(DashboardService $dashboardService, NotificationService $notificationService): int
    {
        $date = $this->option('date') 
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : Carbon::yesterday();

        $this->info("Generating daily sales report for {$date->format('Y-m-d')}...");

        $dashboardData = $dashboardService->getAdminDashboardData();
        
        $reportData = [
            'date' => $date->format('Y-m-d'),
            'sales' => $dashboardData['sales'],
            'orders' => $dashboardData['orders'],
            'products' => $dashboardData['products'],
            'customers' => $dashboardData['customers']
        ];

        $notificationService->sendDailySalesReport($reportData);

        $this->info('Daily sales report sent successfully.');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Today Sales', '$' . number_format($reportData['sales']['today'], 2)],
                ['Today Orders', $reportData['orders']['today_total']],
                ['Pending Orders', $reportData['orders']['pending']],
                ['Low Stock Products', $reportData['products']['low_stock']]
            ]
        );

        return self::SUCCESS;
    }
}
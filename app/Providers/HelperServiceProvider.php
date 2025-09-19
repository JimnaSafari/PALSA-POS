<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register helper files
        $this->loadHelpers();
    }

    public function boot(): void
    {
        //
    }

    protected function loadHelpers(): void
    {
        $helperFiles = [
            app_path('Helpers/CurrencyHelper.php'),
        ];

        foreach ($helperFiles as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}
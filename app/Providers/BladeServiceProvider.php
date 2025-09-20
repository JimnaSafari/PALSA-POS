<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

class BladeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Currency formatting directives
        Blade::directive('kes', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::formatKESSymbol($expression); ?>";
        });

        Blade::directive('kesShort', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::formatKESShort($expression); ?>";
        });

        Blade::directive('mpesa', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::formatMpesa($expression); ?>";
        });

        // Phone number formatting for Kenya
        Blade::directive('kenyanPhone', function ($expression) {
            return "<?php echo preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1 $2 $3', $expression); ?>";
        });

        // Date formatting for Kenya (EAT timezone)
        Blade::directive('kenyanDate', function ($expression) {
            return "<?php echo $expression->setTimezone('Africa/Nairobi')->format('d/m/Y H:i'); ?>";
        });

        // M-Pesa transaction reference formatting
        Blade::directive('mpesaRef', function ($expression) {
            return "<?php echo strtoupper($expression); ?>";
        });
    }
}
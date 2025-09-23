<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function check()
    {
        $checks = [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'services' => []
        ];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['services']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $checks['services']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
            $checks['status'] = 'error';
        }

        // Cache check
        try {
            Cache::store()->put('health_check', 'ok', 10);
            $cacheValue = Cache::store()->get('health_check');
            if ($cacheValue === 'ok') {
                $checks['services']['cache'] = [
                    'status' => 'ok',
                    'message' => 'Cache is working'
                ];
            } else {
                throw new \Exception('Cache read/write failed');
            }
        } catch (\Exception $e) {
            $checks['services']['cache'] = [
                'status' => 'error',
                'message' => 'Cache failed: ' . $e->getMessage()
            ];
            $checks['status'] = 'error';
        }

        // Storage check
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::put($testFile, 'health check test');
            Storage::delete($testFile);
            $checks['services']['storage'] = [
                'status' => 'ok',
                'message' => 'File storage is working'
            ];
        } catch (\Exception $e) {
            $checks['services']['storage'] = [
                'status' => 'error',
                'message' => 'Storage failed: ' . $e->getMessage()
            ];
            $checks['status'] = 'error';
        }

        // System info
        $checks['system'] = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'memory_usage' => $this->formatBytes(memory_get_peak_usage(true)),
            'uptime' => $this->getSystemUptime()
        ];

        $statusCode = $checks['status'] === 'ok' ? 200 : 503;

        return response()->json($checks, $statusCode);
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

    private function getSystemUptime()
    {
        if (function_exists('shell_exec')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $uptime = shell_exec('net stats srv');
                if ($uptime) {
                    // Parse Windows uptime
                    return 'Windows system uptime available';
                }
            } else {
                $uptime = shell_exec('uptime -p');
                if ($uptime) {
                    return trim($uptime);
                }
            }
        }
        return 'Uptime information not available';
    }
}

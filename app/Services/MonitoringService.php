<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MonitoringService
{
    /**
     * Log application metrics
     */
    public static function logMetrics(array $metrics): void
    {
        Log::channel('metrics')->info('Application Metrics', $metrics);
    }

    /**
     * Check database health
     */
    public static function checkDatabaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $start) * 1000;

            return [
                'status' => 'healthy',
                'response_time_ms' => round($responseTime, 2),
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Check cache health
     */
    public static function checkCacheHealth(): array
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';
            
            $start = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);
            $responseTime = (microtime(true) - $start) * 1000;

            if ($retrieved === $testValue) {
                return [
                    'status' => 'healthy',
                    'response_time_ms' => round($responseTime, 2),
                    'timestamp' => now()->toISOString()
                ];
            } else {
                return [
                    'status' => 'unhealthy',
                    'error' => 'Cache value mismatch',
                    'timestamp' => now()->toISOString()
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Check external API health
     */
    public static function checkExternalApiHealth(): array
    {
        $results = [];

        // Check Midtrans API
        try {
            $start = microtime(true);
            $response = Http::timeout(5)->get('https://api.midtrans.com/v2/ping');
            $responseTime = (microtime(true) - $start) * 1000;

            $results['midtrans'] = [
                'status' => $response->successful() ? 'healthy' : 'unhealthy',
                'response_time_ms' => round($responseTime, 2),
                'http_status' => $response->status(),
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            $results['midtrans'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }

        // Check Diggie API
        try {
            $start = microtime(true);
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('diggie.api_key'),
                ])
                ->get(config('diggie.api_url') . '/ping');
            $responseTime = (microtime(true) - $start) * 1000;

            $results['diggie'] = [
                'status' => $response->successful() ? 'healthy' : 'unhealthy',
                'response_time_ms' => round($responseTime, 2),
                'http_status' => $response->status(),
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            $results['diggie'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }

        return $results;
    }

    /**
     * Get system metrics
     */
    public static function getSystemMetrics(): array
    {
        return [
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'disk_usage' => [
                'free' => disk_free_space('/'),
                'total' => disk_total_space('/')
            ],
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Log performance metrics
     */
    public static function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $metrics = [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'memory_usage' => memory_get_usage(true),
            'timestamp' => now()->toISOString()
        ];

        if (!empty($context)) {
            $metrics['context'] = $context;
        }

        Log::channel('performance')->info('Performance Metric', $metrics);
    }

    /**
     * Log error with context
     */
    public static function logError(\Throwable $exception, array $context = []): void
    {
        $errorData = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toISOString()
        ];

        if (!empty($context)) {
            $errorData['context'] = $context;
        }

        Log::error('Application Error', $errorData);
    }

    /**
     * Send alert to external monitoring service
     */
    public static function sendAlert(string $level, string $message, array $context = []): void
    {
        $alertData = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'server' => gethostname()
        ];

        // Log locally
        Log::channel('alerts')->{$level}($message, $alertData);

        // Send to external service (Slack, Discord, etc.)
        if (config('services.slack.webhook_url')) {
            try {
                Http::timeout(5)->post(config('services.slack.webhook_url'), [
                    'text' => "🚨 *{$level}*: {$message}",
                    'attachments' => [
                        [
                            'color' => $level === 'critical' ? 'danger' : 'warning',
                            'fields' => [
                                [
                                    'title' => 'Environment',
                                    'value' => app()->environment(),
                                    'short' => true
                                ],
                                [
                                    'title' => 'Server',
                                    'value' => gethostname(),
                                    'short' => true
                                ],
                                [
                                    'title' => 'Timestamp',
                                    'value' => now()->toDateTimeString(),
                                    'short' => true
                                ]
                            ]
                        ]
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send alert to Slack', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Get comprehensive health check
     */
    public static function getHealthCheck(): array
    {
        return [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => [
                'database' => self::checkDatabaseHealth(),
                'cache' => self::checkCacheHealth(),
                'external_apis' => self::checkExternalApiHealth(),
                'system' => self::getSystemMetrics()
            ]
        ];
    }
}

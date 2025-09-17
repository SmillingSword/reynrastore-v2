<?php

namespace App\Http\Controllers;

use App\Services\MonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    /**
     * Basic health check endpoint
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'service' => 'Reynra Store',
            'version' => config('app.version', '1.0.0')
        ]);
    }

    /**
     * Comprehensive health check
     */
    public function detailed(): JsonResponse
    {
        $healthCheck = MonitoringService::getHealthCheck();
        
        // Determine overall status
        $overallStatus = 'healthy';
        foreach ($healthCheck['checks'] as $check) {
            if (is_array($check)) {
                if (isset($check['status']) && $check['status'] !== 'healthy') {
                    $overallStatus = 'unhealthy';
                    break;
                }
                // Check nested arrays (like external_apis)
                foreach ($check as $subCheck) {
                    if (is_array($subCheck) && isset($subCheck['status']) && $subCheck['status'] !== 'healthy') {
                        $overallStatus = 'unhealthy';
                        break 2;
                    }
                }
            }
        }

        $healthCheck['status'] = $overallStatus;

        $statusCode = $overallStatus === 'healthy' ? 200 : 503;

        return response()->json($healthCheck, $statusCode);
    }

    /**
     * Database health check
     */
    public function database(): JsonResponse
    {
        $check = MonitoringService::checkDatabaseHealth();
        $statusCode = $check['status'] === 'healthy' ? 200 : 503;

        return response()->json($check, $statusCode);
    }

    /**
     * Cache health check
     */
    public function cache(): JsonResponse
    {
        $check = MonitoringService::checkCacheHealth();
        $statusCode = $check['status'] === 'healthy' ? 200 : 503;

        return response()->json($check, $statusCode);
    }

    /**
     * External APIs health check
     */
    public function externalApis(): JsonResponse
    {
        $checks = MonitoringService::checkExternalApiHealth();
        
        $overallStatus = 'healthy';
        foreach ($checks as $check) {
            if ($check['status'] !== 'healthy') {
                $overallStatus = 'unhealthy';
                break;
            }
        }

        $statusCode = $overallStatus === 'healthy' ? 200 : 503;

        return response()->json([
            'status' => $overallStatus,
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }

    /**
     * System metrics
     */
    public function metrics(): JsonResponse
    {
        $metrics = MonitoringService::getSystemMetrics();

        return response()->json($metrics);
    }
}

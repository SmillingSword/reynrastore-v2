<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\EnhancedSecurityService;
use Illuminate\Support\Facades\Log;

class PaymentSecurityMiddleware
{
    protected $securityService;

    public function __construct(EnhancedSecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get client IP and user agent
        $clientIp = $request->ip();
        $userAgent = $request->userAgent();
        
        // Log payment request attempt
        $this->securityService->logQrisSecurityEvent('payment_request_attempt', [
            'ip' => $clientIp,
            'user_agent' => $userAgent,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        // 1. Rate limiting check
        $rateLimitKey = 'payment_' . $clientIp;
        if (!$this->securityService->checkRateLimit($rateLimitKey, 10, 5)) {
            $this->securityService->logQrisSecurityEvent('payment_rate_limit_exceeded', [
                'ip' => $clientIp,
                'user_agent' => $userAgent,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Too many payment requests. Please try again later.',
                'error_code' => 'RATE_LIMIT_EXCEEDED'
            ], 429);
        }

        // 2. Check for suspicious location
        if ($this->securityService->checkSuspiciousLocation($clientIp)) {
            $this->securityService->logQrisSecurityEvent('suspicious_location_detected', [
                'ip' => $clientIp,
                'user_agent' => $userAgent,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment request from suspicious location blocked.',
                'error_code' => 'SUSPICIOUS_LOCATION'
            ], 403);
        }

        // 3. Validate IP address format
        if (!$this->securityService->validateIPAddress($clientIp)) {
            $this->securityService->logQrisSecurityEvent('invalid_ip_address', [
                'ip' => $clientIp,
                'user_agent' => $userAgent,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid request source.',
                'error_code' => 'INVALID_IP'
            ], 400);
        }

        // 4. Detect suspicious activity in request data
        $requestData = $request->all();
        $suspiciousActivity = $this->securityService->detectSuspiciousActivity($requestData);
        
        if (!empty($suspiciousActivity)) {
            $this->securityService->logQrisSecurityEvent('suspicious_activity_detected', [
                'ip' => $clientIp,
                'user_agent' => $userAgent,
                'suspicious_patterns' => $suspiciousActivity,
                'request_data' => $requestData,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Suspicious activity detected in request.',
                'error_code' => 'SUSPICIOUS_ACTIVITY'
            ], 400);
        }

        // 5. For QRIS payment requests, perform additional fraud detection
        if ($request->is('*/payment/qris*') || $request->has('payment_method') && $request->payment_method === 'qris') {
            $fraudIndicators = $this->securityService->detectQrisPaymentFraud($requestData);
            
            if (!empty($fraudIndicators)) {
                $this->securityService->logQrisSecurityEvent('qris_fraud_indicators_detected', [
                    'ip' => $clientIp,
                    'user_agent' => $userAgent,
                    'fraud_indicators' => $fraudIndicators,
                    'request_data' => $requestData,
                ]);
                
                // Don't block immediately, but flag for manual review
                $request->attributes->set('fraud_indicators', $fraudIndicators);
                $request->attributes->set('requires_manual_review', true);
            }
        }

        // 6. Validate payment session token if present
        if ($request->has('payment_token') && $request->has('order_number')) {
            $isValidToken = $this->securityService->validatePaymentSessionToken(
                $request->payment_token,
                $request->order_number
            );
            
            if (!$isValidToken) {
                $this->securityService->logQrisSecurityEvent('invalid_payment_session_token', [
                    'ip' => $clientIp,
                    'user_agent' => $userAgent,
                    'order_number' => $request->order_number,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired payment session.',
                    'error_code' => 'INVALID_SESSION_TOKEN'
                ], 401);
            }
        }

        // 7. Check for required security headers
        $requiredHeaders = ['User-Agent', 'Accept'];
        foreach ($requiredHeaders as $header) {
            if (!$request->hasHeader($header)) {
                $this->securityService->logQrisSecurityEvent('missing_required_header', [
                    'ip' => $clientIp,
                    'missing_header' => $header,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request format.',
                    'error_code' => 'MISSING_HEADERS'
                ], 400);
            }
        }

        // 8. Add security context to request
        $request->attributes->set('security_context', [
            'client_ip' => $clientIp,
            'user_agent' => $userAgent,
            'session_id' => session()->getId(),
            'request_timestamp' => now(),
            'security_validated' => true,
        ]);

        // Continue with the request
        $response = $next($request);

        // Log successful security validation
        $this->securityService->logQrisSecurityEvent('payment_security_validation_passed', [
            'ip' => $clientIp,
            'user_agent' => $userAgent,
            'url' => $request->fullUrl(),
            'response_status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}

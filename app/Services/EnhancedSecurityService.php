<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EnhancedSecurityService
{
    /**
     * Generate secure order signature
     */
    public function generateOrderSignature(array $orderData): string
    {
        $payload = json_encode($orderData) . now()->timestamp . config('app.key');
        return hash_hmac('sha256', $payload, config('app.key'));
    }

    /**
     * Validate order signature
     */
    public function validateOrderSignature(string $signature, array $orderData): bool
    {
        $expectedSignature = $this->generateOrderSignature($orderData);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate unique order token
     */
    public function generateOrderToken(): string
    {
        return Str::random(64) . time();
    }

    /**
     * Rate limiting check
     */
    public function checkRateLimit(string $identifier, int $maxAttempts = 5, int $decayMinutes = 1): bool
    {
        $key = 'rate_limit_' . $identifier;
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return false;
        }
        
        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        return true;
    }

    /**
     * Detect suspicious activity
     */
    public function detectSuspiciousActivity(array $requestData): array
    {
        $suspicious = [];
        
        // Check for SQL injection patterns
        $sqlPatterns = ['/union\s+select/i', '/insert\s+into/i', '/drop\s+table/i', '/delete\s+from/i'];
        foreach ($requestData as $value) {
            if (!is_string($value)) continue;
            foreach ($sqlPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $suspicious[] = 'Potential SQL injection detected';
                    break;
                }
            }
        }
        
        // Check for XSS patterns
        $xssPatterns = ['/<script/i', '/javascript:/i', '/on\w+\s*=/i'];
        foreach ($requestData as $value) {
            if (!is_string($value)) continue;
            foreach ($xssPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $suspicious[] = 'Potential XSS detected';
                    break;
                }
            }
        }
        
        return $suspicious;
    }

    /**
     * Encrypt sensitive data
     */
    public function encryptSensitiveData(string $data): string
    {
        return openssl_encrypt($data, 'AES-256-CBC', config('app.key'), 0, substr(config('app.key'), 0, 16));
    }

    /**
     * Decrypt sensitive data
     */
    public function decryptSensitiveData(string $encryptedData): string
    {
        return openssl_decrypt($encryptedData, 'AES-256-CBC', config('app.key'), 0, substr(config('app.key'), 0, 16));
    }

    /**
     * Generate secure payment reference
     */
    public function generatePaymentReference(string $orderNumber): string
    {
        return hash('sha256', $orderNumber . time() . config('app.key'));
    }

    /**
     * Validate IP address
     */
    public function validateIPAddress(string $ip): bool
    {
        // Check if IP is from Indonesia (example)
        $allowedCountries = ['ID']; // Indonesia
        // In production, use IP geolocation service
        
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        Log::channel('security')->info($event, [
            'timestamp' => now(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => $data
        ]);
    }

    /**
     * Generate QRIS payment signature for enhanced security
     */
    public function generateQrisPaymentSignature(array $paymentData): string
    {
        $payload = [
            'order_id' => $paymentData['order_id'],
            'amount' => $paymentData['amount'],
            'timestamp' => now()->timestamp,
            'merchant_id' => config('qris.merchant_id', 'default'),
        ];
        
        $signatureString = implode('|', $payload) . '|' . config('app.key');
        return hash_hmac('sha256', $signatureString, config('app.key'));
    }

    /**
     * Validate QRIS payment signature
     */
    public function validateQrisPaymentSignature(string $signature, array $paymentData): bool
    {
        $expectedSignature = $this->generateQrisPaymentSignature($paymentData);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate secure QRIS transaction ID
     */
    public function generateQrisTransactionId(string $orderNumber): string
    {
        $prefix = 'QRIS';
        $timestamp = now()->format('YmdHis');
        $random = Str::upper(Str::random(6));
        $hash = substr(hash('sha256', $orderNumber . config('app.key')), 0, 8);
        
        return $prefix . $timestamp . $random . $hash;
    }

    /**
     * Validate QRIS webhook signature from Midtrans
     */
    public function validateQrisWebhookSignature(array $webhookData, string $receivedSignature): bool
    {
        $orderId = $webhookData['order_id'] ?? '';
        $statusCode = $webhookData['status_code'] ?? '';
        $grossAmount = $webhookData['gross_amount'] ?? '';
        $serverKey = config('midtrans.server_key');
        
        $signatureString = $orderId . $statusCode . $grossAmount . $serverKey;
        $expectedSignature = hash('sha512', $signatureString);
        
        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Detect QRIS payment fraud patterns
     */
    public function detectQrisPaymentFraud(array $paymentData): array
    {
        $fraudIndicators = [];
        
        // Check for unusual amount patterns
        $amount = $paymentData['amount'] ?? 0;
        if ($amount > 10000000) { // > 10 million IDR
            $fraudIndicators[] = 'High amount transaction detected';
        }
        
        // Check for rapid successive payments from same IP
        $ip = request()->ip();
        $recentPayments = Cache::get("qris_payments_{$ip}", []);
        $recentCount = count(array_filter($recentPayments, function($timestamp) {
            return $timestamp > (time() - 300); // Last 5 minutes
        }));
        
        if ($recentCount > 5) {
            $fraudIndicators[] = 'Rapid successive payments detected';
        }
        
        // Update payment tracking
        $recentPayments[] = time();
        Cache::put("qris_payments_{$ip}", array_slice($recentPayments, -10), now()->addHour());
        
        // Check for suspicious user agent patterns
        $userAgent = request()->userAgent();
        $suspiciousAgents = ['bot', 'crawler', 'spider', 'scraper'];
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $fraudIndicators[] = 'Suspicious user agent detected';
                break;
            }
        }
        
        return $fraudIndicators;
    }

    /**
     * Generate payment session token for frontend security
     */
    public function generatePaymentSessionToken(string $orderNumber): string
    {
        $payload = [
            'order_number' => $orderNumber,
            'timestamp' => now()->timestamp,
            'random' => Str::random(16),
        ];
        
        $token = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', $token, config('app.key'));
        
        return $token . '.' . $signature;
    }

    /**
     * Validate payment session token
     */
    public function validatePaymentSessionToken(string $token, string $orderNumber): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return false;
        }
        
        [$payload, $signature] = $parts;
        $expectedSignature = hash_hmac('sha256', $payload, config('app.key'));
        
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }
        
        $data = json_decode(base64_decode($payload), true);
        if (!$data || $data['order_number'] !== $orderNumber) {
            return false;
        }
        
        // Check if token is not expired (1 hour)
        $tokenAge = now()->timestamp - $data['timestamp'];
        return $tokenAge <= 3600;
    }

    /**
     * Log QRIS security event with enhanced details
     */
    public function logQrisSecurityEvent(string $event, array $data = []): void
    {
        $securityData = [
            'event_type' => 'qris_security',
            'timestamp' => now(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'request_id' => Str::uuid(),
            'data' => $data,
        ];
        
        Log::channel('security')->warning($event, $securityData);
        
        // Also log to dedicated QRIS security log if configured
        if (config('logging.channels.qris_security')) {
            Log::channel('qris_security')->warning($event, $securityData);
        }
    }

    /**
     * Validate payment amount against order
     */
    public function validatePaymentAmount(float $requestedAmount, float $orderAmount): bool
    {
        // Allow small variance for rounding (1 IDR)
        $variance = abs($requestedAmount - $orderAmount);
        return $variance <= 1;
    }

    /**
     * Generate anti-tampering hash for payment data
     */
    public function generatePaymentDataHash(array $paymentData): string
    {
        ksort($paymentData); // Sort keys for consistent hashing
        $dataString = json_encode($paymentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return hash('sha256', $dataString . config('app.key'));
    }

    /**
     * Validate payment data integrity
     */
    public function validatePaymentDataIntegrity(array $paymentData, string $expectedHash): bool
    {
        $actualHash = $this->generatePaymentDataHash($paymentData);
        return hash_equals($expectedHash, $actualHash);
    }

    /**
     * Check if IP is from suspicious location
     */
    public function checkSuspiciousLocation(string $ip): bool
    {
        // In production, integrate with IP geolocation service
        // For now, implement basic checks
        
        // Block known malicious IP ranges (example)
        $blockedRanges = [
            '10.0.0.0/8',     // Private networks shouldn't access payment
            '172.16.0.0/12',  // Private networks
            '192.168.0.0/16', // Private networks
        ];
        
        foreach ($blockedRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if IP is in given range
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }
        
        [$subnet, $mask] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);
        
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}

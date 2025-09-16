<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SecurityService
{
    /**
     * Check if request passes honeypot validation
     */
    public static function validateHoneypot(Request $request): bool
    {
        // Check for common honeypot fields
        $honeypotFields = ['website', 'url', 'homepage', 'phone', 'fax'];
        
        foreach ($honeypotFields as $field) {
            if ($request->filled($field)) {
                Log::warning('Honeypot triggered', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'field' => $field,
                    'value' => $request->input($field)
                ]);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Generate request fingerprint for bot detection
     */
    public static function generateFingerprint(Request $request): string
    {
        $components = [
            $request->ip(),
            $request->userAgent() ?? 'unknown',
            $request->header('Accept-Language', 'unknown'),
            $request->header('Accept-Encoding', 'unknown'),
            $request->header('Accept', 'unknown'),
        ];
        
        return hash('sha256', implode('|', $components));
    }
    
    /**
     * Check for suspicious request patterns
     */
    public static function isSuspiciousRequest(Request $request): bool
    {
        $userAgent = $request->userAgent() ?? '';
        $ip = $request->ip();
        
        // Check for bot patterns in user agent
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
            'python', 'requests', 'http', 'automated', 'script'
        ];
        
        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        // Check for missing common headers
        if (empty($userAgent) || 
            !$request->hasHeader('Accept') || 
            !$request->hasHeader('Accept-Language')) {
            return true;
        }
        
        // Check request timing patterns (very fast repeated requests)
        $fingerprint = self::generateFingerprint($request);
        $cacheKey = "request_timing:{$fingerprint}";
        $lastRequest = Cache::get($cacheKey);
        
        if ($lastRequest && (microtime(true) - $lastRequest) < 1.0) {
            return true; // Less than 1 second between requests
        }
        
        Cache::put($cacheKey, microtime(true), 300); // Store for 5 minutes
        
        return false;
    }
    
    /**
     * Implement progressive rate limiting
     */
    public static function checkProgressiveRateLimit(Request $request, string $action = 'general'): array
    {
        $ip = $request->ip();
        $cacheKey = "rate_limit:{$action}:{$ip}";
        $attempts = Cache::get($cacheKey, 0);
        
        // Progressive delays: 0, 2, 5, 10, 30, 60 seconds
        $delays = [0, 2, 5, 10, 30, 60];
        $maxAttempts = count($delays) - 1;
        
        if ($attempts >= $maxAttempts) {
            $delay = $delays[$maxAttempts];
        } else {
            $delay = $delays[$attempts];
        }
        
        return [
            'allowed' => $attempts < $maxAttempts,
            'attempts' => $attempts,
            'delay' => $delay,
            'retry_after' => now()->addSeconds($delay),
        ];
    }
    
    /**
     * Record a failed attempt for progressive rate limiting
     */
    public static function recordFailedAttempt(Request $request, string $action = 'general'): void
    {
        $ip = $request->ip();
        $cacheKey = "rate_limit:{$action}:{$ip}";
        $attempts = Cache::get($cacheKey, 0) + 1;
        
        // Store attempts for increasing durations based on attempt count
        $duration = min($attempts * 60, 3600); // Max 1 hour
        Cache::put($cacheKey, $attempts, $duration);
        
        Log::info('Security: Rate limit attempt recorded', [
            'ip' => $ip,
            'action' => $action,
            'attempts' => $attempts,
            'duration' => $duration
        ]);
    }
    
    /**
     * Clear rate limit for successful action
     */
    public static function clearRateLimit(Request $request, string $action = 'general'): void
    {
        $ip = $request->ip();
        $cacheKey = "rate_limit:{$action}:{$ip}";
        Cache::forget($cacheKey);
    }
    
    /**
     * Comprehensive security check
     */
    public static function performSecurityChecks(Request $request, string $action = 'magic_link'): array
    {
        $issues = [];
        
        // Check honeypot
        if (!self::validateHoneypot($request)) {
            $issues[] = 'honeypot_triggered';
        }
        
        // Check for suspicious patterns
        if (self::isSuspiciousRequest($request)) {
            $issues[] = 'suspicious_request';
        }
        
        // Check progressive rate limit
        $rateLimit = self::checkProgressiveRateLimit($request, $action);
        if (!$rateLimit['allowed']) {
            $issues[] = 'rate_limited';
        }
        
        $passed = empty($issues);
        
        if (!$passed) {
            Log::warning('Security checks failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'issues' => $issues,
                'action' => $action
            ]);
        }
        
        return [
            'passed' => $passed,
            'issues' => $issues,
            'rate_limit' => $rateLimit ?? null
        ];
    }
}
<?php

use Tests\TestCase;
use App\Services\MathCaptchaService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

uses(TestCase::class);

describe('MathCaptchaService', function () {
    beforeEach(function () {
        Session::flush();
    });

    test('generates math captcha with question and key', function () {
        $captcha = MathCaptchaService::generate();
        
        expect($captcha)->toHaveKeys(['question', 'key']);
        expect($captcha['question'])->toBeString();
        expect($captcha['key'])->toBeString();
        expect($captcha['key'])->toStartWith('math_captcha_');
        
        // Check that session data was stored
        $sessionData = Session::get($captcha['key']);
        expect($sessionData)->toHaveKeys(['answer', 'expires_at', 'attempts']);
        expect($sessionData['attempts'])->toBe(0);
    });

    test('verifies correct captcha answer', function () {
        $captcha = MathCaptchaService::generate();
        
        // Extract the expected answer from the session
        $sessionData = Session::get($captcha['key']);
        $correctAnswer = $sessionData['answer'];
        
        $result = MathCaptchaService::verify($captcha['key'], (string) $correctAnswer);
        
        expect($result)->toBeTrue();
        // Session should be cleared after successful verification
        expect(Session::has($captcha['key']))->toBeFalse();
    });

    test('rejects incorrect captcha answer', function () {
        $captcha = MathCaptchaService::generate();
        
        $result = MathCaptchaService::verify($captcha['key'], '999999');
        
        expect($result)->toBeFalse();
        
        // Session should still exist with incremented attempts
        $sessionData = Session::get($captcha['key']);
        expect($sessionData['attempts'])->toBe(1);
    });

    test('rejects expired captcha', function () {
        $captcha = MathCaptchaService::generate();
        
        // Manually expire the captcha
        $sessionData = Session::get($captcha['key']);
        $sessionData['expires_at'] = now()->subMinutes(1);
        Session::put($captcha['key'], $sessionData);
        
        $result = MathCaptchaService::verify($captcha['key'], '5');
        
        expect($result)->toBeFalse();
        expect(Session::has($captcha['key']))->toBeFalse();
    });

    test('limits captcha attempts', function () {
        $captcha = MathCaptchaService::generate();
        
        // Make 3 incorrect attempts
        for ($i = 0; $i < 3; $i++) {
            MathCaptchaService::verify($captcha['key'], '999999');
        }
        
        // Fourth attempt should fail and clear session
        $result = MathCaptchaService::verify($captcha['key'], '999999');
        
        expect($result)->toBeFalse();
        expect(Session::has($captcha['key']))->toBeFalse();
    });
});

describe('SecurityService', function () {
    beforeEach(function () {
        Cache::flush();
    });

    test('validates honeypot correctly', function () {
        $request = Request::create('/test', 'POST', [
            'email' => 'test@example.com'
        ]);
        
        expect(SecurityService::validateHoneypot($request))->toBeTrue();
        
        $requestWithHoneypot = Request::create('/test', 'POST', [
            'email' => 'test@example.com',
            'website' => 'http://spam.com' // This should trigger honeypot
        ]);
        
        expect(SecurityService::validateHoneypot($requestWithHoneypot))->toBeFalse();
    });

    test('generates request fingerprint', function () {
        $request = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml',
        ]);
        
        $fingerprint = SecurityService::generateFingerprint($request);
        
        expect($fingerprint)->toBeString();
        expect(strlen($fingerprint))->toBe(64); // SHA256 hash length
    });

    test('detects suspicious requests', function () {
        // Normal request
        $normalRequest = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml',
        ]);
        
        expect(SecurityService::isSuspiciousRequest($normalRequest))->toBeFalse();
        
        // Bot request
        $botRequest = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'curl/7.68.0',
        ]);
        
        expect(SecurityService::isSuspiciousRequest($botRequest))->toBeTrue();
    });

    test('implements progressive rate limiting', function () {
        $request = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.100'
        ]);
        
        // First check should be allowed
        $result = SecurityService::checkProgressiveRateLimit($request, 'test_action');
        expect($result['allowed'])->toBeTrue();
        expect($result['delay'])->toBe(0);
        
        // Record some failed attempts
        for ($i = 0; $i < 3; $i++) {
            SecurityService::recordFailedAttempt($request, 'test_action');
        }
        
        // Check should now have delay
        $result = SecurityService::checkProgressiveRateLimit($request, 'test_action');
        expect($result['delay'])->toBeGreaterThan(0);
    });

    test('clears rate limit on success', function () {
        $request = Request::create('/test', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.101'
        ]);
        
        // Record failed attempts
        SecurityService::recordFailedAttempt($request, 'test_action');
        SecurityService::recordFailedAttempt($request, 'test_action');
        
        // Verify attempts are recorded
        $result = SecurityService::checkProgressiveRateLimit($request, 'test_action');
        expect($result['attempts'])->toBeGreaterThan(0);
        
        // Clear rate limit
        SecurityService::clearRateLimit($request, 'test_action');
        
        // Check should be back to normal
        $result = SecurityService::checkProgressiveRateLimit($request, 'test_action');
        expect($result['attempts'])->toBe(0);
        expect($result['delay'])->toBe(0);
    });

    test('performs comprehensive security checks', function () {
        // Clean request should pass
        $cleanRequest = Request::create('/test', 'POST', [
            'email' => 'test@example.com'
        ], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml',
        ]);
        
        $result = SecurityService::performSecurityChecks($cleanRequest, 'test');
        expect($result['passed'])->toBeTrue();
        expect($result['issues'])->toBeEmpty();
        
        // Request with honeypot should fail
        $honeypotRequest = Request::create('/test', 'POST', [
            'email' => 'test@example.com',
            'website' => 'spam.com'
        ], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml',
        ]);
        
        $result = SecurityService::performSecurityChecks($honeypotRequest, 'test');
        expect($result['passed'])->toBeFalse();
        expect($result['issues'])->toContain('honeypot_triggered');
    });
});
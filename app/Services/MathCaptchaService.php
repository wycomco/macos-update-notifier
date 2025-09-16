<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class MathCaptchaService
{
    /**
     * Generate a math captcha problem and store the answer in session
     */
    public static function generate(): array
    {
        // $operations = ['+', '-', '*'];
        $operations = ['+', '*'];
        $operation = $operations[array_rand($operations)];
        
        // Generate numbers based on operation to keep answers reasonable
        switch ($operation) {
            case '+':
                $num1 = rand(1, 50);
                $num2 = rand(1, 50);
                $answer = $num1 + $num2;
                break;
            case '-':
                $num1 = rand(10, 100);
                $num2 = rand(1, $num1 - 1); // Ensure positive result
                $answer = $num1 - $num2;
                break;
            case '*':
                $num1 = rand(1, 12);
                $num2 = rand(1, 12);
                $answer = $num1 * $num2;
                break;
        }
        
        $question = "{$num1} {$operation} {$num2}";
        
        // Store answer in session with a unique key to prevent conflicts
        $captchaKey = 'math_captcha_' . uniqid();
        Session::put($captchaKey, [
            'answer' => $answer,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0
        ]);
        
        return [
            'question' => $question,
            'key' => $captchaKey
        ];
    }
    
    /**
     * Verify the captcha answer
     */
    public static function verify(string $captchaKey, string $userAnswer): bool
    {
        $captchaData = Session::get($captchaKey);
        
        if (!$captchaData) {
            return false;
        }
        
        // Check if expired
        if (now()->gt($captchaData['expires_at'])) {
            Session::forget($captchaKey);
            return false;
        }
        
        // Check attempts limit
        if ($captchaData['attempts'] >= 3) {
            Session::forget($captchaKey);
            return false;
        }
        
        // Increment attempts
        $captchaData['attempts']++;
        Session::put($captchaKey, $captchaData);
        
        // Check answer
        $isCorrect = (int) $userAnswer === $captchaData['answer'];
        
        if ($isCorrect) {
            Session::forget($captchaKey);
        }
        
        return $isCorrect;
    }
    
    /**
     * Clear expired captcha sessions
     */
    public static function cleanup(): void
    {
        $allSessions = Session::all();
        
        foreach ($allSessions as $key => $value) {
            if (str_starts_with($key, 'math_captcha_') && 
                isset($value['expires_at']) && 
                now()->gt($value['expires_at'])) {
                Session::forget($key);
            }
        }
    }
}
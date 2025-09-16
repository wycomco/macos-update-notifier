<?php

namespace App\Http\Requests;

use App\Services\MathCaptchaService;
use App\Services\SecurityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class MagicLinkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'captcha_answer' => ['required', 'integer'],
            'captcha_key' => ['required', 'string'],
            // Honeypot fields (should be empty)
            'website' => ['max:0'],
            'url' => ['max:0'],
            'homepage' => ['max:0'],
            'phone' => ['max:0'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Get input data
            $data = $validator->getData();
            
            // Perform comprehensive security checks
            $securityCheck = SecurityService::performSecurityChecks($this, 'magic_link');
            
            if (!$securityCheck['passed']) {
                if (in_array('rate_limited', $securityCheck['issues'])) {
                    $retryAfter = $securityCheck['rate_limit']['delay'] ?? 60;
                    throw new ThrottleRequestsException('Too many requests', null, [], $retryAfter);
                }
                
                $validator->errors()->add('security', 'Security validation failed. Please try again.');
                return;
            }
            
            // Verify CAPTCHA  
            $captchaKey = $data['captcha_key'] ?? '';
            $captchaAnswer = $data['captcha_answer'] ?? '';
            
            if (!MathCaptchaService::verify($captchaKey, $captchaAnswer)) {
                $validator->errors()->add('captcha_answer', 'Incorrect answer. Please try again.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'captcha_answer.required' => 'Please solve the math problem.',
            'captcha_answer.integer' => 'Please enter a number.',
            'website.max' => 'Invalid form submission.',
            'url.max' => 'Invalid form submission.',
            'homepage.max' => 'Invalid form submission.',
            'phone.max' => 'Invalid form submission.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        // Record failed attempt for progressive rate limiting
        SecurityService::recordFailedAttempt($this, 'magic_link');
        
        parent::failedValidation($validator);
    }
}
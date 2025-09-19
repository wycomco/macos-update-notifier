<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\MagicLinkRequest;
use App\Models\User;
use App\Notifications\MagicLinkNotification;
use App\Services\MathCaptchaService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MagicLinkController extends Controller
{
    /**
     * Show the magic link request form
     */
    public function create()
    {
        // Generate CAPTCHA for the form
        $captcha = MathCaptchaService::generate();
        
        return view('auth.magic-link', [
            'captcha' => $captcha
        ]);
    }

    /**
     * Send a magic link to the user's email
     */
    public function store(MagicLinkRequest $request)
    {
        $email = $request->validated()['email'];
        $user = User::where('email', $email)->first();

        try {

            if ($user) {
                // Generate signed URL for existing user
                $magicLink = URL::temporarySignedRoute(
                    'magic-link.verify',
                    now()->addMinutes(15),
                    ['user' => $user->id]
                );

                $user->notify(new MagicLinkNotification($magicLink));

                $message = 'Magic link sent! Check your email';
            } else {
                // For unknown emails, generate a special signed URL that includes the email
                $magicLink = URL::temporarySignedRoute(
                    'magic-link.verify-new',
                    now()->addMinutes(15),
                    ['email' => $email]
                );

                // Send magic link to the email without creating a user yet
                \Illuminate\Support\Facades\Mail::to($email)->send(
                    new \App\Mail\MagicLinkMail($magicLink, $email)
                );

                $message = 'Magic link sent! If this email is valid, an account will be created when you click the link.';
            }

            // Clear rate limits on successful submission
            SecurityService::clearRateLimit($request, 'magic_link');

        } catch (\Exception $e) {
            Log::error('Failed to send magic link email: ' . $e->getMessage());
            
            // Record failed attempt for rate limiting
            SecurityService::recordFailedAttempt($request, 'magic_link');
            
            return back()->withErrors(['email' => 'Failed to send magic link. Please try again later.']);
        }

        return back()->with('success', $message);
    }

    /**
     * Verify the magic link and log the user in
     */
    public function verify(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('magic-link.form')->withErrors([
                'email' => 'This login link is invalid or has expired.',
            ]);
        }

        Auth::login($user);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Verify magic link for new user creation
     */
    public function verifyNew(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('magic-link.form')->withErrors([
                'email' => 'This login link is invalid or has expired.',
            ]);
        }

        $email = $request->query('email');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('magic-link.form')->withErrors([
                'email' => 'Invalid email address.',
            ]);
        }

        // Check if user already exists (maybe created in the meantime)
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            Auth::login($existingUser);
            $existingUser->update(['last_login_at' => now()]);
            return redirect()->intended(route('dashboard'));
        }

        // Create new user account
        $user = User::create([
            'email' => $email,
            'name' => explode('@', $email)[0], // Use part before @ as default name
            'password' => bcrypt(Str::random(32)), // Generate random password (not used for magic link auth)
            'email_verified_at' => now(), // Auto-verify email since they're using it
            'is_super_admin' => $this->shouldBeSuperAdmin($email), // Check if email should be super admin
        ]);

        Auth::login($user);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'))->with('success', 'Welcome! Your account has been created.');
    }

    /**
     * Determine if an email should be granted super admin privileges
     * This could be configured via environment variables or a config file
     */
    private function shouldBeSuperAdmin(string $email): bool
    {
        // Get super admin emails from config
        $superAdminEmails = collect(config('auth.super_admin_emails', []))
            ->map(fn($email) => trim($email))
            ->filter();

        // Check if the email is in the super admin list
        if ($superAdminEmails->contains($email)) {
            return true;
        }

        // Additional logic could be added here, such as:
        // - Domain-based validation (e.g., all @company.com emails)
        // - Pattern matching
        // - Database-stored whitelist

        return false;
    }
}

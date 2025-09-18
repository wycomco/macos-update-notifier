<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\App;

class PublicSubscriberController extends Controller
{
    /**
     * Set the application locale based on subscriber's language
     */
    private function setLocaleForSubscriber(Subscriber $subscriber): void
    {
        if ($subscriber->language && in_array($subscriber->language, array_keys(config('subscriber_languages.supported', [])))) {
            App::setLocale($subscriber->language);
        }
    }
    
    /**
     * Show unsubscribe confirmation page
     */
    public function showUnsubscribe(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid unsubscribe link');
        }
        
        $this->setLocaleForSubscriber($subscriber);
        
        if (!$subscriber->isActive()) {
            return view('public.already-unsubscribed', compact('subscriber'));
        }
        
        return view('public.unsubscribe', compact('subscriber'));
    }

    /**
     * Process unsubscribe
     */
    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid unsubscribe link');
        }
        
        $this->setLocaleForSubscriber($subscriber);
        
        if ($subscriber->isActive()) {
            $subscriber->unsubscribe();
            return redirect()->route('public.unsubscribed', $token)->with('success', __('public.unsubscribe.success'));
        }
        
        return redirect()->back()->with('success', 'You have been successfully unsubscribed.');
    }

    /**
     * Show unsubscribed confirmation page
     */
    public function showUnsubscribed(string $token): View
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Invalid link');
        }

        $this->setLocaleForSubscriber($subscriber);

        return view('public.unsubscribed');
    }

    /**
     * Show version changed confirmation page
     */
    public function showVersionChanged(string $token): View
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Invalid link');
        }

        $this->setLocaleForSubscriber($subscriber);

        return view('public.version-changed');
    }

    /**
     * Show language changed confirmation page
     */
    public function showLanguageChanged(string $token): View
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            abort(404, 'Invalid link');
        }

        $this->setLocaleForSubscriber($subscriber);

        return view('public.language-changed');
    }

    /**
     * Show version change form
     */
    public function showVersionChange(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid link');
        }
        
        $this->setLocaleForSubscriber($subscriber);
        
        if (!$subscriber->isActive()) {
            return view('public.subscriber-inactive', compact('subscriber'));
        }
        
        // Handle subscribed versions update
        $availableVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        if (empty($availableVersions)) {
            $availableVersions = ['macOS 14', 'macOS 15'];
        }
        
        return view('public.change-version', compact('subscriber', 'availableVersions'));
    }

    /**
     * Update subscriber's version preference
     */
    public function changeVersion(Request $request, string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid link');
        }
        
        $this->setLocaleForSubscriber($subscriber);
        
        if (!$subscriber->isActive()) {
            return view('public.subscriber-inactive', compact('subscriber'));
        }
        
        // Handle subscribed versions update
        $availableVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        if (empty($availableVersions)) {
            $availableVersions = ['macOS 14', 'macOS 15'];
        }
        
        $request->validate([
            'subscribed_versions' => ['required', 'array', 'min:1'],
            'subscribed_versions.*' => ['string', 'in:' . implode(',', $availableVersions)],
        ]);
        
        $subscriber->updateVersions($request->subscribed_versions);
        
        return redirect()->route('public.version-changed', $token)->with('success', __('public.version_change.success'));
    }

    /**
     * Show language change form
     */
    public function showLanguageChange(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid link');
        }
        
        $this->setLocaleForSubscriber($subscriber);
        
        if (!$subscriber->isActive()) {
            return view('public.subscriber-inactive', compact('subscriber'));
        }
        
        $supportedLanguages = config('subscriber_languages.supported', []);
        
        return view('public.change-language', compact('subscriber', 'supportedLanguages'));
    }

    /**
     * Update subscriber's language preference
     */
    public function changeLanguage(Request $request, string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid link');
        }
        
        $this->setLocaleForSubscriber($subscriber);
        
        if (!$subscriber->isActive()) {
            return view('public.subscriber-inactive', compact('subscriber'));
        }
        
        $request->validate([
            'language' => config('subscriber_languages.validation_rule', 'required|string|in:en,de,fr,es'),
        ]);
        
        $subscriber->updateLanguage($request->language);
        
        // Update locale for the success message
        $this->setLocaleForSubscriber($subscriber);
        
        return redirect()->route('public.language-changed', $token)->with('success', __('public.language_change.success'));
    }
}

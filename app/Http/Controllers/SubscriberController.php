<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SubscriberController extends Controller
{
    /**
     * Get available macOS versions from the database or fallback versions
     * 
     * Priority order:
     * 1. Database versions (from SOFA feed)
     * 2. Fallback hardcoded versions (when database is empty or SOFA unavailable)
     */
    private function getAvailableVersions(): array
    {
        // Get versions from database (populated by SOFA feed via FetchMacOSReleases job)
        $dbVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        // If we have versions in the database, use those (preferred: from SOFA feed)
        if (!empty($dbVersions)) {
            // Sort versions naturally
            usort($dbVersions, function($a, $b) {
                return version_compare($a, $b);
            });
            
            return $dbVersions;
        }
        
        // Fallback: Comprehensive list of macOS major versions 
        // Used only when database is empty (e.g., before first SOFA fetch or if SOFA is unavailable)
        $fallbackVersions = [
            'macOS 10.15', // Catalina
            'macOS 11',    // Big Sur
            'macOS 12',    // Monterey
            'macOS 13',    // Ventura
            'macOS 14',    // Sonoma
            'macOS 15',    // Sequoia
            'macOS 26',    // Tahoe
        ];
        
        // Sort versions naturally
        usort($fallbackVersions, function($a, $b) {
            return version_compare($a, $b);
        });
        
        return $fallbackVersions;
    }

    /**
     * Get subscribers for the current admin
     */
    private function getSubscribersQuery($showAll = false)
    {
        if (Auth::user()->isSuperAdmin() && $showAll) {
            return Subscriber::query();
        }
        
        return Auth::user()->subscribers();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle view mode toggle for super admins
        $showAll = false;
        if (Auth::user()->isSuperAdmin()) {
            // Check for toggle parameter
            if ($request->has('show_all')) {
                $showAll = $request->boolean('show_all');
                // Store preference in session
                session(['subscriber_view_mode' => $showAll ? 'all' : 'own']);
            } else {
                // Use stored preference or default to own subscribers
                $viewMode = session('subscriber_view_mode', 'own');
                $showAll = ($viewMode === 'all');
            }
        }
        
        $subscribers = $this->getSubscribersQuery($showAll)->paginate(10);
        $totalSubscribers = $this->getSubscribersQuery($showAll)->count();
        $latestReleases = Release::orderByDesc('release_date')->take(5)->get();
        
        return view('subscribers.index', compact('subscribers', 'totalSubscribers', 'latestReleases', 'showAll'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableVersions = $this->getAvailableVersions();
        
        $supportedLanguages = config('subscriber_languages.supported', []);
        return view('subscribers.create', compact('availableVersions', 'supportedLanguages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $availableVersions = $this->getAvailableVersions();
        
                $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:subscribers,email'],
            'subscribed_versions' => ['required', 'array', 'min:1'],
            'subscribed_versions.*' => ['string', 'in:' . implode(',', $availableVersions)],
            'days_to_install' => ['required', 'integer', 'min:1', 'max:365'],
            'language' => config('subscriber_languages.validation_rule', 'required|string|in:en,de,fr,es'),
        ]);

        $validated['admin_id'] = Auth::id();
        
        // Apply default language if none specified
        if (empty($validated['language'])) {
            $validated['language'] = config('subscriber_languages.default', 'en');
        }
        
        Subscriber::create($validated);

        return redirect()->route('subscribers.index')
            ->with('success', 'Subscriber created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriber $subscriber)
    {
        $this->authorizeSubscriber($subscriber);
        
        $relatedReleases = Release::whereIn('major_version', $subscriber->subscribed_versions)
            ->orderByDesc('release_date')
            ->take(10)
            ->get();
            
        return view('subscribers.show', compact('subscriber', 'relatedReleases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscriber $subscriber)
    {
        $this->authorizeSubscriber($subscriber);
        
        $availableVersions = $this->getAvailableVersions();
        $supportedLanguages = config('subscriber_languages.supported', []);
        return view('subscribers.edit', compact('subscriber', 'availableVersions', 'supportedLanguages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscriber $subscriber)
    {
        $this->authorizeSubscriber($subscriber);
        
        $availableVersions = $this->getAvailableVersions();
        
        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('subscribers', 'email')->ignore($subscriber->id)],
            'language' => config('subscriber_languages.validation_rule', 'required|string|in:en,de,fr,es'),
            'subscribed_versions' => ['required', 'array', 'min:1'],
            'subscribed_versions.*' => ['string', Rule::in($availableVersions)],
            'days_to_install' => ['required', 'integer', 'min:1', 'max:365']
        ]);

        // Apply default language if none specified
        if (empty($validated['language'])) {
            $validated['language'] = config('subscriber_languages.default', 'en');
        }

        // Check if language changed and log the action
        if ($subscriber->language !== $validated['language']) {
            $subscriber->updateLanguage($validated['language']);
        }

        // Check if subscribed versions changed and log the action
        if ($subscriber->subscribed_versions !== $validated['subscribed_versions']) {
            $subscriber->updateVersions($validated['subscribed_versions']);
        }
        
        // Update other fields normally
        $subscriber->update([
            'email' => $validated['email'],
            'days_to_install' => $validated['days_to_install']
        ]);

        return redirect()->route('subscribers.show', $subscriber)
            ->with('success', 'Subscriber updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriber $subscriber)
    {
        $this->authorizeSubscriber($subscriber);
        
        $subscriber->delete();

        return redirect()->route('subscribers.index')
            ->with('success', 'Subscriber deleted successfully!');
    }

    /**
     * Authorize access to subscriber
     */
    private function authorizeSubscriber(Subscriber $subscriber)
    {
        if (!Auth::user()->isSuperAdmin() && $subscriber->admin_id !== Auth::id()) {
            abort(403, 'Unauthorized access to subscriber');
        }
    }
}

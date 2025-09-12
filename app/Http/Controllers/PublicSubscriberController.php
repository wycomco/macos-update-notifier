<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class PublicSubscriberController extends Controller
{
    /**
     * Show unsubscribe confirmation page
     */
    public function showUnsubscribe(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid unsubscribe link');
        }
        
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
        
        if ($subscriber->isActive()) {
            $subscriber->unsubscribe();
        }
        
        return redirect()->back()->with('success', 'You have been successfully unsubscribed.');
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
        
        if (!$subscriber->isActive()) {
            return view('public.subscriber-inactive', compact('subscriber'));
        }
        
        $availableVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        // Fallback if no releases
        if (empty($availableVersions)) {
            $availableVersions = ['macOS 14', 'macOS 15'];
        }
        
        return view('public.change-version', compact('subscriber', 'availableVersions'));
    }

    /**
     * Process version change
     */
    public function changeVersion(Request $request, string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            abort(404, 'Invalid link');
        }
        
        if (!$subscriber->isActive()) {
            return view('public.subscriber-inactive', compact('subscriber'));
        }
        
        // Handle both macos_version and subscribed_versions for backwards compatibility
        if ($request->has('macos_version')) {
            $validMacOSVersions = ['Sonoma', 'Monterey', 'Big Sur', 'Ventura'];
            
            $request->validate([
                'macos_version' => ['required', 'string', 'in:' . implode(',', $validMacOSVersions)],
            ]);
            
            $subscriber->updateMacOSVersion($request->macos_version);
        } else {
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
        }
        
        return redirect()->back()->with('success', 'Your version preferences have been updated.');
    }
}

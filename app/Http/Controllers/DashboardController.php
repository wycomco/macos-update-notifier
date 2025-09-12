<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\SubscriberAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return view('dashboard.super-admin', $this->getSuperAdminData());
        } else {
            return view('dashboard.admin', $this->getAdminData());
        }
    }
    
    public function admin()
    {
        return view('dashboard.admin', $this->getAdminData());
    }
    
    public function superAdmin()
    {
        $this->authorize('viewAny', User::class); // Ensure only super admin can access
        return view('dashboard.super-admin', $this->getSuperAdminData());
    }

    private function getAdminData()
    {
        $user = Auth::user();
        
        // Get subscribers for this admin only
        $totalSubscribers = $user->subscribers()->count();
        $activeSubscribers = $user->subscribers()->where('is_subscribed', true)->count();
        $unsubscribedCount = $user->subscribers()->where('is_subscribed', false)->count();
        
        // Calculate subscription rate
        $subscriptionRate = $totalSubscribers > 0 ? round(($activeSubscribers / $totalSubscribers) * 100, 1) : 0;
        
        // Get version distribution for this admin's subscribers
        $subscribers = $user->subscribers()
            ->where('is_subscribed', true)
            ->whereNotNull('subscribed_versions')
            ->pluck('subscribed_versions');
        
        $versionStats = [];
        foreach ($subscribers as $versions) {
            if (is_array($versions)) {
                foreach ($versions as $version) {
                    $versionStats[$version] = ($versionStats[$version] ?? 0) + 1;
                }
            }
        }
        
        // Get recent actions for this admin's subscribers
        $recentActions = SubscriberAction::whereHas('subscriber', function ($query) use ($user) {
            $query->where('admin_id', $user->id);
        })->with('subscriber')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return compact(
            'totalSubscribers',
            'activeSubscribers', 
            'unsubscribedCount',
            'subscriptionRate',
            'versionStats',
            'recentActions'
        );
    }

    private function getSuperAdminData()
    {
        // System-wide stats
        $totalAdmins = User::count();
        $totalSubscribers = Subscriber::count();
        $activeSubscribers = Subscriber::where('is_subscribed', true)->count();
        $unsubscribedCount = Subscriber::where('is_subscribed', false)->count();
        
        // Calculate subscription rate
        $subscriptionRate = $totalSubscribers > 0 ? round(($activeSubscribers / $totalSubscribers) * 100, 1) : 0;
        
        // System-wide version distribution
        $subscribers = Subscriber::where('is_subscribed', true)
            ->whereNotNull('subscribed_versions')
            ->pluck('subscribed_versions');
        
        $versionStats = [];
        foreach ($subscribers as $versions) {
            if (is_array($versions)) {
                foreach ($versions as $version) {
                    $versionStats[$version] = ($versionStats[$version] ?? 0) + 1;
                }
            }
        }
        
        // Admin performance stats
        $adminStats = User::where('is_super_admin', false)
            ->orWhereNull('is_super_admin')
            ->withCount('subscribers')
            ->orderBy('subscribers_count', 'desc')
            ->get();
        
        // Recent system activity
        $recentActions = SubscriberAction::with('subscriber')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
        
        return compact(
            'totalAdmins',
            'totalSubscribers',
            'activeSubscribers',
            'unsubscribedCount',
            'subscriptionRate',
            'versionStats',
            'adminStats',
            'recentActions'
        );
    }
}

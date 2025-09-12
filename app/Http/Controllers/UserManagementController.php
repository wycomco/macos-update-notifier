<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function __construct()
    {
        // This will be handled by route middleware
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $users = User::withCount('subscribers')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $user->load(['subscribers' => function ($query) {
            $query->latest()->limit(10);
        }]);
        
        // Get recent actions for this user's subscribers
        $recentActions = \App\Models\SubscriberAction::whereHas('subscriber', function ($query) use ($user) {
            $query->where('admin_id', $user->id);
        })->with('subscriber')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.users.show', compact('user', 'recentActions'));
    }

    /**
     * Promote user to super admin
     */
    public function promoteToSuperAdmin(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }
        
        if ($user->is_super_admin) {
            return back()->with('error', "User {$user->email} is already a super admin.");
        }

        $user->promoteToSuperAdmin();

        return back()->with('success', "User {$user->email} has been promoted to super admin.");
    }

    /**
     * Demote user from super admin
     */
    public function demoteFromSuperAdmin(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($user->id === Auth::id()) {
            abort(403, 'You cannot demote yourself.');
        }
        
        if (!$user->is_super_admin) {
            return back()->with('error', "User {$user->email} is not a super admin.");
        }

        $user->update(['is_super_admin' => false]);

        return back()->with('success', "User {$user->email} has been demoted from super admin.");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($user->id === Auth::id()) {
            abort(403, 'You cannot delete yourself.');
        }

        $userEmail = $user->email;
        $subscriberCount = $user->subscribers()->count();

        // Delete the user (subscribers will be deleted due to cascade foreign key constraint)
        $user->delete();

        $message = "User {$userEmail} has been deleted.";
        if ($subscriberCount > 0) {
            $message .= " Their {$subscriberCount} subscriber(s) have also been deleted.";
        }

        return back()->with('success', $message);
    }
}

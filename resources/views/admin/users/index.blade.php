<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                    User Management
                </h2>
                <p class="mt-2 text-slate-400">
                    Manage administrators and view user statistics
                </p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-400">{{ $users->total() }} total users</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-8">
            
            @if (session('success'))
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-green-400 font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-pink-600/20 rounded-2xl blur-xl"></div>
                    <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-500/20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-red-400 font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10 rounded-2xl blur-xl"></div>
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-purple-500/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">All Users</h3>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-white/10">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-white/10 bg-white/5">
                                        <th class="text-left py-4 px-6 text-slate-400 font-medium text-sm">User</th>
                                        <th class="text-left py-4 px-6 text-slate-400 font-medium text-sm">Role</th>
                                        <th class="text-left py-4 px-6 text-slate-400 font-medium text-sm">Subscribers</th>
                                        <th class="text-left py-4 px-6 text-slate-400 font-medium text-sm">Last Login</th>
                                        <th class="text-left py-4 px-6 text-slate-400 font-medium text-sm">Joined</th>
                                        <th class="text-left py-4 px-6 text-slate-400 font-medium text-sm">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/10">
                                    @foreach($users as $user)
                                        <tr class="hover:bg-white/5 transition-colors">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->name ?? $user->email, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-white font-medium">{{ $user->name ?? 'N/A' }}</p>
                                                        <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                @if($user->is_super_admin)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-500/20 to-orange-500/20 text-yellow-400 border border-yellow-500/30">
                                                        Super Admin
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                                        Admin
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="text-white font-medium">{{ $user->subscribers_count }}</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="text-slate-300">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="text-slate-300">{{ $user->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                       class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/30 text-blue-400 font-medium rounded-lg transition-all duration-300 text-sm">
                                                        View
                                                    </a>
                                                    
                                                    @if(!$user->is_super_admin)
                                                        <form method="POST" action="{{ route('admin.users.promote', $user) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/20 hover:bg-green-500/30 border border-green-500/30 text-green-400 font-medium rounded-lg transition-all duration-300 text-sm"
                                                                    onclick="return confirm('Promote {{ $user->email }} to super admin?')">
                                                                Promote
                                                            </button>
                                                        </form>
                                                    @elseif($user->id !== Auth::id())
                                                        <form method="POST" action="{{ route('admin.users.demote', $user) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-orange-500/20 hover:bg-orange-500/30 border border-orange-500/30 text-orange-400 font-medium rounded-lg transition-all duration-300 text-sm"
                                                                    onclick="return confirm('Demote {{ $user->email }} from super admin?')">
                                                                Demote
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($user->id !== Auth::id())
                                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-400 font-medium rounded-lg transition-all duration-300 text-sm"
                                                                    onclick="return confirm('Are you sure you want to delete {{ $user->email }}? This action cannot be undone. All of their subscribers will also be permanently deleted.')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

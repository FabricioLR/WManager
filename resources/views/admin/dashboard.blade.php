<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - WManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 3px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 h-screen w-screen overflow-hidden font-sans antialiased flex text-slate-100" 
      x-data="{ 
          showAddUserModal: {{ $errors->any() ? 'true' : 'false' }}, 
          searchQuery: '',
          matchesSearch(text) {
              return text.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
          }
      }">

    <!-- Toast / Alert Floating Container -->
    <div class="fixed top-5 right-5 z-50 space-y-3 max-w-sm w-full pointer-events-none">
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-2"
                 class="pointer-events-auto bg-slate-800 border-l-4 border-emerald-500 p-4 rounded-lg shadow-2xl flex items-start space-x-3 border border-slate-700">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-200">Success</p>
                    <p class="text-xs text-emerald-400 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Outer Container -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Top Navigation Bar -->
        <header class="h-[60px] bg-slate-800/80 px-6 border-b border-slate-700/60 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold text-sm shadow-md">
                    ADM
                </div>
                <div>
                    <h1 class="font-semibold text-slate-200 text-sm">Admin Workspace</h1>
                    <p class="text-[11px] text-slate-400">User Management & System Access</p>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center space-x-2">
                <!-- Back to Chat -->
                <a href="{{ route('chat') }}" title="Back to Chat" class="px-3 py-1.5 bg-slate-700/60 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-medium transition flex items-center space-x-1.5 border border-slate-600/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Return to Chat</span>
                </a>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="GET" class="inline">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-2 hover:bg-slate-700/60 rounded-full text-slate-400 hover:text-rose-400 transition flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content Area -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            
            <!-- Dashboard Toolbar / Header Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-100">System Users</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Manage users, grant administrative permissions, and track active platform accounts.</p>
                </div>
                
                <button @click="showAddUserModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-lg shadow-md transition flex items-center justify-center space-x-2 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Add New User</span>
                </button>
            </div>

            <!-- Table Card Container -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden flex flex-col">
                
                <!-- Table Search Filter Header -->
                <div class="p-4 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between">
                    <div class="relative w-full max-w-xs">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Search users..." 
                               class="w-full pl-9 pr-4 py-1.5 bg-slate-800/70 text-slate-200 rounded-lg text-xs border border-slate-700/60 focus:outline-none focus:border-emerald-500 placeholder-slate-400 transition">
                    </div>
                    <span class="text-xs text-slate-400 hidden sm:inline">Total: {{ count($users ?? []) }} Users</span>
                </div>

                <!-- Responsive Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/50 border-b border-slate-800 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-xs">
                            @forelse($users as $u)
                                <tr x-show="matchesSearch('{{ strtolower($u->name . ' ' . $u->email) }}')" class="hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 text-emerald-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($u->name ?? $u->email, 0, 2)) }}
                                        </div>
                                        <span class="font-medium text-slate-200 truncate">{{ $u->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-300 font-mono">{{ $u->email }}</td>
                                    <td class="py-3 px-4">
                                        @if($u->is_admin)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-400">
                                        {{ $u->created_at ? $u->created_at->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 text-xs">
                                        No users found in system.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Add New User -->
    <div x-show="showAddUserModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="showAddUserModal = false" 
             class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border-b-8 border-b-emerald-500"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 bg-slate-800 border border-slate-700 rounded-lg flex items-center justify-center text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-100">Create New User</h3>
                </div>
                <button @click="showAddUserModal = false" class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('dashboard.user.store') }}" method="POST" class="p-6 space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-300 mb-1.5">Full Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           placeholder="John Doe" 
                           required 
                           class="w-full px-3 py-2 bg-slate-800 text-slate-200 rounded-lg text-xs border border-slate-700/80 focus:outline-none focus:border-emerald-500 placeholder-slate-500 transition [color-scheme:dark] autofill:shadow-[0_0_0_30px_#1e293b_inset] autofill:[-webkit-text-fill-color:#e2e8f0]">
                    @error('name')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}"
                           placeholder="user@example.com" 
                           required 
                           class="w-full px-3 py-2 bg-slate-800 text-slate-200 rounded-lg text-xs border border-slate-700/80 focus:outline-none focus:border-emerald-500 placeholder-slate-500 transition [color-scheme:dark] autofill:shadow-[0_0_0_30px_#1e293b_inset] autofill:[-webkit-text-fill-color:#e2e8f0]">
                    @error('email')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="••••••••" 
                           required 
                           class="w-full px-3 py-2 bg-slate-800 text-slate-200 rounded-lg text-xs border border-slate-700/80 focus:outline-none focus:border-emerald-500 placeholder-slate-500 transition [color-scheme:dark] autofill:shadow-[0_0_0_30px_#1e293b_inset] autofill:[-webkit-text-fill-color:#e2e8f0]">
                    @error('password')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Admin Checkbox -->
                <div class="pt-1">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" 
                               name="is_admin" 
                               value="1" 
                               {{ old('is_admin') ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-slate-900">
                        <span class="text-xs text-slate-300 font-medium">Grant Administrator Privileges</span>
                    </label>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-800/80">
                    <button type="button" 
                            @click="showAddUserModal = false" 
                            class="px-4 py-2 bg-slate-800 hover:bg-slate-700/80 text-slate-300 rounded-lg text-xs font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="isSubmitting" 
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white rounded-lg text-xs font-medium transition flex items-center space-x-1.5">
                        <span x-show="!isSubmitting">Create User</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center space-x-1.5">
                            <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Creating...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
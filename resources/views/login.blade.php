<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 3px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 h-screen w-screen overflow-hidden font-sans antialiased flex items-center justify-center text-slate-100 relative">

    <!-- Toast / Alert Floating Container -->
    <div class="fixed top-5 right-5 z-50 space-y-3 max-w-sm w-full pointer-events-none">
        @if(isset($error) || $errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-2"
                 class="pointer-events-auto bg-slate-800 border-l-4 border-rose-500 p-4 rounded-lg shadow-2xl flex items-start space-x-3 border border-slate-700">
                <svg class="w-5 h-5 flex-shrink-0 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-200">Authentication Error</p>
                    <p class="text-xs text-rose-400 mt-0.5">{{ $error ?? $errors->first() }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('status'))
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
                    <p class="text-sm font-semibold text-slate-200">Notice</p>
                    <p class="text-xs text-emerald-400 mt-0.5">{{ session('status') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Login Card -->
    <main class="w-full max-w-md p-6 sm:p-8" x-data="{ showPassword: false, isSubmitting: false }">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden border-b-8 border-b-emerald-500">
            
            <!-- Branding Header -->
            <div class="p-6 text-center border-b border-slate-800 bg-slate-900/50">
                <div class="w-16 h-16 bg-slate-800 border border-slate-700 rounded-full flex items-center justify-center text-emerald-400 mx-auto mb-4 shadow-xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-semibold text-slate-100 tracking-wide">WManager</h1>
                <p class="text-xs text-slate-400 mt-1">Sign in to access your WhatsApp Cloud workspace</p>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login.submit') }}" method="POST" @submit="isSubmitting = true" class="p-6 space-y-4">
                @csrf

                 <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1.5">Email Address</label>
                    <div class="relative flex items-center">
                        <svg class="w-4 h-4 absolute left-3 text-slate-400 z-10 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        <input type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}" 
                            placeholder="admin@example.com" 
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-2 bg-slate-800 text-slate-200 rounded-lg text-sm border border-slate-700/80 focus:outline-none focus:border-emerald-500 placeholder-slate-500 transition [color-scheme:dark] autofill:shadow-[0_0_0_30px_#1e293b_inset] autofill:[-webkit-text-fill-color:#e2e8f0]">
                    </div>
                    @error('email')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-medium text-slate-300">Password</label>
                    </div>
                    <div class="relative flex items-center">
                        <svg class="w-4 h-4 absolute left-3 text-slate-400 z-10 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            id="password" 
                            placeholder="••••••••" 
                            required 
                            class="w-full pl-10 pr-10 py-2 bg-slate-800 text-slate-200 rounded-lg text-sm border border-slate-700/80 focus:outline-none focus:border-emerald-500 placeholder-slate-500 transition [color-scheme:dark] autofill:shadow-[0_0_0_30px_#1e293b_inset] autofill:[-webkit-text-fill-color:#e2e8f0]">
                        
                        <button type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute right-3 text-slate-400 hover:text-slate-200 transition focus:outline-none z-10">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.98 8.98 0 013.682-.793c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="isSubmitting" 
                        class="w-full mt-2 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-medium text-sm rounded-lg shadow-md transition flex items-center justify-center space-x-2">
                    <span x-show="!isSubmitting">Sign In</span>
                    <span x-show="isSubmitting" x-cloak class="flex items-center space-x-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Authenticating...</span>
                    </span>
                </button>
            </form>
        </div>
    </main>

</body>
</html>
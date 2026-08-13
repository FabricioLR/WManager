<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 3px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 h-screen w-screen overflow-hidden font-sans antialiased flex text-slate-100" x-data="wmanager()">
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
                <svg class="w-5 h-5 flex-shrink-0 text-rose-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-200">Warning: Action encountered errors.</p>
                    <p class="text-xs text-rose-400 mt-0.5">{{ $error ?? $errors->first() }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif

        @if (session('success'))
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
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-200">Success</p>
                    <p class="text-xs text-emerald-400 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif

    </div>

    <div class="flex h-full w-full bg-slate-900 overflow-hidden">
        <aside class="w-full md:w-[380px] lg:w-[420px] bg-slate-900 border-r border-slate-800 flex flex-col h-full flex-shrink-0">
            <div class="h-[60px] bg-slate-800/80 px-4 border-b border-slate-700/60 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold text-sm shadow-md">WA</div>
                    <span class="font-semibold text-slate-200 text-sm">Chats</span>
                </div>

                <!-- Header Actions (New Chat + Logout) -->
                <div class="flex items-center space-x-1">
                    <!-- New Chat Button -->
                    <button @click="showAddContactModal = true" title="New Contact" class="p-2 hover:bg-slate-700/60 rounded-full text-slate-300 hover:text-emerald-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>

                    <a href="{{ route('docs') }}" title="API Documentation" class="p-2 hover:bg-slate-700/60 rounded-full text-slate-400 hover:text-sky-400 transition flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </a>

                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="{{ route('dashboard') }}" title="Admin Panel" class="p-2 hover:bg-slate-700/60 rounded-full text-slate-400 hover:text-amber-400 transition flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </a>
                    @endif

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
            </div>

            <div class="p-2.5 bg-slate-900 border-b border-slate-800 flex-shrink-0">
                <div class="relative flex items-center">
                    <svg class="w-4 h-4 absolute left-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="searchQuery" placeholder="Search or start new chat" class="w-full pl-10 pr-4 py-1.5 bg-slate-800/70 text-slate-200 rounded-lg text-sm border border-slate-700/50 focus:outline-none focus:border-emerald-500 placeholder-slate-400">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-slate-800/50">
                @forelse($contacts as $c)
                    @php 
                        $lastMsg = $c->messages->first(); 
                        $isActive = isset($contact) && $contact->id === $c->id;
                    @endphp
                    <a href="{{ route('chat.show', $c->id) }}" 
                    x-show="matchesSearch('{{ strtolower($c->name ?? $c->phone_number) }}')"
                    class="flex items-center px-4 py-3 hover:bg-slate-800/50 transition cursor-pointer {{ $isActive ? 'bg-slate-800 border-l-4 border-emerald-500' : '' }}">
                        <div class="w-11 h-11 rounded-full bg-slate-700 text-emerald-400 border border-slate-600 flex items-center justify-center font-bold text-base mr-3 flex-shrink-0">
                            {{ strtoupper(substr($c->name ?? $c->phone_number, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-semibold text-slate-200 truncate">{{ $c->name ?? $c->phone_number }}</h3>
                                <span class="text-[11px] text-slate-400 flex-shrink-0 ml-2">
                                    {{ $c->last_message_at ? $c->last_message_at->format('H:i') : '' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 truncate mt-1">
                                {{ $lastMsg ? $lastMsg->body : 'No messages' }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-sm text-slate-400">
                        No conversations found.<br>Click '+' to start a new chat.
                    </div>
                @endforelse
            </div>
        </aside>

        <main class="hidden md:flex flex-1 bg-slate-950 flex-col h-full relative">
            @if(isset($contact))
                <div class="h-[60px] bg-slate-900 px-4 border-b border-slate-800 flex justify-between items-center flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-800 text-emerald-400 border border-slate-700 flex items-center justify-center font-semibold text-sm">
                            {{ strtoupper(substr($contact->name ?? $contact->phone_number, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-slate-200 truncate">{{ $contact->name ?? 'WhatsApp User' }}</h2>
                            <p class="text-[11px] text-slate-400 truncate">+{{ $contact->phone_number }}</p>
                        </div>
                    </div>
                </div>

                <div id="messageContainer" class="flex-1 overflow-y-auto p-6 space-y-3 bg-[radial-gradient(#334155_1px,transparent_1px)] [background-size:16px_16px]">
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->direction === 'inbound' ? 'justify-start' : 'justify-end' }} mb-2">
                            
                            <div class="{{ $msg->direction === 'inbound' ? 'bg-slate-800 text-slate-200 border border-slate-700/80 rounded-tl-none' : 'bg-emerald-600 text-white rounded-tr-none' }} rounded-lg py-2 px-3.5 max-w-[70%] shadow-md"
                                x-data="{ expanded: false, limit: 300 }">
                                
                                <div class="text-sm leading-relaxed break-all whitespace-pre-line">
                                    @if(mb_strlen($msg->body) > 300)
                                        <span x-show="!expanded">
                                            {{ \Illuminate\Support\Str::limit($msg->body, 300) }}
                                        </span>

                                        <span x-show="expanded" x-cloak>
                                            {{ $msg->body }}
                                        </span>

                                        <button @click="expanded = !expanded" 
                                                class="block mt-1 text-xs font-semibold underline opacity-80 hover:opacity-100 transition">
                                            <span x-text="expanded ? 'Show less' : 'Read more'"></span>
                                        </button>
                                    @else
                                        {{ $msg->body }}
                                    @endif
                                </div>

                                <div class="flex items-center justify-end space-x-1 mt-1">
                                    <span class="text-[10px] {{ $msg->direction === 'inbound' ? 'text-slate-400' : 'text-emerald-100' }}">
                                        {{ $msg->timestamp ? $msg->timestamp->format('H:i') : $msg->created_at->format('H:i') }}
                                    </span>

                                    @if($msg->direction !== 'inbound')
                                        @if($msg->status === 'failed')
                                            <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($msg->status === 'sending')
                                            <svg class="w-3.5 h-3.5 text-slate-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @elseif($msg->status === 'sent')
                                            <svg class="w-3.5 h-3.5 text-emerald-200 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($msg->status === 'delivered')
                                            <svg class="w-3.5 h-3.5 text-emerald-200" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.41 13.41 6 19l1.41-1.41L1.83 12m4.58 4.59L18 5l-1.41-1.41M22.59 3.59 11 15.17l-3.59-3.58L6 13l5 5 13-13z"/>
                                            </svg>
                                        @elseif($msg->status === 'read')
                                            <svg class="w-3.5 h-3.5 text-sky-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M.41 13.41 6 19l1.41-1.41L1.83 12m4.58 4.59L18 5l-1.41-1.41M22.59 3.59 11 15.17l-3.59-3.58L6 13l5 5 13-13z"/>
                                            </svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-emerald-200 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                @php
                    $isWithin24hWindow = $contact->last_message_from_contact_at && 
                        $contact->last_message_from_contact_at->greaterThanOrEqualTo(now()->subHours(24));
                @endphp

                @if($isWithin24hWindow)
                    <form @submit.prevent="sendMessage('{{ $contact->phone_number }}')" class="h-[62px] bg-slate-900 px-4 border-t border-slate-800 flex items-center space-x-3 flex-shrink-0">
                        <input type="text" x-model="newMessage" placeholder="Type a message..." :disabled="isSending" class="flex-1 bg-slate-800 text-slate-200 rounded-lg px-4 py-2 text-sm border border-slate-700/60 focus:outline-none focus:border-emerald-500 placeholder-slate-400 disabled:bg-slate-800/50">
                        <button type="submit" :disabled="isSending || !newMessage.trim()" class="bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white rounded-full p-2.5 transition flex-shrink-0 shadow-md">
                            <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                        </button>
                    </form>
                @else
                    <div class="bg-amber-950/40 border-t border-amber-800/40 p-3 flex-shrink-0">
                        <div class="flex items-center space-x-2 text-amber-300 text-xs mb-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>The 24-hour response window has expired. You must send an approved template message.</span>
                        </div>

                        <form @submit.prevent="sendTemplateMessage('{{ $contact->phone_number }}')" class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <select x-model="selectedTemplateName" @change="onTemplateSelect()" :disabled="isLoadingTemplates || isSending" class="flex-1 bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-emerald-500">
                                    <option value="">-- Select an approved Template --</option>
                                    <template x-for="tpl in approvedTemplates" :key="tpl.id">
                                        <option :value="tpl.name" x-text="tpl.name + ' (' + tpl.language + ') - ' + tpl.category"></option>
                                    </template>
                                </select>

                                <button type="submit" :disabled="isSending || !selectedTemplateData" class="bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white rounded-lg px-4 py-2 text-sm font-medium transition flex-shrink-0 shadow-md">
                                    Send Template
                                </button>
                            </div>

                            <template x-if="selectedTemplateData">
                                <div class="bg-slate-900 p-3 border border-slate-800 rounded-lg space-y-3 shadow-md">
                                    <template x-if="headerVariables.length > 0">
                                        <div>
                                            <span class="text-xs font-semibold text-slate-300 block mb-1">Header Parameters:</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <template x-for="(varName, idx) in headerVariables" :key="'header-' + idx">
                                                    <div>
                                                        <label class="block text-[10px] font-medium text-slate-400 truncate" x-text="varName"></label>
                                                        <input type="text" x-model="templateHeaderParams[varName]" :placeholder="'Enter ' + varName + '...'" required class="w-full bg-slate-800 text-slate-200 border border-slate-700 rounded px-2 py-1 text-xs focus:outline-none focus:border-emerald-500">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="bodyVariables.length > 0">
                                        <div>
                                            <span class="text-xs font-semibold text-slate-300 block mb-1">Body Parameters:</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <template x-for="(varName, idx) in bodyVariables" :key="'body-' + idx">
                                                    <div>
                                                        <label class="block text-[10px] font-medium text-slate-400 truncate" x-text="varName"></label>
                                                        <input type="text" x-model="templateBodyParams[varName]" :placeholder="'Enter ' + varName + '...'" required class="w-full bg-slate-800 text-slate-200 border border-slate-700 rounded px-2 py-1 text-xs focus:outline-none focus:border-emerald-500">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="border-t border-slate-800 pt-2">
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Template Preview</span>
                                        <div class="bg-emerald-950/60 border border-emerald-800/40 p-2.5 rounded-lg text-xs text-slate-200 space-y-1">
                                            <template x-if="getHeaderComponent()">
                                                <p class="font-bold text-slate-100" x-text="getHeaderComponent().text || ('[' + getHeaderComponent().format + ' HEADER]')"></p>
                                            </template>
                                            <template x-if="getBodyComponent()">
                                                <p class="whitespace-pre-line leading-normal" x-text="getBodyComponent().text"></p>
                                            </template>
                                            <template x-if="getFooterComponent()">
                                                <p class="text-[10px] text-slate-400 pt-1" x-text="getFooterComponent().text"></p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </form>
                    </div>
                @endif
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-slate-900 border-b-8 border-emerald-500">
                    <div class="w-24 h-24 bg-slate-800 border border-slate-700 rounded-full flex items-center justify-center text-emerald-400 mb-6 shadow-xl">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </div>
                    <h2 class="text-2xl font-light text-slate-200 mb-2">WhatsApp Web for Laravel</h2>
                    <p class="text-sm text-slate-400 max-w-md">Select a conversation or click '+' to send a message via your WhatsApp Cloud API.</p>
                </div>
            @endif
        </main>
    </div>

    <div x-show="showAddContactModal" x-cloak class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="showAddContactModal = false" class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-slate-100 mb-4">Add New Contact</h3>
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Phone Number (with Country Code)</label>
                        <input type="text" name="phone_number" x-model="newContactPhone" placeholder="e.g. 5561999999999" required class="w-full px-3 py-2 bg-slate-800 text-slate-200 border border-slate-700 rounded-lg text-sm focus:outline-none focus:border-emerald-500 placeholder-slate-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showAddContactModal = false" class="px-4 py-2 text-sm text-slate-400 hover:bg-slate-800 rounded-lg transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-medium shadow-md transition disabled:opacity-50">Save Contact</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function wmanager() {
            return {
                showAddContactModal: false,
                searchQuery: '',
                newMessage: '',
                newContactPhone: '',
                newContactMessage: '',
                isSending: false,
                apiSecret: '{{ auth()->user()->api_token }}', 
                templates: [],
                selectedTemplateName: '',
                selectedTemplateData: null,
                headerVariables: [],
                bodyVariables: [],
                templateHeaderParams: {},
                templateBodyParams: {},
                isLoadingTemplates: false,

                init() {
                    this.scrollToBottom();
                    this.fetchTemplates();
                },

                matchesSearch(text) {
                    return !this.searchQuery || text.includes(this.searchQuery.toLowerCase());
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('messageContainer');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                get approvedTemplates() {
                    return this.templates.filter(t => t.status === 'APPROVED');
                },

                async fetchTemplates() {
                    this.isLoadingTemplates = true;
                    try {
                        const response = await fetch('/api/templates', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Api-Secret': this.apiSecret
                            }
                        });
                        if (response.ok) {
                            const result = await response.json();
                            this.templates = result.data || [];
                        }
                    } catch (err) {
                        console.error('Failed to load templates:', err);
                    } finally {
                        this.isLoadingTemplates = false;
                    }
                },

                onTemplateSelect() {
                    this.selectedTemplateData = this.templates.find(t => t.name === this.selectedTemplateName) || null;
                    this.headerVariables = [];
                    this.bodyVariables = [];
                    this.templateHeaderParams = {};
                    this.templateBodyParams = {};

                    if (!this.selectedTemplateData) return;

                    const header = this.getHeaderComponent();
                    if (header && header.format === 'TEXT' && header.text) {
                        const matches = header.text.match(/\{\{([^}]+)\}\}/g);
                        if (matches) {
                            this.headerVariables = matches.map(m => m.replace(/[\{\}]/g, '').trim());
                            this.headerVariables.forEach(v => this.templateHeaderParams[v] = '');
                        }
                    }

                    const body = this.getBodyComponent();
                    if (body) {
                        if (this.selectedTemplateData.parameter_format === 'NAMED' && body.example?.body_text_named_params) {
                            this.bodyVariables = body.example.body_text_named_params.map(p => p.param_name);
                        } else if (body.text) {
                            const matches = body.text.match(/\{\{([^}]+)\}\}/g);
                            if (matches) {
                                this.bodyVariables = [...new Set(matches.map(m => m.replace(/[\{\}]/g, '').trim()))];
                            }
                        }
                        this.bodyVariables.forEach(v => this.templateBodyParams[v] = '');
                    }
                },

                getHeaderComponent() {
                    return this.selectedTemplateData?.components?.find(c => c.type === 'HEADER');
                },

                getBodyComponent() {
                    return this.selectedTemplateData?.components?.find(c => c.type === 'BODY');
                },

                getFooterComponent() {
                    return this.selectedTemplateData?.components?.find(c => c.type === 'FOOTER');
                },

                async sendTemplateMessage(phoneNumber) {
                    if (!this.selectedTemplateData) return;

                    this.isSending = true;

                    const components = [];

                    if (this.templateHeaderParams && this.templateHeaderParams.length > 0) {
                        components.push({
                            type: 'header',
                            parameters: this.templateHeaderParams.map(param => ({
                                type: 'text',
                                text: param
                            }))
                        });
                    }

                    if (this.templateBodyParams && this.templateBodyParams.length > 0) {
                        components.push({
                            type: 'body',
                            parameters: this.templateBodyParams.map(param => ({
                                type: 'text',
                                text: param
                            }))
                        });
                    }

                    const payload = {
                        phone_number: phoneNumber,
                        template_name: this.selectedTemplateData.name,
                        language_code: this.selectedTemplateData.language || 'en_US',
                        components: components
                    };

                    try {
                        const response = await fetch('/api/messages/sendTemplate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Api-Secret': this.apiSecret
                            },
                            body: JSON.stringify(payload)
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            const errorData = await response.json().catch(() => ({}));
                            alert(errorData.message || 'Failed to send template message.');
                        }
                    } catch (error) {
                        console.error('Error sending template message:', error);
                        alert('An error occurred while attempting to send the message.');
                    } finally {
                        this.isSending = false;
                    }
                },

                async sendMessage(phoneNumber) {
                    if (!this.newMessage.trim()) return;

                    this.isSending = true;
                    const messageText = this.newMessage;
                    this.newMessage = '';

                    try {
                        const response = await fetch('/api/messages/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Api-Secret': this.apiSecret
                            },
                            body: JSON.stringify({
                                phone_number: phoneNumber,
                                message: messageText
                            })
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Failed to send message.');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                    } finally {
                        this.isSending = false;
                    }
                },
            }
        }
    </script>
</body>
</html>
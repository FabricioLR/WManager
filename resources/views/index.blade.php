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
        ::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.2); border-radius: 3px; }
    </style>
</head>
<body class="bg-[#d1d7db] h-screen w-screen overflow-hidden font-sans antialiased flex" x-data="wmanager()">
    <!-- Container fixo para os Popups (Toasts) -->
    <div class="fixed top-5 right-5 z-50 space-y-3 max-w-sm w-full pointer-events-none">

        <!-- Popup de Erro -->
        @if(isset($error) || $errors->any())
            <div x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-2"
                class="pointer-events-auto bg-white border-l-4 border-red-500 p-4 rounded-lg shadow-xl flex items-start space-x-3 border border-gray-100">
                
                <!-- Ícone de Alerta -->
                <div class="flex-shrink-0 text-red-500 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">Warning: Action encountered errors.</p>
                    <p class="text-xs text-red-600 mt-0.5">
                        {{ $error ?? $errors->first() }}
                    </p>
                </div>

                <!-- Botão Fechar -->
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Popup de Sucesso -->
        @if (session('success'))
            <div x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-2"
                class="pointer-events-auto bg-white border-l-4 border-green-500 p-4 rounded-lg shadow-xl flex items-start space-x-3 border border-gray-100">
                
                <!-- Ícone de Sucesso -->
                <div class="flex-shrink-0 text-green-500 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">Success</p>
                    <p class="text-xs text-green-600 mt-0.5">{{ session('success') }}</p>
                </div>

                <!-- Botão Fechar -->
                <button @click="show = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

    </div>


    <!-- Fullscreen 100% Layout -->
    <div class="flex h-full w-full bg-white overflow-hidden">
        
        <!-- ================= LEFT PANEL: Contacts Sidebar ================= -->
        <div class="w-full md:w-[380px] lg:w-[420px] bg-white border-r border-gray-200 flex flex-col h-full flex-shrink-0">
            
            <!-- App Header -->
            <div class="h-[60px] bg-[#f0f2f5] px-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-teal-600 text-white flex items-center justify-center font-semibold text-sm">
                        WA
                    </div>
                    <span class="font-semibold text-gray-800 text-sm">Chats</span>
                </div>
                
                <div class="flex items-center space-x-2 text-gray-600">
                    <!-- Add New Contact Button -->
                    <button @click="showAddContactModal = true" title="New Chat" class="p-2 hover:bg-gray-200 rounded-full text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="p-2 bg-white border-b border-gray-100 flex-shrink-0">
                <div class="relative flex items-center">
                    <span class="absolute left-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Search or start new chat" class="w-full pl-10 pr-4 py-1.5 bg-[#f0f2f5] rounded-lg text-sm focus:outline-none placeholder-gray-500">
                </div>
            </div>

            <!-- Contact List (Scrollable) -->
            <div class="flex-1 overflow-y-auto">
                @forelse($contacts as $c)
                    @php 
                        $lastMsg = $c->messages->first(); 
                        $isActive = isset($contact) && $contact->id === $c->id;
                    @endphp
                    <a href="{{ route('chat.show', $c->id) }}" 
                       x-show="matchesSearch('{{ strtolower($c->name ?? $c->phone_number) }}')"
                       class="flex items-center px-4 py-3 border-b border-gray-100 hover:bg-[#f0f2f5] transition cursor-pointer {{ $isActive ? 'bg-[#f0f2f5]' : '' }}">
                        <div class="w-12 h-12 rounded-full bg-teal-500 text-white flex items-center justify-center font-bold text-base mr-3 flex-shrink-0">
                            {{ strtoupper(substr($c->name ?? $c->phone_number, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $c->name ?? $c->phone_number }}</h3>
                                <span class="text-[11px] text-gray-400 flex-shrink-0 ml-2">
                                    {{ $c->last_message_at ? $c->last_message_at->format('H:i') : '' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-1">
                                {{ $lastMsg ? $lastMsg->body : 'No messages' }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-sm text-gray-500">
                        No conversations found.<br>Click '+' to start a new chat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ================= RIGHT PANEL: Active Chat Area ================= -->
        <div class="hidden md:flex flex-1 bg-[#efeae2] flex-col h-full relative">
            @if(isset($contact))
                <!-- Chat Header -->
                <div class="h-[60px] bg-[#f0f2f5] px-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-teal-500 text-white flex items-center justify-center font-semibold text-sm">
                            {{ strtoupper(substr($contact->name ?? $contact->phone_number, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900 truncate">{{ $contact->name ?? 'WhatsApp User' }}</h2>
                            <p class="text-[11px] text-gray-500 truncate">+{{ $contact->phone_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Scroll Box -->
                <div id="messageContainer" class="flex-1 overflow-y-auto p-6 space-y-3 bg-[radial-gradient(#0000000d_1px,transparent_1px)] [background-size:16px_16px]">
                    @foreach($messages as $msg)
                        @if($msg->direction === 'inbound')
                            <!-- Inbound (Left / White) -->
                            <div class="flex justify-start">
                                <div class="bg-white text-gray-900 rounded-lg py-2 px-3 max-w-[70%] shadow-sm rounded-tl-none border border-gray-100">
                                    <p class="text-sm leading-relaxed break-words">{{ $msg->body }}</p>
                                    <span class="text-[10px] text-gray-400 block text-right mt-1">
                                        {{ $msg->timestamp ? $msg->timestamp->format('H:i') : $msg->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <!-- Outbound (Right / Light Green) -->
                            <div class="flex justify-end">
                                <div class="bg-[#d9fdd3] text-gray-900 rounded-lg py-2 px-3 max-w-[70%] shadow-sm rounded-tr-none">
                                    <p class="text-sm leading-relaxed break-words">{{ $msg->body }}</p>
                                    <div class="flex items-center justify-end space-x-1 mt-1">
                                        <span class="text-[10px] text-gray-500">
                                            {{ $msg->timestamp ? $msg->timestamp->format('H:i') : $msg->created_at->format('H:i') }}
                                        </span>
                                        
                                        <!-- Dynamic Message Status Ticks -->
                                        @if($msg->status === 'read')
                                            <!-- Blue Double Tick -->
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                            </svg>
                                        @elseif($msg->status === 'delivered')
                                            <!-- Gray Double Tick -->
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                            </svg>
                                        @else
                                            <!-- Single Gray Tick (pending/sent) -->
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Footer Send Message Form -->
                <form @submit.prevent="sendMessage('{{ $contact->phone_number }}')" class="h-[62px] bg-[#f0f2f5] px-4 border-t border-gray-200 flex items-center space-x-3 flex-shrink-0">
                    <input type="text" x-model="newMessage" placeholder="Type a message..." :disabled="isSending" class="flex-1 bg-white rounded-lg px-4 py-2 text-sm border-none focus:outline-none focus:ring-1 focus:ring-teal-500 placeholder-gray-500 disabled:bg-gray-100">
                    
                    <button type="submit" :disabled="isSending || !newMessage.trim()" class="bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white rounded-full p-2 focus:outline-none transition flex-shrink-0">
                        <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                        </svg>
                    </button>
                </form>
            @else
                <!-- Placeholder when no contact is selected -->
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-[#f0f2f5] border-b-8 border-teal-600">
                    <div class="w-24 h-24 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 mb-6">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-light text-gray-700 mb-2">WhatsApp Web for Laravel</h2>
                    <p class="text-sm text-gray-500 max-w-md">Select a conversation or click '+' to send a message via your WhatsApp Cloud API.</p>
                </div>
            @endif
        </div>

    </div>

    <!-- ================= ADD NEW CONTACT MODAL ================= -->
    <div x-show="showAddContactModal" x-cloak class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
        <div @click.outside="showAddContactModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Start New Chat</h3>
            
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number (with Country Code)</label>
                        <input type="text" name="phone_number" x-model="newContactPhone" placeholder="e.g. 5561999999999" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showAddContactModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium disabled:opacity-50">Save Contact</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= ALPINE.JS APPLICATION STATE ================= -->
    <script>
        function wmanager() {
            return {
                showAddContactModal: false,
                searchQuery: '',
                newMessage: '',
                newContactPhone: '',
                newContactMessage: '',
                isSending: false,
                apiSecret: '{{ config("services.api.secret_token") }}', 

                init() {
                    this.scrollToBottom();
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
                            // Reload window to fetch newly stored message from DB
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
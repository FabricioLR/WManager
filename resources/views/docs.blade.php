<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - WManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 3px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 h-screen w-screen overflow-hidden font-sans antialiased flex text-slate-100" x-data="{ activeSection: 'overview' }">

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-[280px] lg:w-[320px] bg-slate-900 border-r border-slate-800 flex flex-col h-full flex-shrink-0">
        
        <!-- Sidebar Header -->
        <div class="h-[60px] bg-slate-800/80 px-4 border-b border-slate-700/60 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-md">
                    API
                </div>
                <div>
                    <h1 class="font-semibold text-slate-200 text-sm">REST API Docs</h1>
                    <p class="text-[10px] text-emerald-400 font-mono">v1.0.0</p>
                </div>
            </div>

            <!-- Return Button -->
            <a href="{{ route('chat') }}" title="Return to App" class="p-2 hover:bg-slate-700/60 rounded-full text-slate-400 hover:text-slate-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1 text-xs">
            
            <div class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Getting Started</div>
            
            <button @click="activeSection = 'overview'" 
                    :class="activeSection === 'overview' ? 'bg-slate-800 text-emerald-400 border-l-2 border-emerald-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'" 
                    class="w-full text-left px-3 py-2 rounded-r-lg font-medium transition flex items-center space-x-2">
                <span>Overview & Authentication</span>
            </button>

            <div class="px-3 py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Messages</div>

            <button @click="activeSection = 'send-message'" 
                    :class="activeSection === 'send-message' ? 'bg-slate-800 text-emerald-400 border-l-2 border-emerald-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'" 
                    class="w-full text-left px-3 py-2 rounded-r-lg font-medium transition flex items-center justify-between">
                <span>Send Text Message</span>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">POST</span>
            </button>

            <button @click="activeSection = 'send-template'" 
                    :class="activeSection === 'send-template' ? 'bg-slate-800 text-emerald-400 border-l-2 border-emerald-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'" 
                    class="w-full text-left px-3 py-2 rounded-r-lg font-medium transition flex items-center justify-between">
                <span>Send Template</span>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">POST</span>
            </button>

            <div class="px-3 py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Templates</div>

            <button @click="activeSection = 'get-templates'" 
                    :class="activeSection === 'get-templates' ? 'bg-slate-800 text-emerald-400 border-l-2 border-emerald-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'" 
                    class="w-full text-left px-3 py-2 rounded-r-lg font-medium transition flex items-center justify-between">
                <span>List Templates</span>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">GET</span>
            </button>

            <div class="px-3 py-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Webhooks</div>

            <button @click="activeSection = 'webhook'" 
                    :class="activeSection === 'webhook' ? 'bg-slate-800 text-emerald-400 border-l-2 border-emerald-500' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'" 
                    class="w-full text-left px-3 py-2 rounded-r-lg font-medium transition flex items-center justify-between">
                <span>WhatsApp Webhook</span>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">POST</span>
            </button>
        </nav>
    </aside>

    <!-- Main Content Panel -->
    <main class="flex-1 overflow-y-auto p-6 md:p-10 space-y-12">

        <!-- OVERVIEW & AUTHENTICATION -->
        <section id="overview" x-show="activeSection === 'overview'" class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-100">API Documentation</h2>
                <p class="text-xs text-slate-400 mt-1">Integrate WhatsApp messaging into your business using secure HTTP REST endpoints.</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4 shadow-md">
                <h3 class="text-sm font-semibold text-slate-200">Base URL</h3>
                <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 font-mono text-xs text-emerald-400 flex items-center justify-between">
                    <span>{{ config('app.url') }}/api</span>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4 shadow-md">
                <h3 class="text-sm font-semibold text-slate-200">Authentication</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Protected endpoints require your secret token passed via custom request headers or query parameters (handled by <code class="text-emerald-400 bg-slate-800 px-1 py-0.5 rounded">CheckApiSecretToken</code> middleware).
                </p>
                <div class="bg-slate-950 p-4 rounded-lg border border-slate-800 font-mono text-xs text-slate-300 space-y-2">
                    <div><span class="text-slate-500">Header:</span> X-Api-Secret-Token: <span class="text-amber-400">{{ auth()->user()->api_token }}</span></div>
                </div>
            </div>
        </section>

        <!-- SEND TEXT MESSAGE -->
        <section id="send-message" x-show="activeSection === 'send-message'" class="space-y-6">
            <div class="flex items-center space-x-3">
                <span class="px-2 py-1 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">POST</span>
                <h2 class="text-lg font-bold text-slate-100 font-mono">/api/messages/send</h2>
            </div>
            <p class="text-xs text-slate-400">Queue a plain text message to be dispatched to a WhatsApp user.</p>

            <!-- Parameters Table -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-800/50 border-b border-slate-800 text-xs font-semibold text-slate-300">Request Body Parameters</div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/30 text-slate-400 text-[11px] uppercase border-b border-slate-800">
                        <tr>
                            <th class="p-3">Field</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Required</th>
                            <th class="p-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <tr>
                            <td class="p-3 font-mono text-emerald-400">phone_number</td>
                            <td class="p-3 text-slate-400">string</td>
                            <td class="p-3 text-emerald-400">Yes</td>
                            <td class="p-3 text-slate-300">Recipient's full phone number in E.164 format (e.g., +155501999)</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-mono text-emerald-400">message</td>
                            <td class="p-3 text-slate-400">string</td>
                            <td class="p-3 text-emerald-400">Yes</td>
                            <td class="p-3 text-slate-300">The plain text content to send</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Code Sample -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-800/50 border-b border-slate-800 text-xs font-semibold text-slate-300">Example Response (202 Accepted)</div>
                <pre class="p-4 bg-slate-950 font-mono text-xs text-slate-300 overflow-x-auto"><code>{
  "status": "queued",
  "message": "Message queued for sending.",
  "data": {
    "message_id": 1024,
    "contact_id": 45,
    "status": "pending"
  }
}</code></pre>
            </div>
        </section>

        <!-- SEND TEMPLATE MESSAGE -->
        <section id="send-template" x-show="activeSection === 'send-template'" class="space-y-6">
            <div class="flex items-center space-x-3">
                <span class="px-2 py-1 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">POST</span>
                <h2 class="text-lg font-bold text-slate-100 font-mono">/api/messages/sendTemplate</h2>
            </div>
            <p class="text-xs text-slate-400">Send an approved WhatsApp Meta Template message to initiate business conversations.</p>

            <!-- Parameters Table -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-800/50 border-b border-slate-800 text-xs font-semibold text-slate-300">Request Body Parameters</div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/30 text-slate-400 text-[11px] uppercase border-b border-slate-800">
                        <tr>
                            <th class="p-3">Field</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Required</th>
                            <th class="p-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <tr>
                            <td class="p-3 font-mono text-emerald-400">phone_number</td>
                            <td class="p-3 text-slate-400">string</td>
                            <td class="p-3 text-emerald-400">Yes</td>
                            <td class="p-3 text-slate-300">Recipient's phone number</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-mono text-emerald-400">template_name</td>
                            <td class="p-3 text-slate-400">string</td>
                            <td class="p-3 text-emerald-400">Yes</td>
                            <td class="p-3 text-slate-300">Exact name of approved template in Meta Manager</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-mono text-emerald-400">language_code</td>
                            <td class="p-3 text-slate-400">string</td>
                            <td class="p-3 text-slate-500">No</td>
                            <td class="p-3 text-slate-300">Template language code (Default: <code class="text-amber-400">en_US</code>)</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-mono text-emerald-400">components</td>
                            <td class="p-3 text-slate-400">array</td>
                            <td class="p-3 text-slate-500">No</td>
                            <td class="p-3 text-slate-300">Array of dynamic variables / components for header/body</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Response Sample -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-800/50 border-b border-slate-800 text-xs font-semibold text-slate-300">Example Payload Body</div>
                <pre class="p-4 bg-slate-950 font-mono text-xs text-slate-300 overflow-x-auto"><code>{
  "phone_number": "+155501999",
  "template_name": "welcome_offer",
  "language_code": "en_US",
  "components": [
    {
      "type": "body",
      "parameters": [
        { "type": "text", "text": "John" }
      ]
    }
  ]
}</code></pre>
            </div>
        </section>

        <!-- LIST TEMPLATES -->
        <section id="get-templates" x-show="activeSection === 'get-templates'" class="space-y-6">
            <div class="flex items-center space-x-3">
                <span class="px-2 py-1 rounded text-xs font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">GET</span>
                <h2 class="text-lg font-bold text-slate-100 font-mono">/api/templates</h2>
            </div>
            <p class="text-xs text-slate-400">Fetch approved message templates directly from Meta/WhatsApp Business profile.</p>

            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-800/50 border-b border-slate-800 text-xs font-semibold text-slate-300">Query Parameters</div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/30 text-slate-400 text-[11px] uppercase border-b border-slate-800">
                        <tr>
                            <th class="p-3">Parameter</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Required</th>
                            <th class="p-3">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <tr>
                            <td class="p-3 font-mono text-sky-400">limit</td>
                            <td class="p-3 text-slate-400">integer</td>
                            <td class="p-3 text-slate-500">No</td>
                            <td class="p-3 text-slate-300">Number of template items to retrieve</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-mono text-sky-400">after</td>
                            <td class="p-3 text-slate-400">string</td>
                            <td class="p-3 text-slate-500">No</td>
                            <td class="p-3 text-slate-300">Pagination cursor for subsequent pages</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- WHATSAPP WEBHOOK -->
        <section id="webhook" x-show="activeSection === 'webhook'" class="space-y-6">
            <div class="flex items-center space-x-3">
                <span class="px-2 py-1 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">POST</span>
                <h2 class="text-lg font-bold text-slate-100 font-mono">/api/whatsapp/webhook</h2>
            </div>
            <p class="text-xs text-slate-400">Endpoint configured for WhatsApp Cloud API webhooks. Dispatches incoming events asynchronously to <code class="text-emerald-400 bg-slate-800 px-1 py-0.5 rounded">ProcessWebhookPayload</code> job queue.</p>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 space-y-2">
                <div class="text-xs font-semibold text-slate-300">Middleware Verification</div>
                <p class="text-xs text-slate-400">Protected by <code class="text-emerald-400">ValidateWhatsAppWebhook</code> middleware to verify payload signature and Meta challenge requests.</p>
            </div>
        </section>

    </main>
</body>
</html>
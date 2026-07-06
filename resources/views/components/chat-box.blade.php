@props(['ordonnance', 'currentUser'])

<div x-data="chatComponent('{{ route('messages.index', $ordonnance) }}', '{{ route('messages.store', $ordonnance) }}', {{ $currentUser->id }})" 
     class="card flex flex-col mt-6" style="height: 500px; border: 1px solid #e2e8f0;">
    
    <!-- Chat Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 16px 16px 0 0;">
        <div class="flex items-center gap-3">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #0f766e; display: flex; align-items: center; justify-content: center; color: white;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Discussion</h3>
                <p class="text-xs text-slate-500">
                    {{ $currentUser->id === $ordonnance->client_id ? 'Avec votre pharmacien' : 'Avec le patient' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 p-6 overflow-y-auto bg-slate-50" id="chat-messages" style="display: flex; flex-direction: column; gap: 1rem;">
        
        <template x-if="loading && messages.length === 0">
            <div class="flex justify-center items-center h-full">
                <svg class="animate-spin h-6 w-6 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </template>

        <template x-for="message in messages" :key="message.id">
            <div class="flex flex-col" :class="message.sender_id === currentUserId ? 'items-end' : 'items-start'">
                <div style="max-width: 80%; padding: 10px 14px; border-radius: 12px; font-size: 0.875rem;"
                     :style="message.sender_id === currentUserId ? 'background-color: #0f766e; color: white; border-bottom-right-radius: 2px;' : 'background-color: white; color: #334155; border: 1px solid #e2e8f0; border-bottom-left-radius: 2px;'">
                    <p x-text="message.content" style="white-space: pre-wrap; word-break: break-word;"></p>
                </div>
                <div class="text-[10px] text-slate-400 mt-1 px-1 flex gap-2">
                    <span x-text="formatDate(message.created_at)"></span>
                    <template x-if="message.sender_id === currentUserId && message.read_at">
                        <span class="text-teal-600">✓ Lu</span>
                    </template>
                </div>
            </div>
        </template>
        
        <template x-if="messages.length === 0 && !loading">
            <div class="text-center text-slate-500 text-sm mt-10">
                Aucun message pour le moment.
            </div>
        </template>
        
        <!-- Empty element to scroll to bottom -->
        <div x-ref="bottom"></div>
    </div>

    <!-- Input Area -->
    <div class="p-4 border-t border-slate-100 bg-white" style="border-radius: 0 0 16px 16px;">
        <form @submit.prevent="sendMessage" class="flex gap-2">
            <input type="text" x-model="newMessage" placeholder="Écrivez votre message..." 
                   class="input flex-1 bg-slate-50 border-slate-200 focus:bg-white" 
                   :disabled="sending" required>
            
            <button type="submit" class="btn btn-primary px-6" :disabled="sending || !newMessage.trim()">
                <template x-if="!sending">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </template>
                <template x-if="sending">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatComponent', (fetchUrl, postUrl, currentUserId) => ({
            messages: [],
            newMessage: '',
            loading: true,
            sending: false,
            currentUserId: currentUserId,
            interval: null,
            
            init() {
                this.fetchMessages();
                // Polling toutes les 5 secondes
                this.interval = setInterval(() => {
                    this.fetchMessages(false);
                }, 5000);
            },
            
            destroy() {
                if(this.interval) clearInterval(this.interval);
            },
            
            fetchMessages(scrollToBottom = true) {
                fetch(fetchUrl, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    const oldLength = this.messages.length;
                    this.messages = data;
                    this.loading = false;
                    
                    if (scrollToBottom && data.length > oldLength) {
                        setTimeout(() => this.$refs.bottom.scrollIntoView({ behavior: 'smooth' }), 100);
                    }
                })
                .catch(err => console.error("Erreur chargement messages:", err));
            },
            
            sendMessage() {
                if (!this.newMessage.trim()) return;
                
                this.sending = true;
                
                fetch(postUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: this.newMessage })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    this.messages.push(data);
                    this.newMessage = '';
                    setTimeout(() => this.$refs.bottom.scrollIntoView({ behavior: 'smooth' }), 100);
                })
                .catch(err => console.error("Erreur envoi message:", err))
                .finally(() => {
                    this.sending = false;
                });
            },
            
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
        }));
    });
</script>
@endpush

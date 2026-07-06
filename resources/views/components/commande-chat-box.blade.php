@props(['commande'])

<div class="card p-0 flex flex-col" style="height: 600px;" x-data="commandeChat({{ $commande->id }}, {{ auth()->id() }})" @new-message.window="fetchMessages">
    <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <div>
            <h2 style="font-size: 1rem; font-weight: 600; color: #0f172a;">Messagerie</h2>
            <p style="font-size: 0.75rem; color: #64748b;">Discutez à propos de cette commande</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-500"></span>
            </span>
            <span class="text-xs text-teal-600 font-medium">En direct</span>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50" id="chat-messages-container" x-ref="messagesContainer">
        
        <template x-if="loading">
            <div class="flex justify-center py-4">
                <svg class="animate-spin h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </template>

        <template x-if="!loading && messages.length === 0">
            <div class="text-center py-8">
                <p class="text-sm text-slate-500">Aucun message pour le moment.</p>
                <p class="text-xs text-slate-400 mt-1">Commencez la conversation !</p>
            </div>
        </template>

        <template x-for="msg in messages" :key="msg.id">
            <div class="flex w-full" :class="msg.sender_id === userId ? 'justify-end' : 'justify-start'">
                <div class="max-w-[85%] rounded-2xl px-4 py-2"
                     :class="msg.sender_id === userId ? 'bg-teal-600 text-white rounded-br-none' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-none shadow-sm'">
                    <p class="text-xs font-semibold mb-1 opacity-75" x-text="msg.sender_id === userId ? 'Vous' : msg.sender.name"></p>
                    <p class="text-sm" style="white-space: pre-wrap;" x-text="msg.content"></p>
                    <div class="text-right mt-1">
                        <span class="text-[0.65rem] opacity-60" x-text="formatTime(msg.created_at)"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="p-3 border-t border-slate-100 bg-white">
        <form @submit.prevent="sendMessage" class="flex items-end gap-2">
            <textarea x-model="newMessage" 
                      class="input flex-1 resize-none" 
                      rows="1" 
                      placeholder="Écrivez votre message..." 
                      @keydown.enter.prevent="if(!event.shiftKey) sendMessage()"
                      style="min-height: 44px; max-height: 120px; border-radius: 20px; padding-top: 10px;"></textarea>
            <button type="submit" 
                    class="btn btn-primary h-[44px] w-[44px] p-0 flex items-center justify-center rounded-full shrink-0"
                    :disabled="isSending || newMessage.trim() === ''"
                    style="border-radius: 50%;">
                <svg x-show="!isSending" class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <svg x-show="isSending" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('commandeChat', (commandeId, userId) => ({
            commandeId: commandeId,
            userId: userId,
            messages: [],
            newMessage: '',
            loading: true,
            isSending: false,
            pollInterval: null,

            init() {
                this.fetchMessages();
                
                // Polling toutes les 5 secondes
                this.pollInterval = setInterval(() => {
                    this.fetchMessages(false);
                }, 5000);

                // Nettoyage
                this.$cleanup(() => clearInterval(this.pollInterval));
            },

            async fetchMessages(showLoading = true) {
                if (showLoading && this.messages.length === 0) this.loading = true;
                
                try {
                    const response = await fetch(`/commandes/${this.commandeId}/messages`);
                    const data = await response.json();
                    
                    // Vérifier s'il y a de nouveaux messages
                    const isNewMessage = this.messages.length !== data.length;
                    this.messages = data;
                    
                    if (isNewMessage) {
                        this.scrollToBottom();
                    }
                } catch (error) {
                    console.error('Erreur chargement messages', error);
                } finally {
                    this.loading = false;
                }
            },

            async sendMessage() {
                if (this.newMessage.trim() === '' || this.isSending) return;
                
                this.isSending = true;
                const content = this.newMessage;
                this.newMessage = ''; // Reset optimiste
                
                try {
                    const response = await fetch(`/commandes/${this.commandeId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ content: content })
                    });
                    
                    if (response.ok) {
                        const newMsg = await response.json();
                        this.messages.push(newMsg);
                        this.scrollToBottom();
                    } else {
                        // Restore message on failure
                        this.newMessage = content;
                    }
                } catch (error) {
                    console.error('Erreur envoi message', error);
                    this.newMessage = content;
                } finally {
                    this.isSending = false;
                }
            },

            scrollToBottom() {
                setTimeout(() => {
                    if (this.$refs.messagesContainer) {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                    }
                }, 100);
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute:'2-digit' });
            }
        }));
    });
</script>
@endpush

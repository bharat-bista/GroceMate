@php
    $starterPrompts = config('chatbot.ui.starter_prompts', []);
@endphp

<div
    x-data="groceMateAdminChatbot({
        endpoint: @js(route('inventory.chatbot.message')),
        csrfToken: @js(csrf_token()),
        storageKey: @js(config('chatbot.ui.storage_key')),
        starterPrompts: @js($starterPrompts),
    })"
    x-init="init()"
>
    <!--
        Sticky launcher button:
        This stays visible on every admin page that uses the inventory layout.
        Clicking it opens the slide-over chatbot panel.
    -->
    <button
        type="button"
        @click="togglePanel"
        class="fixed bottom-6 right-6 z-[80] inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-900 text-white shadow-2xl ring-4 ring-white transition hover:-translate-y-1 hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300"
        style="position: fixed; right: 24px; bottom: 24px; width: 64px; height: 64px; z-index: 80; display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; background: #0f172a; color: #ffffff;"
        aria-label="Open admin chatbot"
        title="Open admin chatbot"
    >
        <span
            class="text-base font-bold uppercase tracking-[0.2em] text-white"
            style="display: inline-block; font-size: 15px; font-weight: 700; letter-spacing: 0.2em; line-height: 1; color: #ffffff;"
        >
            AI
        </span>
    </button>

    <!--
        Overlay:
        A small overlay helps the panel feel modal on mobile and keeps focus on
        the conversation without affecting the rest of the admin UI.
    -->
    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[70] bg-slate-950/30"
        style="position: fixed; inset: 0; z-index: 70;"
        @click="closePanel"
    ></div>

    <!--
        Slide-over panel:
        This is the actual chatbot UI. It uses Alpine state plus a Laravel POST
        request to talk with the backend controller.
    -->
    <section
        x-show="open"
        x-cloak
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[90] flex w-full max-w-md flex-col border-l border-slate-200 bg-white shadow-2xl"
        style="position: fixed; top: 0; right: 0; bottom: 0; width: 100%; max-width: 28rem; z-index: 90;"
        aria-label="Admin chatbot panel"
    >
        <header class="border-b border-slate-200 bg-slate-900 px-5 py-4 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-300">GroceMate AI</p>
                    <h2 class="mt-1 text-lg font-semibold">Admin Assistant</h2>
                    <p class="mt-1 text-sm text-slate-300">
                        Ask about low stock, expiry alerts, supplier counts, calculations, or Vanna-backed analytics.
                    </p>
                </div>

                <button
                    type="button"
                    @click="closePanel"
                    class="rounded-lg p-2 text-slate-300 transition hover:bg-slate-800 hover:text-white"
                    aria-label="Close chatbot"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
        </header>

        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Quick prompts</p>

            <div class="mt-3 flex flex-wrap gap-2">
                <template x-for="prompt in starterPrompts" :key="prompt">
                    <button
                        type="button"
                        @click="sendPrompt(prompt)"
                        class="rounded-full border border-slate-200 bg-white px-3 py-2 text-left text-xs font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-100"
                        x-text="prompt"
                    ></button>
                </template>
            </div>
        </div>

        <div class="flex-1 space-y-4 overflow-y-auto bg-white px-5 py-5" x-ref="messageContainer">
            <template x-if="messages.length === 0">
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                    Start with a quick question such as "Which products are low stock?" or "Calculate 2450 + 18%".
                </div>
            </template>

            <template x-for="(item, index) in messages" :key="`${item.role}-${index}-${item.text}`">
                <article class="flex" :class="item.role === 'user' ? 'justify-end' : 'justify-start'">
                    <div
                        class="max-w-[88%] rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm"
                        :class="item.role === 'user'
                            ? 'bg-slate-900 text-white'
                            : 'border border-slate-200 bg-slate-50 text-slate-800'"
                    >
                        <p class="whitespace-pre-line" x-text="item.text"></p>

                        <template x-if="item.source">
                            <p
                                class="mt-2 text-[11px] uppercase tracking-[0.18em]"
                                :class="item.role === 'user' ? 'text-slate-300' : 'text-slate-500'"
                                x-text="item.source"
                            ></p>
                        </template>
                    </div>
                </article>
            </template>

            <template x-if="loading">
                <div class="flex justify-start">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 shadow-sm">
                        Thinking...
                    </div>
                </div>
            </template>
        </div>

        <footer class="border-t border-slate-200 bg-white px-5 py-4">
            <div class="mb-3 flex items-center justify-between text-xs text-slate-500">
                

                <button
                    type="button"
                    @click="clearMessages"
                    class="font-medium text-slate-700 transition hover:text-slate-900"
                >
                    Clear history
                </button>
            </div>

            <form @submit.prevent="submitMessage" class="space-y-3">
                <label class="sr-only" for="admin-chatbot-message">Ask the admin chatbot</label>

                <textarea
                    id="admin-chatbot-message"
                    x-model="draft"
                    rows="3"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    placeholder="Ask a question about stock, expiry, suppliers, or calculations..."
                ></textarea>

                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs leading-5 text-slate-500">
                        The widget stores chat history in your browser for convenience.
                    </p>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="loading || !draft.trim()"
                    >
                        Send
                    </button>
                </div>
            </form>
        </footer>
    </section>
</div>

<script>
    /*
     * Alpine component factory for the sticky admin chatbot.
     *
     * Why a factory?
     * - It keeps the Blade file readable.
     * - It gives us a reusable, well-contained state object.
     * - It makes each method easier to explain and maintain.
     */
    function groceMateAdminChatbot(config) {
        return {
            open: false,
            loading: false,
            draft: '',
            messages: [],
            endpoint: config.endpoint,
            csrfToken: config.csrfToken,
            storageKey: config.storageKey,
            starterPrompts: config.starterPrompts || [],

            /*
             * Restore previous chat messages from localStorage so the admin does
             * not lose context after a refresh.
             */
            init() {
                this.restoreMessages();
            },

            togglePanel() {
                this.open = !this.open;

                if (this.open) {
                    this.scrollMessagesToBottom();
                }
            },

            closePanel() {
                this.open = false;
            },

            /*
             * Submit the current text-area message to Laravel.
             */
            async submitMessage() {
                const message = this.draft.trim();

                if (!message || this.loading) {
                    return;
                }

                this.draft = '';
                await this.sendMessage(message);
            },

            /*
             * Reuse the same send pipeline for starter prompts and manual input.
             */
            async sendPrompt(prompt) {
                this.open = true;
                await this.sendMessage(prompt);
            },

            async sendMessage(message) {
                this.pushMessage({
                    role: 'user',
                    text: message,
                    source: 'you',
                });

                this.loading = true;
                this.open = true;
                this.scrollMessagesToBottom();

                try {
                    const response = await fetch(this.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ message }),
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        const firstValidationError = payload?.errors
                            ? Object.values(payload.errors).flat()[0]
                            : null;

                        throw new Error(firstValidationError || payload?.message || 'The chatbot request failed.');
                    }

                    this.pushMessage({
                        role: 'assistant',
                        text: payload.answer || 'The chatbot returned an empty response.',
                        source: payload.source || 'assistant',
                    });
                } catch (error) {
                    this.pushMessage({
                        role: 'assistant',
                        text: error.message || 'Something went wrong while contacting the chatbot.',
                        source: 'error',
                    });
                } finally {
                    this.loading = false;
                    this.scrollMessagesToBottom();
                }
            },

            /*
             * Store every chat item in memory and browser storage.
             */
            pushMessage(message) {
                this.messages.push(message);
                this.persistMessages();
            },

            clearMessages() {
                this.messages = [];
                localStorage.removeItem(this.storageKey);
            },

            persistMessages() {
                localStorage.setItem(this.storageKey, JSON.stringify(this.messages));
            },

            restoreMessages() {
                const savedMessages = localStorage.getItem(this.storageKey);

                if (!savedMessages) {
                    return;
                }

                try {
                    this.messages = JSON.parse(savedMessages);
                } catch (error) {
                    this.messages = [];
                    localStorage.removeItem(this.storageKey);
                }
            },

            /*
             * Keep the newest reply visible without forcing the admin to scroll.
             */
            scrollMessagesToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.messageContainer;

                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },
        };
    }
</script>

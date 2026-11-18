<section aria-label="AI chat" class="fixed bottom-24 right-4 z-50 flex h-[min(620px,calc(100vh-120px))] w-[min(400px,calc(100vw-32px))] flex-col overflow-hidden rounded-2xl border border-[#dce2da] bg-white shadow-[0_16px_50px_rgba(25,61,45,0.2)] sm:right-6" id="ai-chat-panel" role="dialog" aria-labelledby="ai-chat-title" aria-describedby="ai-chat-status" style="display: none" hidden>
    <header class="flex items-center justify-between gap-3 bg-[#193d2d] px-5 py-4 text-white">
        <div class="flex min-w-0 items-center gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12" />
                    <circle cx="12" cy="12" r="4" />
                </svg>
            </span>
            <div class="min-w-0">
                <h2 id="ai-chat-title" class="m-0 truncate text-sm font-bold">Wellbeing assistant</h2>
                <p id="ai-chat-status" class="mb-0 mt-1 flex items-center gap-1.5 text-xs text-white/75">
                    <span class="h-2 w-2 rounded-full bg-[#9fe0a8]" aria-hidden="true"></span>
                    Local Ollama test chat
                </p>
            </div>
        </div>
        <button id="ai-chat-close" class="grid h-9 w-9 shrink-0 cursor-pointer place-items-center rounded-full border-0 bg-transparent text-white/80 hover:bg-white/10 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-white" type="button" aria-label="Close chat">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12" /></svg>
        </button>
    </header>

    <div id="ai-chat-scroll-area" class="flex-1 overflow-y-auto bg-[#fbfcfa] px-5 py-6">
        <p class="mb-5 text-center text-[11px] font-medium text-[#879188]">A little support for your wellbeing</p>
        <div id="ai-chat-messages" class="space-y-4" role="log" aria-live="polite" aria-label="Chat messages">
            <div class="flex items-start gap-2.5" data-chat-role="assistant">
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#e5f1e4] text-[#28603e]" aria-hidden="true">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12" />
                        <circle cx="12" cy="12" r="4" />
                    </svg>
                </span>
                <div class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-4 py-3 text-[13px] leading-[1.6] text-[#26372d] shadow-sm">
                    <p class="m-0">Hi there! I’m here to help you reflect on your health goals and progress.</p>
                    <p class="mb-0 mt-2">What would you like to focus on today?</p>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-[#e8ece6] bg-white p-4">
        <form id="ai-chat-form">
            <div class="flex items-end gap-2 rounded-xl border border-[#dce2da] bg-[#fbfcfa] p-2">
                <textarea id="ai-chat-input" class="min-h-10 flex-1 resize-none border-0 bg-transparent px-2 py-2 text-sm text-[#26372d] outline-none placeholder:text-[#879188] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#9fcba9]" rows="1" maxlength="1000" placeholder="Ask a question..." aria-label="Message the wellbeing assistant"></textarea>
                <button id="ai-chat-send" class="grid h-10 w-10 shrink-0 cursor-pointer place-items-center rounded-lg border-0 bg-[#28603e] text-white hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#9fcba9] disabled:cursor-not-allowed disabled:opacity-50" type="submit" aria-label="Send message">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z" /><path d="M22 2 11 13" /></svg>
                </button>
            </div>
            <p id="ai-chat-error" class="mb-0 mt-2 text-center text-xs text-[#832e28]" role="alert" hidden></p>
            <p class="mb-0 mt-2 text-center text-[10px] text-[#879188]">Replies come from your local Ollama model.</p>
        </form>
    </div>
</section>
<button id="ai-chat-launcher" class="fixed bottom-5 right-5 z-50 inline-flex min-h-14 cursor-pointer items-center gap-2.5 rounded-full border-0 bg-[#28603e] px-5 text-sm font-bold text-white shadow-[0_8px_24px_rgba(25,61,45,0.28)] transition hover:-translate-y-0.5 hover:bg-[#1d4d31] focus-visible:outline focus-visible:outline-[3px] focus-visible:outline-[#9fcba9] focus-visible:outline-offset-[3px] sm:right-6" type="button" aria-controls="ai-chat-panel" aria-expanded="false">
    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12" />
        <circle cx="12" cy="12" r="4" />
    </svg>
    Chat with AI
</button>

<script>
(() => {
    const chatLauncher = document.getElementById('ai-chat-launcher');
    const chatPanel = document.getElementById('ai-chat-panel');
    const chatCloseButton = document.getElementById('ai-chat-close');
    const chatForm = document.getElementById('ai-chat-form');
    const chatInput = document.getElementById('ai-chat-input');
    const chatSendButton = document.getElementById('ai-chat-send');
    const chatMessages = document.getElementById('ai-chat-messages');
    const chatScrollArea = document.getElementById('ai-chat-scroll-area');
    const chatError = document.getElementById('ai-chat-error');
    let conversation = [{
        role: 'assistant',
        content: 'Hi there! I’m here to help you reflect on your health goals and progress. What would you like to focus on today?'
    }];

    const setChatOpen = (isOpen) => {
        chatPanel.hidden = !isOpen;
        chatPanel.style.display = isOpen ? 'flex' : 'none';
        chatLauncher.hidden = isOpen;
        chatLauncher.style.display = isOpen ? 'none' : 'inline-flex';
        chatLauncher.setAttribute('aria-expanded', String(isOpen));
        (isOpen ? chatCloseButton : chatLauncher).focus();
    };

    const appendMessage = (role, content) => {
        const row = document.createElement('div');
        row.dataset.chatRole = role;
        row.className = role === 'user'
            ? 'flex justify-end'
            : 'flex items-start gap-2.5';

        if (role === 'assistant') {
            const avatar = document.createElement('span');
            avatar.className = 'grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#e5f1e4] text-[10px] font-bold text-[#28603e]';
            avatar.textContent = 'AI';
            row.append(avatar);
        }

        const bubble = document.createElement('div');
        bubble.className = role === 'user'
            ? 'max-w-[85%] rounded-2xl rounded-tr-md bg-[#28603e] px-4 py-3 text-[13px] leading-[1.6] text-white'
            : 'max-w-[85%] rounded-2xl rounded-tl-md bg-white px-4 py-3 text-[13px] leading-[1.6] text-[#26372d] shadow-sm';
        bubble.textContent = content;
        row.append(bubble);
        chatMessages.append(row);
        chatScrollArea.scrollTop = chatScrollArea.scrollHeight;
    };

    chatLauncher.addEventListener('click', () => setChatOpen(true));
    chatCloseButton.addEventListener('click', () => setChatOpen(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !chatPanel.hidden) {
            setChatOpen(false);
        }
    });

    chatInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            chatForm.requestSubmit();
        }
    });

    chatForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const content = chatInput.value.trim();
        if (!content || chatSendButton.disabled) {
            return;
        }

        chatError.hidden = true;
        chatError.textContent = '';
        appendMessage('user', content);
        chatInput.value = '';
        chatSendButton.disabled = true;

        const requestMessages = [...conversation, { role: 'user', content }].slice(-12);
        const thinkingMessage = document.createElement('p');
        thinkingMessage.className = 'ml-10 text-xs text-[#68756c]';
        thinkingMessage.setAttribute('role', 'status');
        thinkingMessage.textContent = 'Thinking...';
        chatMessages.append(thinkingMessage);
        chatScrollArea.scrollTop = chatScrollArea.scrollHeight;

        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'chat', messages: requestMessages }),
                signal: AbortSignal.timeout(120000)
            });
            let data;
            try {
                data = await response.json();
            } catch {
                throw new Error('The chat service returned an invalid response.');
            }
            if (!data || typeof data !== 'object') {
                throw new Error('The chat service returned an invalid response.');
            }
            if (!response.ok) {
                throw new Error(data.error || 'Unable to get a reply from Ollama.');
            }
            if (typeof data.reply !== 'string' || data.reply.trim() === '') {
                throw new Error('Ollama returned an empty reply.');
            }

            conversation = [...requestMessages, { role: 'assistant', content: data.reply }];
            appendMessage('assistant', data.reply);
        } catch (error) {
            chatError.textContent = error.name === 'TimeoutError'
                ? 'Ollama took too long to reply. Please try again.'
                : error.message || 'Unable to reach the local AI. Check that Ollama is running.';
            chatError.hidden = false;
        } finally {
            thinkingMessage.remove();
            chatSendButton.disabled = false;
            chatInput.focus();
            chatScrollArea.scrollTop = chatScrollArea.scrollHeight;
        }
    });
})();
</script>
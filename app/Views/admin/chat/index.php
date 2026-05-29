<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Messages</h1>
            <p class="text-muted-foreground">Chat with organizations</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Inbox Sidebar -->
        <div class="rounded-xl border bg-card shadow overflow-hidden md:col-span-1">
            <div class="p-4 border-b border-white/5 bg-muted/30">
                <h3 class="font-semibold text-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Inbox
                </h3>
            </div>
            <div id="inbox-list" class="divide-y divide-white/5 max-h-[600px] overflow-y-auto">
                <?php if ($inbox): ?>
                    <?php foreach ($inbox as $item): ?>
                        <a href="#" data-org-id="<?= $item['id'] ?>"
                            class="inbox-item flex items-center gap-3 p-4 hover:bg-muted/30 transition-colors <?= ($item['last_message'] ?? false) ? '' : 'opacity-50' ?>">
                            <div class="w-10 h-10 rounded-full bg-blue-600/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-blue-400"><?= esc(mb_substr($item['acronym'] ?: $item['name'], 0, 2)) ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium truncate"><?= esc($item['name']) ?></span>
                                    <?php if ($item['unread_count'] > 0): ?>
                                        <span class="ml-2 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold flex-shrink-0"><?= $item['unread_count'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-muted-foreground truncate mt-0.5"><?= esc($item['last_message'] ?? 'No messages yet') ?></p>
                                <p class="text-[10px] text-muted-foreground mt-0.5"><?= $item['last_message_time'] ? date('M d, h:i A', strtotime($item['last_message_time'])) : '' ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-6 text-center text-sm text-muted-foreground">No organizations found</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Conversation Pane -->
        <div class="rounded-xl border bg-card shadow overflow-hidden md:col-span-2 flex flex-col">
            <div id="chat-header" class="p-4 border-b border-white/5 bg-muted/30 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Select an organization to start chatting</span>
            </div>
            <div id="chat-messages" class="flex-1 p-6 space-y-4 overflow-y-auto min-h-[400px] max-h-[500px] bg-muted/10">
                <div class="flex items-center justify-center h-full">
                    <p class="text-sm text-muted-foreground">Click an organization from the inbox to view their messages</p>
                </div>
            </div>
            <div id="chat-input-wrapper" class="p-4 border-t border-white/5 hidden">
                <form id="chat-form" class="flex gap-3">
                    <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off"
                        class="flex-1 h-10 rounded-md border border-white/10 bg-background text-foreground px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="submit"
                        class="inline-flex items-center h-10 px-4 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= csrf_header() ?>';
    let currentCsrfHash = '<?= csrf_hash() ?>';
    let activeOrgId = null;
    let lastPollTime = null;
    let pollInterval = null;
    const userId = <?= session()->get('user_id') ?>;

    // Load conversation
    document.querySelectorAll('.inbox-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const orgId = this.dataset.orgId;
            activeOrgId = orgId;

            // Highlight active
            document.querySelectorAll('.inbox-item').forEach(el => el.classList.remove('bg-blue-600/5', 'border-l-2', 'border-l-blue-500'));
            this.classList.add('bg-blue-600/5', 'border-l-2', 'border-l-blue-500');

            loadConversation(orgId);
        });
    });

    function loadConversation(orgId) {
        fetch('<?= base_url('admin/chat') ?>/' + orgId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: currentCsrfHash }
        })
        .then(r => r.json())
        .then(data => {
            currentCsrfHash = data.csrf;
            const header = document.getElementById('chat-header');
            header.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-blue-600/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-blue-400">${data.organization.acronym ? data.organization.acronym.substring(0, 2) : data.organization.name.substring(0, 2)}</span>
                </div>
                <span class="text-sm font-medium">${data.organization.name}</span>
            `;

            const container = document.getElementById('chat-messages');
            container.innerHTML = '';
            if (data.messages.length === 0) {
                container.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-sm text-muted-foreground">No messages yet. Start the conversation!</p></div>';
            } else {
                data.messages.forEach(msg => appendMessage(msg));
            }

            document.getElementById('chat-input-wrapper').classList.remove('hidden');
            container.scrollTop = container.scrollHeight;

            lastPollTime = data.messages.length > 0 ? data.messages[data.messages.length - 1].created_at : new Date().toISOString().replace('T', ' ').substring(0, 19);

            // Start polling
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => pollMessages(orgId), 3000);

            // Update unread badge
            updateInboxUnread();
        });
    }

    function appendMessage(msg) {
        const container = document.getElementById('chat-messages');
        const isMine = parseInt(msg.sender_id) === userId;
        const div = document.createElement('div');
        div.className = `flex ${isMine ? 'justify-end' : 'justify-start'} message-item`;
        div.dataset.msgId = msg.id;
        div.innerHTML = `
            <div class="max-w-[70%] ${isMine ? 'bg-blue-600 text-white' : 'bg-muted text-foreground'} rounded-xl px-4 py-2.5">
                <p class="text-sm">${escHtml(msg.message)}</p>
                <p class="text-[10px] ${isMine ? 'text-blue-200' : 'text-muted-foreground'} mt-1 text-right">
                    ${msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''}
                    ${msg.is_read && isMine ? '<span class="ml-1">✓✓</span>' : ''}
                </p>
            </div>
        `;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function pollMessages(orgId) {
        if (!orgId) return;
        let url = '<?= base_url('admin/chat/poll') ?>/' + orgId;
        if (lastPollTime) url += '?since=' + encodeURIComponent(lastPollTime);

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: currentCsrfHash }
        })
        .then(r => r.json())
        .then(data => {
            currentCsrfHash = data.csrf;
            if (data.messages && data.messages.length > 0) {
                const existingIds = new Set();
                document.querySelectorAll('.message-item').forEach(el => {
                    if (el.dataset.msgId) existingIds.add(parseInt(el.dataset.msgId));
                });

                data.messages.forEach(msg => {
                    if (!existingIds.has(msg.id)) {
                        appendMessage(msg);
                        existingIds.add(msg.id);
                    }
                });

                if (data.messages.length > 0) {
                    lastPollTime = data.messages[data.messages.length - 1].created_at;
                }
            }

            // Update inbox sidebar
            if (data.inbox) {
                updateInboxList(data.inbox);
            }
        });
    }

    function updateInboxList(inbox) {
        const list = document.getElementById('inbox-list');
        list.innerHTML = '';
        inbox.forEach(item => {
            const isActive = parseInt(item.id) === parseInt(activeOrgId);
            const div = document.createElement('a');
            div.href = '#';
            div.dataset.orgId = item.id;
            div.className = `inbox-item flex items-center gap-3 p-4 hover:bg-muted/30 transition-colors ${isActive ? 'bg-blue-600/5 border-l-2 border-l-blue-500' : ''} ${item.last_message ? '' : 'opacity-50'}`;
            div.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-blue-600/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-blue-400">${(item.acronym || item.name).substring(0, 2)}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium truncate">${escHtml(item.name)}</span>
                        ${item.unread_count > 0 && !isActive ? `<span class="ml-2 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold flex-shrink-0">${item.unread_count}</span>` : ''}
                    </div>
                    <p class="text-xs text-muted-foreground truncate mt-0.5">${escHtml(item.last_message || 'No messages yet')}</p>
                    <p class="text-[10px] text-muted-foreground mt-0.5">${item.last_message_time ? new Date(item.last_message_time).toLocaleDateString() : ''}</p>
                </div>
            `;
            div.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.inbox-item').forEach(el => el.classList.remove('bg-blue-600/5', 'border-l-2', 'border-l-blue-500'));
                this.classList.add('bg-blue-600/5', 'border-l-2', 'border-l-blue-500');
                activeOrgId = this.dataset.orgId;
                loadConversation(activeOrgId);
            });
            list.appendChild(div);
        });
    }

    function updateInboxUnread() {
        fetch('<?= base_url('admin/chat') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: currentCsrfHash }
        })
        .then(r => r.text())
        .then(() => {});
    }

    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        if (!message || !activeOrgId) return;

        fetch('<?= base_url('admin/chat/send') ?>/' + activeOrgId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                [csrfHeaderName]: currentCsrfHash
            },
            body: new URLSearchParams({ 'message': message, '<?= csrf_token() ?>': currentCsrfHash })
        })
        .then(r => r.json())
        .then(data => {
            currentCsrfHash = data.csrf;
            if (data.success) {
                input.value = '';
            }
        });
    });

    function escHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
</script>
<?= $this->endSection() ?>

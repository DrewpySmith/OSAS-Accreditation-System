<?= $this->extend('layouts/org_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Messages</h1>
            <p class="text-muted-foreground">Chat with the administration</p>
        </div>
    </div>

    <div class="rounded-xl border bg-card shadow overflow-hidden flex flex-col">
        <div id="chat-header" class="p-4 border-b border-white/5 bg-muted/30 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-green-600/10 border border-green-500/20 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="text-sm font-medium">Admin</span>
            <span class="text-xs text-muted-foreground">USG Administration</span>
        </div>

        <div id="chat-messages" class="flex-1 p-6 space-y-4 overflow-y-auto min-h-[450px] max-h-[550px] bg-muted/10">
            <?php if ($messages): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="flex <?= $msg['sender_id'] == session()->get('user_id') ? 'justify-end' : 'justify-start' ?> message-item" data-msg-id="<?= $msg['id'] ?>">
                        <div class="max-w-[70%] <?= $msg['sender_id'] == session()->get('user_id') ? 'bg-green-600 text-white' : 'bg-muted text-foreground' ?> rounded-xl px-4 py-2.5">
                            <p class="text-sm"><?= esc($msg['message']) ?></p>
                            <p class="text-[10px] <?= $msg['sender_id'] == session()->get('user_id') ? 'text-green-200' : 'text-muted-foreground' ?> mt-1 text-right">
                                <?= date('h:i A', strtotime($msg['created_at'])) ?>
                                <?php if ($msg['is_read'] && $msg['sender_id'] == session()->get('user_id')): ?>
                                    <span class="ml-1">✓✓</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="flex items-center justify-center h-full">
                    <p class="text-sm text-muted-foreground">No messages yet. Start a conversation with the administration.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="p-4 border-t border-white/5">
            <form id="chat-form" class="flex gap-3">
                <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off"
                    class="flex-1 h-10 rounded-md border border-white/10 bg-background text-foreground px-3 text-sm focus:outline-none focus:ring-1 focus:ring-green-500">
                <button type="submit"
                    class="inline-flex items-center h-10 px-4 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= csrf_header() ?>';
    let currentCsrfHash = '<?= csrf_hash() ?>';
    let lastPollTime = <?= $messages ? "'" . end($messages)['created_at'] . "'" : 'null' ?>;
    const userId = <?= session()->get('user_id') ?>;

    function appendMessage(msg) {
        const container = document.getElementById('chat-messages');
        const isMine = parseInt(msg.sender_id) === userId;

        const div = document.createElement('div');
        div.className = `flex ${isMine ? 'justify-end' : 'justify-start'} message-item`;
        div.dataset.msgId = msg.id;
        div.innerHTML = `
            <div class="max-w-[70%] ${isMine ? 'bg-green-600 text-white' : 'bg-muted text-foreground'} rounded-xl px-4 py-2.5">
                <p class="text-sm">${escHtml(msg.message)}</p>
                <p class="text-[10px] ${isMine ? 'text-green-200' : 'text-muted-foreground'} mt-1 text-right">
                    ${msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : ''}
                    ${msg.is_read && isMine ? '<span class="ml-1">\u2713\u2713</span>' : ''}
                </p>
            </div>
        `;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function poll() {
        let url = '<?= base_url('organization/chat/poll') ?>';
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
        });
    }

    setInterval(poll, 3000);

    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        if (!message) return;

        fetch('<?= base_url('organization/chat/send') ?>', {
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

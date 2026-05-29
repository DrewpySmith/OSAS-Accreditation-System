<?= $this->extend('layouts/org_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Announcements</h1>
            <p class="text-muted-foreground">Latest announcements from the administration</p>
        </div>
        <?php if ($unread_count > 0): ?>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/10 border border-red-500/20 text-red-400">
                <?= $unread_count ?> unread
            </span>
        <?php endif; ?>
    </div>

    <?php if ($announcements): ?>
        <div class="space-y-3">
            <?php foreach ($announcements as $a): ?>
                <?php
                    $readModel = new \App\Models\AnnouncementReadModel();
                    $isRead = $readModel->isRead($a['id'], session()->get('organization_id'));
                ?>
                <div class="rounded-xl border transition-all cursor-pointer hover:border-white/20 hover:translate-x-0.5 group
                    <?= $isRead ? 'border-white/5 bg-card opacity-60' : 'border-green-500/20 bg-green-500/5' ?>"
                    onclick="markAsRead(<?= $a['id'] ?>, this)">
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <div class="mt-1.5">
                                <div class="w-2 h-2 rounded-full <?= $isRead ? 'bg-muted-foreground/20' : 'bg-green-400 animate-pulse' ?>"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-sm"><?= esc($a['title']) ?></h3>
                                    <span class="text-[10px] text-muted-foreground"><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></span>
                                </div>
                                <p class="text-sm text-muted-foreground leading-relaxed"><?= nl2br(esc($a['message'])) ?></p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-muted-foreground flex-shrink-0 <?= $isRead ? 'opacity-30' : 'opacity-70' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-xl border bg-card p-16 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-1">No announcements</h3>
            <p class="text-sm text-muted-foreground">There are no announcements from the administration yet</p>
        </div>
    <?php endif; ?>
</div>

<script>
    const csrfHeaderName = '<?= csrf_header() ?>';
    let currentCsrfHash = '<?= csrf_hash() ?>';

    function markAsRead(id, el) {
        const dot = el.querySelector('.w-2\\.h-2');
        if (dot && !dot.classList.contains('bg-muted-foreground/20')) {
            dot.className = 'w-2 h-2 rounded-full bg-muted-foreground/20';
            el.className = el.className.replace('border-green-500/20 bg-green-500/5', 'border-white/5 bg-card opacity-60');
            fetch('<?= base_url('organization/announcements/read') ?>/' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    [csrfHeaderName]: currentCsrfHash
                },
                body: '<?= csrf_token() ?>=' + currentCsrfHash
            })
            .then(r => r.json())
            .then(data => { currentCsrfHash = data.csrf; });
        }
    }

    function updateCsrfToken(newHash) {
        currentCsrfHash = newHash;
    }
</script>
<?= $this->endSection() ?>

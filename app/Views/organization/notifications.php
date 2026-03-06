<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Notifications'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-3xl font-bold tracking-tight">Notifications</h2>
            <?php if ($unread_count > 0): ?>
                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/10 border border-red-500/20 text-red-400">
                    <?= $unread_count ?> Unread
                </span>
            <?php endif; ?>
        </div>
        <?php if ($unread_count > 0): ?>
            <button onclick="markAllRead()"
                class="inline-flex items-center h-9 px-4 rounded-md border border-white/10 bg-card text-foreground text-sm font-medium hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Mark All as Read
            </button>
        <?php endif; ?>
    </div>

    <!-- Notifications List -->
    <?php if (empty($notifications)): ?>
        <div class="rounded-xl border bg-card p-16 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground opacity-50" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-foreground mb-1">No notifications yet</h3>
            <p class="text-muted-foreground text-sm">You'll see notifications here when admin comments on your submissions.
            </p>
        </div>
    <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($notifications as $notification): ?>
                <?php $isUnread = !$notification['is_read']; ?>
                <div class="rounded-xl border transition-all cursor-pointer hover:border-white/20 hover:translate-x-0.5 group
                            <?= $isUnread ? 'border-green-500/20 bg-green-500/5' : 'border-white/5 bg-card opacity-60' ?>"
                    onclick="markAsRead(<?= $notification['id'] ?>, '<?= $notification['type'] ?>', <?= $notification['related_id'] ?? 'null' ?>)">
                    <div class="p-4 flex items-start gap-4">
                        <!-- Indicator dot -->
                        <div class="mt-1 flex-shrink-0">
                            <?php if ($isUnread): ?>
                                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                            <?php else: ?>
                                <div class="w-2 h-2 rounded-full bg-muted-foreground/20"></div>
                            <?php endif; ?>
                        </div>
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold <?= $isUnread ? 'text-foreground' : 'text-muted-foreground' ?>">
                                <?= esc($notification['title']) ?>
                            </p>
                            <p class="text-sm text-muted-foreground mt-0.5 line-clamp-2">
                                <?= esc($notification['message']) ?>
                            </p>
                            <p class="text-xs text-muted-foreground/60 mt-1.5">
                                <?= timeAgo($notification['created_at']) ?>
                            </p>
                        </div>
                        <!-- Arrow -->
                        <div class="flex-shrink-0 text-muted-foreground group-hover:text-foreground transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) csrfToken = next;
    }

    function markAsRead(id, type, relatedId) {
        fetch(`/organization/notifications/mark-read/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: csrfToken }
        })
            .then(r => r.json())
            .then(data => {
                updateCsrfToken(data.csrf);
                if (data.success) {
                    if ((type === 'comment' || type === 'status') && relatedId) {
                        window.location.href = `/organization/submissions/view/${relatedId}`;
                    } else {
                        location.reload();
                    }
                }
            });
    }

    function markAllRead() {
        if (!confirm('Mark all notifications as read?')) return;
        fetch('/organization/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: csrfToken }
        })
            .then(r => r.json())
            .then(data => { updateCsrfToken(data.csrf); if (data.success) location.reload(); });
    }
</script>

<?= $this->endSection() ?>

<?php
function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60)
        return 'Just now';
    if ($diff < 3600) {
        $m = floor($diff / 60);
        return $m . ' minute' . ($m > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 86400) {
        $h = floor($diff / 3600);
        return $h . ' hour' . ($h > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 604800) {
        $d = floor($diff / 86400);
        return $d . ' day' . ($d > 1 ? 's' : '') . ' ago';
    }
    return date('M d, Y', $time);
}
?>
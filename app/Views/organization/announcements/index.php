<?= $this->extend('layouts/org_modern') ?>

<?= $this->section('content') ?>
<?php
    $priorityStyles = [
        'low' => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-400', 'border' => 'border-slate-500/20'],
        'medium' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'border' => 'border-blue-500/20'],
        'high' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'border' => 'border-amber-500/20'],
        'urgent' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'border' => 'border-red-500/20'],
    ];
    $categoryIcons = ['general'=>'📋','update'=>'🔄','maintenance'=>'🔧','feature'=>'✨','security'=>'🔒','announcement'=>'📢'];

    $pinned = array_values(array_filter($announcements, fn($a) => $a['is_pinned']));
    $regular = array_values(array_filter($announcements, fn($a) => !$a['is_pinned']));
    $selected = !empty($announcements) ? $announcements[0] : null;
?>

<div class="flex gap-6 min-h-[calc(100vh-120px)]">

    <!-- Sidebar -->
    <div class="w-full md:w-96 flex-shrink-0">
        <div class="sticky top-24 space-y-4">
            <!-- Welcome Card -->
            <div class="rounded-2xl bg-gradient-to-br from-green-600 to-emerald-700 p-6 text-white shadow-lg shadow-green-500/10">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <span class="text-2xl">📢</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">Announcements</h2>
                        <p class="text-sm text-green-100">Stay up to date</p>
                    </div>
                </div>
                <p class="text-sm text-green-100">Browse the latest updates, announcements, and important notices from the administration.</p>
                <?php if ($unread_count > 0): ?>
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                        <?= $unread_count ?> unread
                    </div>
                <?php endif; ?>
            </div>

            <!-- Announcement List -->
            <div class="space-y-1 max-h-[calc(100vh-300px)] overflow-y-auto pr-1 custom-scrollbar">
                <?php if (!empty($pinned)): ?>
                    <div class="mb-3">
                        <h3 class="mb-2 flex items-center gap-1.5 px-3 text-xs font-semibold uppercase tracking-wider text-amber-400/80">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            Pinned
                        </h3>
                        <?php foreach ($pinned as $a): ?>
                            <?php $isRead = $a['is_read'] ?? false; ?>
                            <button onclick="selectAnnouncement(<?= $a['id'] ?>)"
                                class="w-full text-left rounded-xl p-3 transition-all mb-1 announcement-item
                                <?= ($selected['id'] ?? null) == $a['id'] ? 'bg-green-500/10 border-2 border-green-500/30' : 'bg-card border-2 border-transparent hover:bg-muted/50 hover:border-white/10' ?>"
                                data-id="<?= $a['id'] ?>">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 text-xl mt-0.5"><?= $categoryIcons[$a['category'] ?? 'general'] ?? '📋' ?></span>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-sm line-clamp-1 <?= $isRead ? 'text-muted-foreground' : '' ?>"><?= esc($a['title']) ?></h4>
                                        <p class="mt-1 text-xs text-muted-foreground line-clamp-1"><?= esc($a['summary'] ?? mb_substr($a['message'], 0, 60)) ?></p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <?php $pri = $priorityStyles[$a['priority'] ?? 'medium'] ?? $priorityStyles['medium']; ?>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium <?= $pri['bg'] ?> <?= $pri['text'] ?>"><?= $a['priority'] ?? 'medium' ?></span>
                                            <span class="text-[10px] text-muted-foreground"><?= time_ago($a['created_at']) ?></span>
                                            <?php if (($a['comment_count'] ?? 0) > 0): ?>
                                                <span class="text-[10px] text-muted-foreground flex items-center gap-0.5">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                                    <?= $a['comment_count'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div>
                    <?php if (!empty($pinned)): ?>
                        <h3 class="mb-2 flex items-center gap-1.5 px-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            All Announcements
                        </h3>
                    <?php endif; ?>
                    <?php if (empty($announcements)): ?>
                        <div class="rounded-xl bg-card p-6 text-center border border-white/10">
                            <span class="text-4xl">📭</span>
                            <h3 class="mt-3 text-sm font-medium">No announcements yet</h3>
                            <p class="mt-1 text-xs text-muted-foreground">Check back later for updates</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($regular as $a): ?>
                            <?php $isRead = $a['is_read'] ?? false; ?>
                            <button onclick="selectAnnouncement(<?= $a['id'] ?>)"
                                class="w-full text-left rounded-xl p-3 transition-all mb-1 announcement-item
                                <?= ($selected['id'] ?? null) == $a['id'] ? 'bg-green-500/10 border-2 border-green-500/30' : 'bg-card border-2 border-transparent hover:bg-muted/50 hover:border-white/10' ?>"
                                data-id="<?= $a['id'] ?>">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 text-xl mt-0.5"><?= $categoryIcons[$a['category'] ?? 'general'] ?? '📋' ?></span>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-sm line-clamp-1 <?= $isRead ? 'text-muted-foreground' : '' ?>"><?= esc($a['title']) ?></h4>
                                        <p class="mt-1 text-xs text-muted-foreground line-clamp-1"><?= esc($a['summary'] ?? mb_substr($a['message'], 0, 60)) ?></p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <?php $pri = $priorityStyles[$a['priority'] ?? 'medium'] ?? $priorityStyles['medium']; ?>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium <?= $pri['bg'] ?> <?= $pri['text'] ?>"><?= $a['priority'] ?? 'medium' ?></span>
                                            <span class="text-[10px] text-muted-foreground"><?= time_ago($a['created_at']) ?></span>
                                            <?php if (($a['comment_count'] ?? 0) > 0): ?>
                                                <span class="text-[10px] text-muted-foreground flex items-center gap-0.5">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                                    <?= $a['comment_count'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content - Detail View -->
    <div class="flex-1 hidden md:block">
        <div class="sticky top-24" id="detail-panel">
            <?php if ($selected): ?>
                <?php $pri = $priorityStyles[$selected['priority'] ?? 'medium'] ?? $priorityStyles['medium']; ?>
                <div id="detail-content" data-id="<?= $selected['id'] ?>">
                    <!-- Urgent Banner -->
                    <?php if (($selected['priority'] ?? '') === 'urgent'): ?>
                        <div class="mb-6 flex items-center gap-3 rounded-2xl bg-gradient-to-r from-red-500 to-orange-500 p-4 text-white shadow-lg">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold">Urgent Announcement</p>
                                <p class="text-sm text-red-100">This requires your immediate attention</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Article -->
                    <article class="rounded-2xl bg-card border border-white/10 p-8 shadow-sm">
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-3xl"><?= $categoryIcons[$selected['category'] ?? 'general'] ?? '📋' ?></span>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium <?= $pri['bg'] ?> <?= $pri['text'] ?> border <?= $pri['border'] ?>">
                                        <?= ucfirst($selected['priority'] ?? 'medium') ?> Priority
                                    </span>
                                    <span class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium capitalize">
                                        <?= $selected['category'] ?? 'general' ?>
                                    </span>
                                </div>
                            </div>
                            <h1 class="text-2xl font-bold"><?= esc($selected['title']) ?></h1>
                        </div>

                        <!-- Meta -->
                        <div class="mb-6 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                            <div class="flex items-center gap-2">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-500 text-xs font-bold text-white">
                                    <?= mb_substr($selected['author'] ?? $selected['sender_name'] ?? 'A', 0, 1) ?>
                                </div>
                                <span><?= esc($selected['author'] ?? $selected['sender_name'] ?? 'Admin') ?></span>
                            </div>
                            <span class="text-white/10">•</span>
                            <span><?= date('F d, Y h:i A', strtotime($selected['created_at'])) ?></span>
                            <?php if (!empty($selected['expires_at'])): ?>
                                <span class="text-white/10">•</span>
                                <span class="text-amber-400">Expires: <?= date('M d, Y', strtotime($selected['expires_at'])) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="prose prose-invert max-w-none text-sm leading-relaxed space-y-1">
                            <?php
                                $content = $selected['content'] ?? $selected['message'] ?? '';
                                $lines = explode("\n", $content);
                                foreach ($lines as $line) {
                                    $line = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
                                    $line = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $line);
                                    if (preg_match('/^### (.+)/', $line, $m)) {
                                        echo '<h3 class="mt-4 mb-2 text-base font-semibold">' . $m[1] . '</h3>';
                                    } elseif (preg_match('/^- (.+)/', $line, $m)) {
                                        echo '<li class="ml-4 list-disc">' . $m[1] . '</li>';
                                    } elseif (preg_match('/^\d+\. (.+)/', $line, $m)) {
                                        echo '<li class="ml-4 list-decimal">' . $m[1] . '</li>';
                                    } elseif (trim($line) === '') {
                                        echo '<div class="h-2"></div>';
                                    } else {
                                        echo '<p>' . $line . '</p>';
                                    }
                                }
                            ?>
                        </div>

                        <!-- Attachments -->
                        <?php if (!empty($selected['attachments'])): ?>
                            <div class="mt-8 pt-6 border-t border-white/10">
                                <h4 class="flex items-center gap-2 text-sm font-medium mb-4">
                                    <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Attachments (<?= count($selected['attachments']) ?>)
                                </h4>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <?php foreach ($selected['attachments'] as $att): ?>
                                        <?php
                                            $icon = '📄';
                                            $ext = strtolower(pathinfo($att['file_name'], PATHINFO_EXTENSION));
                                            if (in_array($ext, ['pdf'])) $icon = '📕';
                                            elseif (in_array($ext, ['doc','docx'])) $icon = '📘';
                                            elseif (in_array($ext, ['xls','xlsx'])) $icon = '📗';
                                            elseif (in_array($ext, ['jpg','jpeg','png','gif'])) $icon = '🖼️';

                                            $size = $att['file_size'] ?? 0;
                                            $sizeStr = $size >= 1048576 ? round($size/1048576, 1).' MB' : round($size/1024, 1).' KB';
                                        ?>
                                        <div class="flex items-center justify-between rounded-xl border border-white/10 bg-muted/30 px-4 py-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span class="text-2xl flex-shrink-0"><?= $icon ?></span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium truncate"><?= esc($att['file_name']) ?></p>
                                                    <p class="text-xs text-muted-foreground"><?= $sizeStr ?></p>
                                                </div>
                                            </div>
                                            <a href="<?= base_url('organization/announcements/attachment-download/' . $att['id']) ?>"
                                                class="inline-flex items-center rounded-lg border border-white/10 px-3 py-1.5 text-xs font-medium text-foreground hover:bg-muted transition-colors flex-shrink-0">
                                                Download
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </article>

                    <!-- Discussion Section -->
                    <div class="mt-6 rounded-2xl bg-card border border-white/10 p-6 shadow-sm">
                        <h4 class="flex items-center gap-2 text-sm font-semibold mb-4">
                            <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            Discussion
                            <span class="text-muted-foreground font-normal">(<?= count($selected['comments'] ?? []) ?>)</span>
                        </h4>

                        <!-- Comment Input -->
                        <div class="flex items-start gap-3 mb-6">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-500 text-xs font-bold text-white flex-shrink-0 mt-1">
                                <?= mb_substr(session()->get('username') ?? 'U', 0, 1) ?>
                            </div>
                            <div class="flex-1">
                                <textarea id="comment-input" rows="2" placeholder="Add to the discussion..."
                                    class="w-full rounded-xl border border-white/10 bg-background px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500/50 resize-none"></textarea>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-muted-foreground/60">Markdown supported</span>
                                    <button onclick="postComment(<?= $selected['id'] ?>)"
                                        class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Post
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Comments List -->
                        <div id="comments-list" class="space-y-4">
                            <?php foreach (($selected['comments'] ?? []) as $comment): ?>
                                <div class="flex items-start gap-3" id="comment-<?= $comment['id'] ?>">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-xs font-bold text-white flex-shrink-0">
                                        <?= mb_substr($comment['username'] ?? 'U', 0, 1) ?>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium"><?= esc($comment['username'] ?? 'User') ?></span>
                                            <?php if (($comment['role'] ?? '') === 'admin'): ?>
                                                <span class="text-[10px] font-medium text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm mt-1 text-foreground/90"><?= nl2br(esc($comment['comment'])) ?></p>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="text-xs text-muted-foreground"><?= time_ago($comment['created_at']) ?></span>
                                            <?php if ($comment['user_id'] == session()->get('user_id') || session()->get('role') === 'admin'): ?>
                                                <button onclick="deleteComment(<?= $comment['id'] ?>, <?= $selected['id'] ?>)"
                                                    class="text-xs text-muted-foreground hover:text-red-400 transition-colors">Delete</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($selected['comments'])): ?>
                                <p class="text-sm text-muted-foreground text-center py-4">No comments yet. Be the first to start the discussion!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Nav -->
                    <?php $others = array_slice(array_merge($pinned, $regular), 0, 5); ?>
                    <?php if (count($others) > 1): ?>
                        <div class="mt-4 rounded-2xl bg-muted/30 p-4">
                            <h4 class="text-sm font-medium text-muted-foreground mb-3">Other Announcements</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($others as $o): ?>
                                    <?php if ($o['id'] != $selected['id']): ?>
                                        <button onclick="selectAnnouncement(<?= $o['id'] ?>)"
                                            class="rounded-lg bg-card border border-white/10 px-3 py-2 text-sm hover:shadow-md transition-all hover:border-green-500/30">
                                            <span class="mr-1.5"><?= $categoryIcons[$o['category'] ?? 'general'] ?? '📋' ?></span>
                                            <?= mb_substr($o['title'], 0, 30) ?><?= strlen($o['title']) > 30 ? '...' : '' ?>
                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="flex items-center justify-center rounded-2xl border-2 border-dashed border-white/10 bg-card/50 p-12 min-h-[500px]">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-muted">
                            <span class="text-4xl">👆</span>
                        </div>
                        <h3 class="text-lg font-medium">Select an announcement</h3>
                        <p class="mt-2 text-sm text-muted-foreground max-w-xs mx-auto">
                            Choose an announcement from the list to read its full content and details
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const csrfHeaderName = '<?= csrf_header() ?>';
let currentCsrfHash = '<?= csrf_hash() ?>';

function selectAnnouncement(id) {
    // Mark as read
    fetch('<?= base_url('organization/announcements/read/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', [csrfHeaderName]: currentCsrfHash },
        body: '<?= csrf_token() ?>=' + currentCsrfHash
    }).then(r => r.json()).then(data => { currentCsrfHash = data.csrf; });

    // Reload to show updated detail
    location.hash = 'ann-' + id;
    location.reload();
}

function postComment(announcementId) {
    const input = document.getElementById('comment-input');
    const text = input.value.trim();
    if (!text) return;

    fetch('<?= base_url('organization/announcements/comment/') ?>' + announcementId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', [csrfHeaderName]: currentCsrfHash },
        body: '<?= csrf_token() ?>=' + currentCsrfHash + '&comment=' + encodeURIComponent(text)
    }).then(r => r.json()).then(data => {
        currentCsrfHash = data.csrf;
        if (data.success) location.reload();
        else alert(data.message || 'Failed to post comment');
    });
}

function deleteComment(commentId, announcementId) {
    if (!confirm('Delete this comment?')) return;
    fetch('<?= base_url('organization/announcements/comment/delete/') ?>' + commentId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', [csrfHeaderName]: currentCsrfHash },
        body: '<?= csrf_token() ?>=' + currentCsrfHash
    }).then(r => r.json()).then(data => {
        currentCsrfHash = data.csrf;
        if (data.success) location.reload();
    });
}

function updateCsrfToken(newHash) { currentCsrfHash = newHash; }
</script>
<?= $this->endSection() ?>

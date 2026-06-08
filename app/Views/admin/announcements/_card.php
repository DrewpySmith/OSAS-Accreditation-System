<?php
    $priorityStyles = [
        'low' => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-400', 'dot' => 'bg-slate-400'],
        'medium' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'dot' => 'bg-blue-400'],
        'high' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'dot' => 'bg-amber-400'],
        'urgent' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'dot' => 'bg-red-400'],
    ];
    $categoryStyles = [
        'general' => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-400'],
        'update' => ['bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-400'],
        'maintenance' => ['bg' => 'bg-orange-500/10', 'text' => 'text-orange-400'],
        'feature' => ['bg' => 'bg-green-500/10', 'text' => 'text-green-400'],
        'security' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400'],
        'announcement' => ['bg' => 'bg-purple-500/10', 'text' => 'text-purple-400'],
    ];
    $categoryIcons = [
        'general' => '📋', 'update' => '🔄', 'maintenance' => '🔧',
        'feature' => '✨', 'security' => '🔒', 'announcement' => '📢',
    ];

    $pri = $priorityStyles[$a['priority'] ?? 'medium'] ?? $priorityStyles['medium'];
    $cat = $categoryStyles[$a['category'] ?? 'general'] ?? $categoryStyles['general'];
    $catIcon = $categoryIcons[$a['category'] ?? 'general'] ?? '📋';

    $summary = $a['summary'] ?? mb_substr(strip_tags($a['message']), 0, 100);
    $tags = !empty($a['tags']) ? (is_string($a['tags']) ? json_decode($a['tags'], true) : $a['tags']) : [];
    if (empty($tags) && !empty($a['summary'])) {
        // Try to extract tags from a "tags" field if it exists
    }

    $createdAt = $a['created_at'] ?? '';
    $timeDiff = '';
    if ($createdAt) {
        $now = time();
        $then = strtotime($createdAt);
        $diff = $now - $then;
        if ($diff < 60) $timeDiff = 'Just now';
        elseif ($diff < 3600) $timeDiff = floor($diff/60) . 'm ago';
        elseif ($diff < 86400) $timeDiff = floor($diff/3600) . 'h ago';
        elseif ($diff < 604800) $timeDiff = floor($diff/86400) . 'd ago';
        else $timeDiff = date('M d', $then);
    }
?>

<div class="announcement-card group relative overflow-hidden rounded-xl border border-white/10 bg-card shadow-sm transition-all hover:shadow-md hover:border-white/20
    <?= ($a['priority'] ?? '') === 'urgent' ? 'border-red-500/30' : '' ?>"
    data-id="<?= $a['id'] ?>"
    data-title="<?= esc($a['title']) ?>"
    data-summary="<?= esc($summary) ?>"
    data-content="<?= esc($a['content'] ?? $a['message'] ?? '') ?>"
    data-message="<?= esc($a['message'] ?? '') ?>"
    data-priority="<?= $a['priority'] ?? 'medium' ?>"
    data-category="<?= $a['category'] ?? 'general' ?>"
    data-author="<?= esc($a['author'] ?? $a['sender_name'] ?? '') ?>"
    data-pinned="<?= $a['is_pinned'] ?? 0 ?>"
    data-expires="<?= $a['expires_at'] ?? '' ?>">

    <?php if (($a['priority'] ?? '') === 'urgent'): ?>
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-500 to-orange-500"></div>
    <?php endif; ?>

    <div class="p-5">
        <!-- Header -->
        <div class="mb-3 flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium <?= $pri['bg'] ?> <?= $pri['text'] ?>">
                        <span class="h-1.5 w-1.5 rounded-full <?= $pri['dot'] ?>"></span>
                        <?= $a['priority'] ?? 'medium' ?>
                    </span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?= $cat['bg'] ?> <?= $cat['text'] ?>">
                        <?= $catIcon ?> <?= $a['category'] ?? 'general' ?>
                    </span>
                    <?php if (!$a['is_active']): ?>
                        <span class="inline-flex items-center rounded-full bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">Draft</span>
                    <?php endif; ?>
                    <?php if ($a['is_pinned']): ?>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-400">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            Pinned
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="text-base font-semibold line-clamp-2"><?= esc($a['title']) ?></h3>
            </div>

            <!-- Actions (hover) -->
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                <button onclick="event.stopPropagation(); togglePin(<?= $a['id'] ?>)"
                    class="rounded-lg p-2 transition-colors <?= $a['is_pinned'] ? 'text-amber-400 hover:bg-amber-500/10' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                    title="<?= $a['is_pinned'] ? 'Unpin' : 'Pin' ?>">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="<?= $a['is_pinned'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </button>
                <button onclick="event.stopPropagation(); editAnnouncement(<?= $a['id'] ?>)"
                    class="rounded-lg p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-blue-400" title="Edit">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </button>
                <button onclick="event.stopPropagation(); toggleActive(<?= $a['id'] ?>)"
                    class="rounded-lg p-2 transition-colors <?= $a['is_active'] ? 'text-green-400 hover:bg-green-500/10' : 'text-muted-foreground hover:bg-muted hover:text-foreground' ?>"
                    title="<?= $a['is_active'] ? 'Deactivate' : 'Activate' ?>">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="<?= $a['is_active'] ? 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' ?>" />
                    </svg>
                </button>
                <button onclick="event.stopPropagation(); deleteAnnouncement(<?= $a['id'] ?>)"
                    class="rounded-lg p-2 text-muted-foreground transition-colors hover:bg-red-500/10 hover:text-red-400" title="Delete">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Summary -->
        <p class="mb-4 text-sm text-muted-foreground line-clamp-2"><?= esc($summary) ?></p>

        <!-- Footer -->
        <div class="flex items-center justify-between border-t border-white/5 pt-3">
            <div class="flex items-center gap-2">
                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-[10px] font-bold text-white">
                    <?= mb_substr($a['author'] ?? $a['sender_name'] ?? 'A', 0, 1) ?>
                </div>
                <span class="text-xs text-muted-foreground"><?= esc($a['author'] ?? $a['sender_name'] ?? 'Admin') ?></span>
            </div>
            <div class="flex items-center gap-3 text-xs text-muted-foreground">
                <?php if ($a['attachment_count'] ?? 0 > 0): ?>
                    <span class="flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <?= $a['attachment_count'] ?>
                    </span>
                <?php endif; ?>
                <?php if ($a['comment_count'] ?? 0 > 0): ?>
                    <span class="flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        <?= $a['comment_count'] ?>
                    </span>
                <?php endif; ?>
                <span><?= $timeDiff ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/documents') ?>"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-white/10 bg-card hover:bg-muted h-10 w-10 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-500"></i>
                    Review Document
                </h1>
                <p class="text-sm text-muted-foreground"><?= esc($document['document_title']) ?></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <?php
            $statusClasses = [
                'pending' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                'reviewed' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                'approved' => 'bg-green-500/10 text-green-500 border-green-500/20',
                'rejected' => 'bg-red-500/10 text-red-500 border-red-500/20'
            ];
            $currentStatusClass = $statusClasses[$document['status']] ?? 'bg-muted text-muted-foreground';
            ?>
            <span
                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold <?= $currentStatusClass ?>">
                <?= ucfirst($document['status']) ?>
            </span>
            <span class="text-sm text-muted-foreground mr-2">ID: #<?= $document['id'] ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content Area: Preview & Information -->
        <div class="lg:col-span-2 space-y-6 text-card-foreground">
            <!-- Review Actions & Info Card -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                <div class="p-6 border-b border-white/5 bg-muted/30">
                    <h3 class="font-semibold text-lg flex items-center gap-2 italic">
                        <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                        Document Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Organization
                            </p>
                            <p class="text-sm font-medium"><?= esc($document['org_name']) ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Campus</p>
                            <p class="text-sm font-medium"><?= esc($document['campus'] ?? 'N/A') ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Academic Year
                            </p>
                            <p class="text-sm font-medium"><?= esc($document['academic_year']) ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Document Type
                            </p>
                            <p class="text-sm font-medium">
                                <?= esc(str_replace('_', ' ', ucwords($document['document_type']))) ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Submitted By
                            </p>
                            <p class="text-sm font-medium"><?= esc($document['submitted_by_name']) ?></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Date Submitted
                            </p>
                            <p class="text-sm font-medium">
                                <?= date('M d, Y h:i A', strtotime($document['created_at'])) ?></p>
                        </div>
                    </div>

                    <?php if (!empty($document['description'])): ?>
                        <div class="space-y-2 mb-6 p-4 rounded-lg bg-muted/20 border border-white/5">
                            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Description</p>
                            <p class="text-sm"><?= nl2br(esc($document['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-3 pt-4 border-t border-white/5">
                        <a href="<?= base_url('admin/documents/download/' . $document['id']) ?>"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-green-600/10 text-green-500 border border-green-500/20 hover:bg-green-600/20 h-10 px-4 py-2 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Download File
                        </a>
                        <div class="h-10 w-px bg-white/10 hidden sm:block"></div>
                        <button onclick="updateStatus('reviewed')"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 h-10 px-4 py-2">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>
                            Reviewed
                        </button>
                        <button onclick="updateStatus('approved')"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-green-600 text-white hover:bg-green-700 h-10 px-4 py-2">
                            <i data-lucide="thumbs-up" class="w-4 h-4 mr-2"></i>
                            Approve
                        </button>
                        <button onclick="updateStatus('rejected')"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-700 h-10 px-4 py-2">
                            <i data-lucide="thumbs-down" class="w-4 h-4 mr-2"></i>
                            Reject
                        </button>
                    </div>
                </div>
            </div>

            <!-- File Preview Card -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                <div class="p-4 border-b border-white/5 bg-muted/30 flex justify-between items-center">
                    <h3 class="font-semibold text-base flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-primary"></i>
                        Preview: <?= esc($document['file_name']) ?>
                    </h3>
                    <div class="flex gap-2">
                        <a href="<?= base_url('admin/documents/preview/' . $document['id']) ?>" target="_blank"
                            class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-white/10 bg-card hover:bg-muted h-8 px-3 transition-colors">
                            <i data-lucide="external-link" class="w-3 h-3 mr-1"></i> Full Screen
                        </a>
                    </div>
                </div>
                <div class="bg-black/20 p-6 min-h-[500px] flex items-center justify-center">
                    <?php if (strpos(strtolower($document['file_name']), '.pdf') !== false): ?>
                        <iframe src="<?= base_url('admin/documents/preview/' . $document['id']) ?>"
                            class="w-full h-[600px] rounded border-0 bg-white"></iframe>
                    <?php else: ?>
                        <div class="text-center space-y-4 max-w-sm">
                            <i data-lucide="file-warning" class="w-12 h-12 mx-auto text-muted-foreground opacity-20"></i>
                            <p class="text-muted-foreground">Preview not available for this file type. Please download the
                                document to review it.</p>
                            <a href="<?= base_url('admin/documents/download/' . $document['id']) ?>"
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-white/10 bg-card hover:bg-muted h-10 px-4 py-2 transition-colors w-full">
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download Now
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar: History & Comments -->
        <div class="space-y-6">
            <!-- Review History -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden">
                <div class="p-4 border-b border-white/5 bg-muted/30">
                    <h3 class="font-semibold text-sm flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-blue-400"></i>
                        Review Activity
                    </h3>
                </div>
                <div class="p-4 max-h-[400px] overflow-y-auto custom-scrollbar">
                    <?php if (!empty($review_history)): ?>
                        <div
                            class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-px before:bg-white/10">
                            <?php foreach ($review_history as $event): ?>
                                <div class="relative">
                                    <div
                                        class="absolute -left-6 top-1 w-4 h-4 rounded-full border border-white/20 bg-card flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-start gap-2">
                                            <p class="text-xs font-semibold"><?= esc($event['username'] ?? 'System') ?></p>
                                            <p class="text-[10px] text-muted-foreground whitespace-nowrap">
                                                <?= date('M d, H:i', strtotime($event['created_at'])) ?></p>
                                        </div>

                                        <?php if (($event['action'] ?? '') === 'status_change'): ?>
                                            <p class="text-xs text-muted-foreground">
                                                Changed status to <span
                                                    class="text-foreground font-medium"><?= ucfirst($event['to_status'] ?? '') ?></span>
                                            </p>
                                        <?php elseif (($event['action'] ?? '') === 'comment'): ?>
                                            <div class="text-xs p-2 rounded bg-muted/30 border border-white/5 mt-1 italic">
                                                "<?= esc($event['comment'] ?? '') ?>"
                                            </div>
                                        <?php else: ?>
                                            <p class="text-xs text-muted-foreground">
                                                <?= ucfirst(esc($event['action'] ?? 'activity')) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-8 text-center">
                            <p class="text-xs text-muted-foreground">No recent activity</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Discussion/Comments -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 border-b border-white/5 bg-muted/30">
                    <h3 class="font-semibold text-sm flex items-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-green-400"></i>
                        Discussion
                    </h3>
                </div>
                <div class="p-4 flex-1 max-h-[400px] overflow-y-auto custom-scrollbar space-y-4">
                    <?php if (!empty($document['comments'])): ?>
                        <?php foreach ($document['comments'] as $comment): ?>
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-[10px]">
                                    <span class="font-bold text-foreground"><?= esc($comment['username']) ?> <span
                                            class="font-normal text-muted-foreground">(<?= ucfirst($comment['role']) ?>)</span></span>
                                    <span
                                        class="text-muted-foreground"><?= date('M d, h:i A', strtotime($comment['created_at'])) ?></span>
                                </div>
                                <div class="p-3 rounded-lg bg-muted border border-white/5 text-sm">
                                    <?= nl2br(esc($comment['comment'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="py-12 text-center text-muted-foreground">
                            <i data-lucide="messages-square" class="w-8 h-8 mx-auto opacity-10 mb-2"></i>
                            <p class="text-xs">No comments yet. Start the conversation!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="p-4 border-t border-white/5 bg-card">
                    <form id="commentForm" class="space-y-3">
                        <?= csrf_field() ?>
                        <textarea id="comment" rows="3" placeholder="Add a comment..." required
                            class="flex w-full rounded-md border border-white/10 bg-muted/50 px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-blue-500 text-foreground resize-none"></textarea>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium bg-secondary text-white hover:bg-secondary/90 h-9 px-4 transition-colors">
                            <i data-lucide="send" class="w-3 h-3 mr-2"></i>
                            Post Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) {
            csrfToken = next;
            // Also update any hidden CSRF fields
            document.querySelectorAll('input[name="<?= csrf_token() ?>"]').forEach(el => el.value = next);
        }
    }

    function updateStatus(status) {
        if (confirm(`Are you sure you want to mark this document as ${status}?`)) {
            fetch('<?= base_url("admin/documents/status/" . $document["id"]) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeaderName]: csrfToken
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    updateCsrfToken(data.csrf);
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => alert('Failed to update status'));
        }
    }

    document.getElementById('commentForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const comment = document.getElementById('comment').value;
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        fetch('<?= base_url("admin/documents/comment/" . $document["id"]) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                [csrfHeaderName]: csrfToken
            },
            body: JSON.stringify({ comment: comment })
        })
            .then(response => response.json())
            .then(data => {
                updateCsrfToken(data.csrf);
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                }
            })
            .catch(err => {
                alert('Failed to post comment');
                submitBtn.disabled = false;
            });
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
<?= $this->endSection() ?>
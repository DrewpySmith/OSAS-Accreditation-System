<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <?php
        $totalCount = count($announcements);
        $pinnedCount = count(array_filter($announcements, fn($a) => $a['is_pinned']));
        $activeCount = count(array_filter($announcements, fn($a) => $a['is_active']));
        $urgentCount = count(array_filter($announcements, fn($a) => ($a['priority'] ?? 'medium') === 'urgent'));
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Announcements Hub</h1>
            <p class="text-sm text-muted-foreground mt-1">Manage and send announcements to organizations</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-2xl font-bold"><?= $totalCount ?></p>
                <p class="text-xs text-muted-foreground">Total Announcements</p>
            </div>
            <button id="new-announcement-btn"
                class="inline-flex items-center h-10 px-4 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/20 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Announcement
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-white/10 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10">
                    <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold"><?= $totalCount ?></p>
                    <p class="text-xs text-muted-foreground">Total</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-white/10 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10">
                    <svg class="h-6 w-6 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold"><?= $pinnedCount ?></p>
                    <p class="text-xs text-muted-foreground">Pinned</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-white/10 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold"><?= $activeCount ?></p>
                    <p class="text-xs text-muted-foreground">Active</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-white/10 bg-card p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-500/10">
                    <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold"><?= $urgentCount ?></p>
                    <p class="text-xs text-muted-foreground">Urgent</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
            <input type="text" id="search-input" placeholder="Search announcements..."
                class="w-full rounded-lg border border-white/10 bg-background py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50">
        </div>
        <div class="flex items-center gap-3">
            <select id="filter-priority" class="rounded-lg border border-white/10 bg-background px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                <option value="all">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
            <select id="filter-category" class="rounded-lg border border-white/10 bg-background px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                <option value="all">All Categories</option>
                <option value="general">General</option>
                <option value="update">Update</option>
                <option value="maintenance">Maintenance</option>
                <option value="feature">Feature</option>
                <option value="security">Security</option>
                <option value="announcement">Announcement</option>
            </select>
        </div>
    </div>

    <?php
        $pinnedAnnouncements = array_filter($announcements, fn($a) => $a['is_pinned']);
        $regularAnnouncements = array_filter($announcements, fn($a) => !$a['is_pinned']);
    ?>

    <!-- Announcements Grid -->
    <?php if (empty($announcements)): ?>
        <div class="rounded-xl border border-dashed border-white/10 bg-card p-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-1">No announcements yet</h3>
            <p class="text-sm text-muted-foreground">Create your first announcement to get started</p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php if (!empty($pinnedAnnouncements)): ?>
                <div class="space-y-3">
                    <h3 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-amber-400/80">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        Pinned
                    </h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <?php foreach ($pinnedAnnouncements as $a): ?>
                            <?= $this->include('admin/announcements/_card', ['a' => $a]) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($regularAnnouncements)): ?>
                <div class="space-y-3">
                    <?php if (!empty($pinnedAnnouncements)): ?>
                        <h3 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            All Announcements
                        </h3>
                    <?php endif; ?>
                    <div class="grid gap-4 md:grid-cols-2">
                        <?php foreach ($regularAnnouncements as $a): ?>
                            <?= $this->include('admin/announcements/_card', ['a' => $a]) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div id="announcement-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-card border border-white/10 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between border-b border-white/5 px-6 py-4">
            <div>
                <h3 class="text-lg font-semibold" id="modal-title">New Announcement</h3>
                <p class="text-sm text-muted-foreground" id="modal-subtitle">Create a new announcement for your audience</p>
            </div>
            <button onclick="closeModal()" class="rounded-lg p-2 text-muted-foreground hover:bg-muted transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="announcement-form" method="POST" action="<?= base_url('admin/announcements/store') ?>" enctype="multipart/form-data">
            <input type="hidden" name="announcement_id" id="announcement-id">
            <div class="overflow-y-auto max-h-[calc(90vh-140px)] p-6 space-y-5">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="ann-title" required
                        class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50"
                        placeholder="Enter announcement title">
                </div>
                <!-- Summary -->
                <div>
                    <label class="block text-sm font-medium mb-1.5">Summary <span class="text-red-500">*</span></label>
                    <input type="text" name="summary" id="ann-summary" required
                        class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50"
                        placeholder="Brief summary (shown in card preview)">
                </div>
                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium mb-1.5">Content <span class="text-red-500">*</span></label>
                    <textarea name="content" id="ann-content" rows="6" required
                        class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50 resize-none"
                        placeholder="Write the full announcement content here..."></textarea>
                </div>
                <!-- Message (legacy field) -->
                <input type="hidden" name="message" id="ann-message">
                <!-- Priority & Category -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Priority <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label): ?>
                                <label class="flex cursor-pointer items-center justify-center rounded-lg border-2 px-3 py-2 text-sm font-medium transition-all
                                    priority-option border-white/10 text-muted-foreground hover:border-white/20"
                                    data-value="<?= $val ?>">
                                    <input type="radio" name="priority" value="<?= $val ?>" class="sr-only" <?= $val === 'medium' ? 'checked' : '' ?>>
                                    <?= $label ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Category <span class="text-red-500">*</span></label>
                        <select name="category" id="ann-category"
                            class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                            <option value="general">General</option>
                            <option value="update">Update</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="feature">Feature</option>
                            <option value="security">Security</option>
                            <option value="announcement">Announcement</option>
                        </select>
                    </div>
                </div>
                <!-- Author & Expiry -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Author</label>
                        <input type="text" name="author" id="ann-author"
                            class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                            placeholder="Author name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Expires At</label>
                        <input type="datetime-local" name="expires_at" id="ann-expires"
                            class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>
                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tags</label>
                    <input type="text" name="tags" id="ann-tags"
                        class="w-full rounded-lg border border-white/10 bg-background px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        placeholder="Comma-separated tags (e.g., important, update, v2.0)">
                </div>
                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium mb-1.5">Attachments</label>
                    <div class="rounded-lg border-2 border-dashed border-white/10 p-4 text-center hover:border-blue-500/30 transition-colors">
                        <input type="file" name="attachments[]" id="ann-attachments" multiple
                            class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                        <label for="ann-attachments" class="cursor-pointer">
                            <svg class="mx-auto h-8 w-8 text-muted-foreground mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                            <p class="text-sm text-muted-foreground">Click to upload or drag and drop</p>
                            <p class="text-xs text-muted-foreground/60 mt-1">PDF, DOC, XLS, JPG, PNG up to 10MB each</p>
                        </label>
                        <div id="file-list" class="mt-3 space-y-2 hidden"></div>
                    </div>
                </div>
                <!-- Toggles -->
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative h-6 w-11 rounded-full transition-colors bg-muted" id="active-toggle">
                            <input type="checkbox" name="is_pinned" id="ann-pinned" class="sr-only">
                            <span class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform translate-x-0.5"></span>
                        </div>
                        <span class="text-sm font-medium">Pinned</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-white/5 px-6 py-4">
                <button type="button" onclick="closeModal()"
                    class="inline-flex items-center h-9 px-4 rounded-lg border border-white/10 bg-card text-muted-foreground text-sm hover:bg-muted transition-colors">Cancel</button>
                <button type="submit"
                    class="inline-flex items-center h-9 px-5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/20">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfHeaderName = '<?= csrf_header() ?>';
let currentCsrfHash = '<?= csrf_hash() ?>';

const priorityColors = {
    low: { bg: 'bg-slate-500/10', text: 'text-slate-400', dot: 'bg-slate-400' },
    medium: { bg: 'bg-blue-500/10', text: 'text-blue-400', dot: 'bg-blue-400' },
    high: { bg: 'bg-amber-500/10', text: 'text-amber-400', dot: 'bg-amber-400' },
    urgent: { bg: 'bg-red-500/10', text: 'text-red-400', dot: 'bg-red-400' }
};
const categoryColors = {
    general: { bg: 'bg-slate-500/10', text: 'text-slate-400' },
    update: { bg: 'bg-indigo-500/10', text: 'text-indigo-400' },
    maintenance: { bg: 'bg-orange-500/10', text: 'text-orange-400' },
    feature: { bg: 'bg-green-500/10', text: 'text-green-400' },
    security: { bg: 'bg-red-500/10', text: 'text-red-400' },
    announcement: { bg: 'bg-purple-500/10', text: 'text-purple-400' }
};
const categoryIcons = {
    general: '📋', update: '🔄', maintenance: '🔧', feature: '✨', security: '🔒', announcement: '📢'
};

// Search & Filter
document.getElementById('search-input').addEventListener('input', filterCards);
document.getElementById('filter-priority').addEventListener('change', filterCards);
document.getElementById('filter-category').addEventListener('change', filterCards);

function filterCards() {
    const query = document.getElementById('search-input').value.toLowerCase();
    const priority = document.getElementById('filter-priority').value;
    const category = document.getElementById('filter-category').value;

    document.querySelectorAll('.announcement-card').forEach(card => {
        const title = (card.dataset.title || '').toLowerCase();
        const summary = (card.dataset.summary || '').toLowerCase();
        const cardPriority = card.dataset.priority || '';
        const cardCategory = card.dataset.category || '';

        const matchSearch = !query || title.includes(query) || summary.includes(query);
        const matchPriority = priority === 'all' || cardPriority === priority;
        const matchCategory = category === 'all' || cardCategory === category;

        card.style.display = (matchSearch && matchPriority && matchCategory) ? '' : 'none';
    });
}

// Priority radio styling
document.querySelectorAll('.priority-option').forEach(opt => {
    opt.addEventListener('click', function() {
        document.querySelectorAll('.priority-option').forEach(o => {
            o.classList.remove('border-blue-500', 'bg-blue-500/10', 'text-blue-400');
            o.classList.add('border-white/10', 'text-muted-foreground');
        });
        this.classList.remove('border-white/10', 'text-muted-foreground');
        this.classList.add('border-blue-500', 'bg-blue-500/10', 'text-blue-400');
    });
});

// File list preview
document.getElementById('ann-attachments').addEventListener('change', function() {
    const list = document.getElementById('file-list');
    if (this.files.length > 0) {
        list.innerHTML = '';
        list.classList.remove('hidden');
        Array.from(this.files).forEach(f => {
            const size = f.size >= 1048576 ? (f.size/1048576).toFixed(1)+' MB' : (f.size/1024).toFixed(1)+' KB';
            list.innerHTML += `<div class="flex items-center justify-between rounded-lg bg-muted/50 px-3 py-2">
                <span class="text-sm truncate">${f.name}</span>
                <span class="text-xs text-muted-foreground ml-2">${size}</span>
            </div>`;
        });
    } else {
        list.classList.add('hidden');
    }
});

// Pinned toggle
document.getElementById('active-toggle').addEventListener('click', function() {
    const cb = document.getElementById('ann-pinned');
    cb.checked = !cb.checked;
    this.style.backgroundColor = cb.checked ? '#f59e0b' : '';
    this.querySelector('span').style.transform = cb.checked ? 'translateX(1.25rem)' : 'translateX(0.125rem)';
});

// Modal
document.getElementById('new-announcement-btn').addEventListener('click', function() {
    document.getElementById('modal-title').textContent = 'New Announcement';
    document.getElementById('modal-subtitle').textContent = 'Create a new announcement for your audience';
    document.getElementById('announcement-form').action = '<?= base_url('admin/announcements/store') ?>';
    document.getElementById('announcement-id').value = '';
    document.getElementById('ann-title').value = '';
    document.getElementById('ann-summary').value = '';
    document.getElementById('ann-content').value = '';
    document.getElementById('ann-message').value = '';
    document.getElementById('ann-author').value = '';
    document.getElementById('ann-tags').value = '';
    document.getElementById('ann-expires').value = '';
    document.getElementById('ann-category').value = 'general';
    document.getElementById('ann-pinned').checked = false;
    document.getElementById('active-toggle').style.backgroundColor = '';
    document.getElementById('active-toggle').querySelector('span').style.transform = 'translateX(0.125rem)';
    document.getElementById('file-list').classList.add('hidden');
    // Reset priority to medium
    document.querySelectorAll('.priority-option').forEach(o => {
        o.classList.remove('border-blue-500', 'bg-blue-500/10', 'text-blue-400');
        o.classList.add('border-white/10', 'text-muted-foreground');
    });
    const medOpt = document.querySelector('.priority-option[data-value="medium"]');
    if (medOpt) {
        medOpt.classList.remove('border-white/10', 'text-muted-foreground');
        medOpt.classList.add('border-blue-500', 'bg-blue-500/10', 'text-blue-400');
    }
    document.getElementById('announcement-modal').classList.remove('hidden');
});

function editAnnouncement(id) {
    fetch('<?= base_url('admin/announcements') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => {});
    // Fetch announcement data via a simple approach - use data stored in DOM
    const card = document.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    document.getElementById('modal-title').textContent = 'Edit Announcement';
    document.getElementById('modal-subtitle').textContent = 'Update the announcement details';
    document.getElementById('announcement-form').action = '<?= base_url('admin/announcements/update/') ?>' + id;
    document.getElementById('announcement-id').value = id;
    document.getElementById('ann-title').value = card.dataset.title || '';
    document.getElementById('ann-summary').value = card.dataset.summary || '';
    document.getElementById('ann-content').value = card.dataset.content || '';
    document.getElementById('ann-message').value = card.dataset.message || '';
    document.getElementById('ann-author').value = card.dataset.author || '';
    document.getElementById('ann-category').value = card.dataset.category || 'general';
    document.getElementById('ann-expires').value = card.dataset.expires || '';
    document.getElementById('ann-pinned').checked = card.dataset.pinned === '1';
    document.getElementById('active-toggle').style.backgroundColor = card.dataset.pinned === '1' ? '#f59e0b' : '';
    document.getElementById('active-toggle').querySelector('span').style.transform = card.dataset.pinned === '1' ? 'translateX(1.25rem)' : 'translateX(0.125rem)';

    // Set priority
    document.querySelectorAll('.priority-option').forEach(o => {
        o.classList.remove('border-blue-500', 'bg-blue-500/10', 'text-blue-400');
        o.classList.add('border-white/10', 'text-muted-foreground');
    });
    const priOpt = document.querySelector(`.priority-option[data-value="${card.dataset.priority || 'medium'}"]`);
    if (priOpt) {
        priOpt.classList.remove('border-white/10', 'text-muted-foreground');
        priOpt.classList.add('border-blue-500', 'bg-blue-500/10', 'text-blue-400');
    }

    document.getElementById('file-list').classList.add('hidden');
    document.getElementById('announcement-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('announcement-modal').classList.add('hidden');
}

function toggleActive(id) {
    if (!confirm('Toggle this announcement\'s active status?')) return;
    fetch('<?= base_url('admin/announcements/toggle/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', [csrfHeaderName]: currentCsrfHash },
        body: '<?= csrf_token() ?>=' + currentCsrfHash
    }).then(r => r.json()).then(data => { currentCsrfHash = data.csrf; if (data.success) location.reload(); });
}

function togglePin(id) {
    fetch('<?= base_url('admin/announcements/toggle-pin/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', [csrfHeaderName]: currentCsrfHash },
        body: '<?= csrf_token() ?>=' + currentCsrfHash
    }).then(r => r.json()).then(data => { currentCsrfHash = data.csrf; if (data.success) location.reload(); });
}

function deleteAnnouncement(id) {
    if (!confirm('Delete this announcement permanently?')) return;
    fetch('<?= base_url('admin/announcements/delete/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded', [csrfHeaderName]: currentCsrfHash },
        body: '<?= csrf_token() ?>=' + currentCsrfHash
    }).then(r => r.json()).then(data => { currentCsrfHash = data.csrf; if (data.success) location.reload(); });
}

document.getElementById('announcement-form').addEventListener('submit', function(e) {
    e.preventDefault();
    // Sync content to message field for backward compat
    document.getElementById('ann-message').value = document.getElementById('ann-content').value;

    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: currentCsrfHash },
        body: formData
    }).then(r => r.json()).then(data => {
        currentCsrfHash = data.csrf;
        if (data.success) location.reload();
        else if (data.errors) alert(Object.values(data.errors).join('\n'));
    });
});
</script>
<?= $this->endSection() ?>

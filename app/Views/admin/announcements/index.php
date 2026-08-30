<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Announcements</h1>
            <p class="text-muted-foreground">Send announcements to organizations</p>
        </div>
        <button id="new-announcement-btn"
            class="inline-flex items-center h-10 px-4 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            New Announcement
        </button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if ($announcements): ?>
        <div class="rounded-xl border bg-card shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Title</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">Target</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground hidden lg:table-cell">Sent By</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground hidden lg:table-cell">Status</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground hidden sm:table-cell">Date</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $a): ?>
                        <tr class="border-b border-white/5 hover:bg-muted/30 transition-colors">
                            <td class="p-4 align-middle">
                                <div class="font-medium"><?= esc($a['title']) ?></div>
                                <div class="text-xs text-muted-foreground mt-0.5 line-clamp-1"><?= esc(mb_substr(strip_tags($a['message']), 0, 80)) ?></div>
                            </td>
                            <td class="p-4 align-middle hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                    <?= $a['target_type'] === 'all' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : '' ?>
                                    <?= $a['target_type'] === 'campus' ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : '' ?>
                                    <?= $a['target_type'] === 'organization' ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : '' ?>">
                                    <?= ucfirst($a['target_type']) ?><?= $a['target_value'] ? ': ' . esc($a['target_value']) : '' ?>
                                </span>
                            </td>
                            <td class="p-4 align-middle text-muted-foreground hidden lg:table-cell"><?= esc($a['sender_name']) ?></td>
                            <td class="p-4 align-middle hidden lg:table-cell">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                    <?= $a['is_active'] ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-muted text-muted-foreground border-white/10' ?>">
                                    <?= $a['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="p-4 align-middle text-muted-foreground hidden sm:table-cell"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                            <td class="p-4 align-middle">
                                <div class="flex items-center gap-1">
                                    <button onclick="editAnnouncement(<?= $a['id'] ?>)"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-foreground hover:bg-white/5 transition-colors"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="toggleActive(<?= $a['id'] ?>)"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md <?= $a['is_active'] ? 'text-green-400 hover:text-green-300 hover:bg-green-500/10' : 'text-muted-foreground hover:text-foreground hover:bg-white/5' ?> transition-colors"
                                        title="<?= $a['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $a['is_active'] ? 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' ?>" />
                                        </svg>
                                    </button>
                                    <button onclick="deleteAnnouncement(<?= $a['id'] ?>)"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                        title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="rounded-xl border bg-card p-16 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-1">No announcements yet</h3>
            <p class="text-sm text-muted-foreground">Create your first announcement to get started</p>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div id="announcement-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="relative bg-card border border-white/10 rounded-xl shadow-2xl w-full max-w-lg mx-4 animate-scaleIn">
        <div class="p-6 border-b border-white/5">
            <h3 class="text-lg font-semibold" id="modal-title">New Announcement</h3>
        </div>
        <form id="announcement-form" method="POST" action="<?= base_url('admin/announcements/store') ?>">
            <input type="hidden" name="announcement_id" id="announcement-id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5 uppercase tracking-wider">Title</label>
                    <input type="text" name="title" id="ann-title" required
                        class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5 uppercase tracking-wider">Message</label>
                    <textarea name="message" id="ann-message" rows="5" required
                        class="w-full rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 resize-y"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5 uppercase tracking-wider">Target</label>
                    <select name="target_type" id="target-type" required
                        class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                        onchange="toggleTargetValue()">
                        <option value="all">All Organizations</option>
                        <option value="campus">By Campus</option>
                        <option value="organization">Specific Organization</option>
                    </select>
                </div>
                <div id="target-value-wrapper" class="hidden">
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5 uppercase tracking-wider" id="target-value-label">Select Campus</label>
                    <select name="target_value" id="target-value"
                        class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </select>
                </div>
            </div>
            <div class="p-6 pt-0 flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                    class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-card text-muted-foreground text-sm hover:bg-muted transition-colors">Cancel</button>
                <button type="submit"
                    class="inline-flex items-center h-9 px-4 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= csrf_header() ?>';
    let currentCsrfHash = '<?= csrf_hash() ?>';

    function toggleTargetValue() {
        const type = document.getElementById('target-type').value;
        const wrapper = document.getElementById('target-value-wrapper');
        const select = document.getElementById('target-value');
        const label = document.getElementById('target-value-label');

        if (type === 'all') {
            wrapper.classList.add('hidden');
            select.required = false;
            return;
        }

        wrapper.classList.remove('hidden');
        select.required = true;
        select.innerHTML = '<option value="">Loading...</option>';

        if (type === 'campus') {
            label.textContent = 'Select Campus';
            const campuses = <?= json_encode($campuses) ?>;
            select.innerHTML = '<option value="">Select Campus</option>' +
                campuses.map(c => `<option value="${c}">${c}</option>`).join('');
        } else {
            label.textContent = 'Select Organization';
            fetch('<?= base_url('admin/announcements/orgs') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: currentCsrfHash }
            })
            .then(r => r.json())
            .then(data => {
                currentCsrfHash = data.csrf;
                select.innerHTML = '<option value="">Select Organization</option>' +
                    data.organizations.map(o => `<option value="${o.id}">${o.name} (${o.acronym})</option>`).join('');
            });
        }
    }

    document.getElementById('new-announcement-btn').addEventListener('click', function() {
        document.getElementById('modal-title').textContent = 'New Announcement';
        document.getElementById('announcement-form').action = '<?= base_url('admin/announcements/store') ?>';
        document.getElementById('announcement-id').value = '';
        document.getElementById('ann-title').value = '';
        document.getElementById('ann-message').value = '';
        document.getElementById('target-type').value = 'all';
        toggleTargetValue();
        document.getElementById('announcement-modal').classList.remove('hidden');
    });

    function editAnnouncement(id) {
        fetch('<?= base_url('admin/announcements') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', [csrfHeaderName]: currentCsrfHash }
        }).then(() => {
            // We'll use a data attribute approach - redirect to modal via stored data
        });
        alert('Edit functionality: click the row data to modify. For now, use the inline approach.');
    }

    function closeModal() {
        document.getElementById('announcement-modal').classList.add('hidden');
    }

    function toggleActive(id) {
        if (!confirm('Toggle this announcement\'s active status?')) return;
        fetch('<?= base_url('admin/announcements/toggle') ?>/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                [csrfHeaderName]: currentCsrfHash
            },
            body: '<?= csrf_token() ?>=' + currentCsrfHash
        })
        .then(r => r.json())
        .then(data => {
            currentCsrfHash = data.csrf;
            if (data.success) location.reload();
        });
    }

    function deleteAnnouncement(id) {
        if (!confirm('Delete this announcement permanently?')) return;
        fetch('<?= base_url('admin/announcements/delete') ?>/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                [csrfHeaderName]: currentCsrfHash
            },
            body: '<?= csrf_token() ?>=' + currentCsrfHash
        })
        .then(r => r.json())
        .then(data => {
            currentCsrfHash = data.csrf;
            if (data.success) location.reload();
        });
    }

    document.getElementById('announcement-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                [csrfHeaderName]: currentCsrfHash
            },
            body: new URLSearchParams(formData)
        })
        .then(r => r.json())
        .then(data => {
            currentCsrfHash = data.csrf;
            if (data.success) {
                location.reload();
            } else if (data.errors) {
                alert(Object.values(data.errors).join('\n'));
            }
        });
    });
</script>
<?= $this->endSection() ?>

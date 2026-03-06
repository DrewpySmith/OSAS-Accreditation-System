<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Calendar of Activities'; ?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Page Header -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print bg-card p-4 rounded-xl border shadow-sm">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Calendar of Activities</h2>
            <p class="text-xs text-muted-foreground">Plan and track your organization's scheduled activities for A.Y.
                <?= esc($academic_year) ?>.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="addActivity()"
                class="inline-flex items-center h-9 px-4 rounded-md bg-green-600 text-white text-xs font-bold hover:bg-green-700 transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Activity
            </button>
            <button onclick="downloadPDF()"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                PDF
            </button>
            <button onclick="window.print()"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Content Card -->
    <div
        class="bg-card rounded-xl border shadow-sm overflow-hidden flex flex-col print:bg-white print:text-black print:border-none print:shadow-none">
        <!-- Official Header -->
        <div class="p-8 md:p-12 text-center border-b print:p-0 print:border-none">
            <div class="space-y-1 mb-6">
                <p class="uppercase text-[10px] tracking-widest font-bold text-muted-foreground print:text-gray-600">
                    Republic of the Philippines</p>
                <h3 class="text-lg font-black tracking-tight print:text-black">SULTAN KUDARAT STATE UNIVERSITY</h3>
                <p class="text-[10px] text-muted-foreground print:text-gray-600 font-medium">ACCESS, EJC Montilla, 9800
                    City of Tacurong</p>
                <p class="text-[10px] text-muted-foreground print:text-gray-600 font-medium">Province of Sultan Kudarat
                </p>
                <h2
                    class="text-3xl font-black mt-10 mb-6 tracking-[0.1em] border-y py-2 border-green-500/20 text-green-400 print:text-black print:border-black uppercase">
                    Calendar of Activities</h2>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-sm font-bold uppercase text-muted-foreground print:text-gray-600">A.Y.</span>
                    <input type="text" id="academic_year"
                        class="bg-transparent border-b-2 border-white/10 font-black text-center focus:border-green-500 outline-none w-[120px] print:border-black print:text-black"
                        value="<?= esc($academic_year) ?>" placeholder="2024-2025">
                </div>
                <div class="mt-4 pt-4 border-t border-white/5 print:border-black">
                    <p
                        class="text-xl font-black underline decoration-green-500/50 underline-offset-4 print:text-black print:decoration-black">
                        <?= isset($organization['name']) ? esc($organization['name']) : '' ?>
                    </p>
                    <p class="text-[10px] uppercase font-bold text-muted-foreground mt-1 print:text-gray-600">
                        (Organization Name)</p>
                </div>
            </div>
        </div>

        <!-- Activities Table -->
        <div class="p-4 md:p-8">
            <div class="rounded-lg border overflow-hidden print:border-black">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 print:bg-gray-100">
                        <tr>
                            <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black w-[140px]">
                                Date</th>
                            <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black">Activity
                                Title</th>
                            <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black w-[200px]">
                                Responsible Person</th>
                            <th class="h-10 px-4 text-left font-bold text-muted-foreground print:text-black w-[150px]">
                                Remarks</th>
                            <th
                                class="h-10 px-4 text-center font-bold text-muted-foreground print:text-black w-[120px]">
                                Status</th>
                            <th class="no-print h-10 px-4 text-right font-bold text-muted-foreground w-[80px]"></th>
                        </tr>
                    </thead>
                    <tbody id="activities-body" class="divide-y print:divide-black">
                        <?php if (empty($activities)): ?>
                            <tr>
                                <td colspan="6" class="p-12 text-center text-muted-foreground italic">
                                    No activities scheduled yet. Click "Add Activity" to start planning.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($activities as $activity):
                                $st = $activity['status'] ?? 'planned';
                                $badgeClasses = match ($st) {
                                    'completed' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                    'ongoing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'cancelled' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    default => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'
                                };
                                ?>
                                <tr class="group hover:bg-white/5 transition-colors print:hover:bg-transparent">
                                    <td class="p-4 font-medium text-muted-foreground print:text-black">
                                        <?= date('M d, Y', strtotime($activity['activity_date'])) ?>
                                    </td>
                                    <td class="p-4 font-bold print:text-black"><?= esc($activity['activity_title']) ?></td>
                                    <td class="p-4 text-muted-foreground print:text-black">
                                        <?= esc($activity['responsible_person']) ?></td>
                                    <td class="p-4 text-xs text-muted-foreground print:text-black">
                                        <?= esc($activity['remarks']) ?></td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $badgeClasses ?> print:border-black print:text-black">
                                            <?= ucfirst($st) ?>
                                        </span>
                                    </td>
                                    <td class="no-print p-4">
                                        <div class="flex justify-end gap-1">
                                            <button onclick="editActivity(<?= htmlspecialchars(json_encode($activity)) ?>)"
                                                class="h-8 w-8 rounded-md text-muted-foreground hover:text-blue-400 hover:bg-blue-500/10 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="deleteActivity(<?= $activity['id'] ?>)"
                                                class="h-8 w-8 rounded-md text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Signatures Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-20 mt-20 pb-8 print:text-black">
                <div class="text-center">
                    <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">Prepared
                        by:</p>
                    <input type="text" id="head_name"
                        class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                        placeholder="Head of Organization"
                        value="<?= isset($signatory['head_name']) ? esc($signatory['head_name']) : '' ?>">
                    <p
                        class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                        Head of Organization</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] font-bold uppercase text-muted-foreground mb-4 print:text-gray-600">Approved:
                    </p>
                    <input type="text" id="adviser_name"
                        class="w-full text-center font-black text-lg bg-transparent border-b-2 border-white/10 focus:border-green-500 outline-none print:border-black print:text-black"
                        placeholder="Adviser Name"
                        value="<?= isset($signatory['adviser_name']) ? esc($signatory['adviser_name']) : '' ?>">
                    <p
                        class="text-[10px] font-bold uppercase text-muted-foreground mt-2 print:text-black tracking-widest leading-none">
                        Adviser</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modernized Modal -->
<div id="activityModal"
    class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-in fade-in duration-200">
    <div
        class="bg-card w-full max-w-lg rounded-2xl border shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6 border-b flex items-center justify-between bg-muted/30">
            <h3 id="modalTitle" class="text-lg font-bold tracking-tight">Add Activity</h3>
            <button onclick="closeModal()" class="text-muted-foreground hover:text-foreground p-1 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="activityForm" class="p-6 space-y-4">
            <input type="hidden" id="activity_id">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Date</label>
                    <input type="date" id="activity_date" required
                        class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Status</label>
                    <select id="status"
                        class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
                        <option value="planned" selected>Planned</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Activity
                    Title</label>
                <input type="text" id="activity_title" required placeholder="e.g. Annual General Assembly"
                    class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Responsible
                    Person</label>
                <input type="text" id="responsible_person" required placeholder="e.g. John Doe, President"
                    class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Remarks</label>
                <textarea id="remarks" rows="3" placeholder="Additional details..."
                    class="w-full bg-background border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none transition-all resize-none"></textarea>
            </div>
            <div class="pt-4 flex gap-2 justify-end">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 rounded-lg border text-sm font-bold hover:bg-muted transition-all">Cancel</button>
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-green-600 text-white text-sm font-black hover:bg-green-700 transition-all shadow-lg shadow-green-600/20">
                    Save Activity
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfHeaderName = '<?= esc(config('Security')->headerName) ?>';
    let csrfToken = '<?= csrf_hash() ?>';

    function updateCsrfToken(next) {
        if (typeof next === 'string' && next.length > 0) csrfToken = next;
    }

    function addActivity() {
        document.getElementById('modalTitle').textContent = 'Add Activity';
        document.getElementById('activity_id').value = '';
        document.getElementById('activityForm').reset();
        document.getElementById('activityModal').classList.remove('hidden');
    }

    function editActivity(act) {
        document.getElementById('modalTitle').textContent = 'Edit Activity';
        document.getElementById('activity_id').value = act.id;
        document.getElementById('activity_date').value = act.activity_date;
        document.getElementById('activity_title').value = act.activity_title;
        document.getElementById('responsible_person').value = act.responsible_person;
        document.getElementById('remarks').value = act.remarks;
        document.getElementById('status').value = act.status;
        document.getElementById('activityModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('activityModal').classList.add('hidden');
    }

    document.getElementById('activityForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('activity_id').value;
        const url = id ? `/organization/calendar-activities/update/${id}` : '/organization/calendar-activities/store';

        const fd = new FormData();
        fd.append('academic_year', document.getElementById('academic_year').value);
        fd.append('activity_date', document.getElementById('activity_date').value);
        fd.append('activity_title', document.getElementById('activity_title').value);
        fd.append('responsible_person', document.getElementById('responsible_person').value);
        fd.append('remarks', document.getElementById('remarks').value);
        fd.append('status', document.getElementById('status').value);
        fd.append('head_name', document.getElementById('head_name').value);
        fd.append('adviser_name', document.getElementById('adviser_name').value);

        fetch(url, {
            method: 'POST',
            headers: { [csrfHeaderName]: csrfToken },
            body: fd
        })
            .then(r => r.json())
            .then(data => {
                updateCsrfToken(data.csrf);
                if (data.success) { location.reload(); }
                else { alert('Error: ' + data.message); }
            });
    });

    function deleteActivity(id) {
        if (confirm('Delete this activity?')) {
            window.location.href = `/organization/calendar-activities/delete/${id}`;
        }
    }

    function downloadPDF() {
        const ay = document.getElementById('academic_year').value;
        if (!ay) { alert('Enter A.Y.'); return; }
        window.location.href = `/organization/calendar-activities/download/${ay}`;
    }
</script>

<?= $this->endSection() ?>
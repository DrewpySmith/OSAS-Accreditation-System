<?= $this->extend('layouts/admin_modern') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-slide-up animation-delay-100">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Pending Registrations</h2>
            <p class="text-muted-foreground mt-1">Review new organization applications and adviser verification signatures.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/organizations') ?>" class="inline-flex items-center h-10 px-4 rounded-md border border-white/10 hover:bg-muted text-sm font-medium transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Active Organizations
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-400 text-sm animate-slide-up">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-red-400 text-sm animate-slide-up">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Table of Pending Signups -->
    <div class="rounded-xl border bg-card shadow-sm animate-slide-up animation-delay-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/30">
                        <th class="h-12 px-6 text-left font-medium text-muted-foreground">Organization Details</th>
                        <th class="h-12 px-6 text-left font-medium text-muted-foreground">Adviser Validation</th>
                        <th class="h-12 px-6 text-left font-medium text-muted-foreground">Officer Account</th>
                        <th class="h-12 px-6 text-left font-medium text-muted-foreground">Status</th>
                        <th class="h-12 px-6 text-right font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($registrations)): ?>
                        <tr>
                            <td colspan="5" class="h-24 text-center text-muted-foreground">No pending organization registrations found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $reg): ?>
                            <tr class="hover:bg-muted/10 transition-colors">
                                <!-- Organization info -->
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-foreground text-base"><?= esc($reg['name']) ?></div>
                                    <div class="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                                        <span class="px-2 py-0.5 rounded-full bg-white/5 border border-white/10 font-medium"><?= esc($reg['acronym'] ?: 'N/A') ?></span>
                                        <span>&bull;</span>
                                        <span><?= esc($reg['campus']) ?> Campus</span>
                                    </div>
                                    <?php if (!empty($reg['description'])): ?>
                                        <div class="text-xs text-muted-foreground mt-2 max-w-sm line-clamp-2"><?= esc($reg['description']) ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Adviser Validation info -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-foreground"><?= esc($reg['adviser_name']) ?></div>
                                    <div class="text-xs text-muted-foreground mt-0.5"><?= esc($reg['adviser_email']) ?></div>
                                    <?php if (!empty($reg['signature_path'])): ?>
                                        <button class="inline-flex items-center gap-1.5 text-xs text-emerald-400 hover:text-emerald-300 font-semibold mt-2.5 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-md transition-colors"
                                                onclick="previewSignature('<?= base_url($reg['signature_path']) ?>', '<?= esc($reg['adviser_name']) ?>')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                            View Digital Signature
                                        </button>
                                    <?php endif; ?>
                                </td>

                                <!-- Officer account details -->
                                <td class="px-6 py-4 text-muted-foreground">
                                    <div class="text-xs font-semibold text-foreground">Officer Email:</div>
                                    <div class="text-xs select-all mt-0.5"><?= esc($reg['officer_email']) ?></div>
                                    <div class="text-[11px] text-muted-foreground mt-1">Requested: <?= date('M d, Y h:i A', strtotime($reg['created_at'])) ?></div>
                                </td>

                                <!-- Status pill badges -->
                                <td class="px-6 py-4">
                                    <?php if ($reg['status'] === 'pending_adviser'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/25">Awaiting Adviser</span>
                                    <?php elseif ($reg['status'] === 'pending_admin'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">Signed by Adviser</span>
                                    <?php elseif ($reg['status'] === 'approved'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/25">Approved & Active</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/25">Rejected</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right">
                                    <?php if ($reg['status'] === 'pending_admin'): ?>
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Approve Form -->
                                            <form action="<?= base_url('admin/organizations/approve-registration/' . $reg['id']) ?>" method="POST" onsubmit="return confirm('Are you sure you want to approve this organization and auto-generate their accreditation checklist?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="inline-flex items-center h-9 px-3.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors gap-1.5">
                                                    Approve
                                                </button>
                                            </form>
                                            <!-- Reject Form -->
                                            <form action="<?= base_url('admin/organizations/reject-registration/' . $reg['id']) ?>" method="POST" onsubmit="return confirm('Are you sure you want to reject this organization application?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="inline-flex items-center h-9 px-3.5 rounded-md bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white text-xs font-semibold transition-all border border-red-500/20">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-muted-foreground">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Digital Signature Signature Preview Modal -->
<div id="sigModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity">
    <div class="bg-white text-slate-900 rounded-xl p-6 w-full max-w-sm shadow-2xl transform scale-95 transition-transform" id="sigModalCard">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h3 class="font-bold text-lg text-slate-800">Adviser Digital Signature</h3>
            <button onclick="closeSignature()" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
        </div>
        <p class="text-xs text-slate-500 mb-3">Digitally signed by <strong id="modalAdviserName"></strong> as the designated adviser commitment.</p>
        <div class="border rounded-lg bg-slate-50 p-4 flex items-center justify-center h-40 overflow-hidden">
            <img id="modalSigImage" src="" alt="Adviser Digital Signature" class="max-h-full max-w-full object-contain mix-blend-multiply">
        </div>
        <button onclick="closeSignature()" class="w-full mt-4 h-10 rounded-md bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition-colors">
            Close Preview
        </button>
    </div>
</div>

<script>
    function previewSignature(url, adviserName) {
        const modal = document.getElementById('sigModal');
        const card = document.getElementById('sigModalCard');
        document.getElementById('modalAdviserName').innerText = adviserName;
        document.getElementById('modalSigImage').src = url;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => card.classList.remove('scale-95'), 10);
    }

    function closeSignature() {
        const modal = document.getElementById('sigModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'My Submissions'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">My Submissions</h2>
            <p class="text-muted-foreground mt-1">View and manage documents you have submitted.</p>
        </div>
        <button id="quick-upload-trigger"
            class="inline-flex items-center h-10 px-4 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition-colors gap-2"
            onclick="document.getElementById('quick-upload-btn').click()">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Upload Document
        </button>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div
            class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div
            class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Submissions Table -->
    <div class="rounded-xl border bg-card shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm caption-bottom">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Type</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Title</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">A.Y.</th>
                        <th
                            class="h-11 px-4 text-left align-middle font-medium text-muted-foreground hidden md:table-cell">
                            File</th>
                        <th
                            class="h-11 px-4 text-left align-middle font-medium text-muted-foreground hidden lg:table-cell">
                            Submitted</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                        <th class="h-11 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="7" class="h-40 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-20" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">No documents submitted yet</p>
                                        <p class="text-xs mt-1">Click "Upload Document" to get started.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc):
                            $st = $doc['status'] ?? 'pending';
                            $badgeClasses = match ($st) {
                                'approved' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                'reviewed' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                default => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'
                            };
                            ?>
                            <tr class="border-b border-white/5 hover:bg-muted/30 transition-colors">
                                <td class="p-4 text-xs text-muted-foreground">
                                    <?= esc($document_types[$doc['document_type']] ?? str_replace('_', ' ', ucwords($doc['document_type']))) ?>
                                </td>
                                <td class="p-4 font-semibold"><?= esc($doc['document_title']) ?></td>
                                <td class="p-4 text-muted-foreground"><?= esc($doc['academic_year']) ?></td>
                                <td class="p-4 text-muted-foreground text-xs hidden md:table-cell max-w-[120px] truncate">
                                    <?= esc($doc['file_name']) ?></td>
                                <td class="p-4 text-muted-foreground hidden lg:table-cell">
                                    <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                                </td>
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $badgeClasses ?>">
                                        <?= ucfirst(esc($st)) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-end gap-1">
                                        <a href="<?= base_url('organization/submissions/view/' . $doc['id']) ?>" title="View"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-foreground hover:bg-white/5 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="<?= base_url('organization/submissions/download/' . $doc['id']) ?>"
                                            title="Download"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-green-400 hover:bg-green-500/10 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <?php if ($st === 'pending'): ?>
                                            <a href="<?= base_url('organization/submissions/delete/' . $doc['id']) ?>"
                                                title="Delete"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                                onclick="return confirm('Are you sure you want to delete this document?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="rounded-xl border bg-card p-4 shadow-sm">
        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">Status Legend</p>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center gap-1.5 text-sm">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border bg-yellow-500/10 text-yellow-400 border-yellow-500/20">Pending</span>
                <span class="text-muted-foreground">— Waiting for admin review</span>
            </div>
            <div class="flex items-center gap-1.5 text-sm">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border bg-blue-500/10 text-blue-400 border-blue-500/20">Reviewed</span>
                <span class="text-muted-foreground">— Admin has reviewed your document</span>
            </div>
            <div class="flex items-center gap-1.5 text-sm">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border bg-green-500/10 text-green-400 border-green-500/20">Approved</span>
                <span class="text-muted-foreground">— Document approved</span>
            </div>
            <div class="flex items-center gap-1.5 text-sm">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border bg-red-500/10 text-red-400 border-red-500/20">Rejected</span>
                <span class="text-muted-foreground">— Needs revision (check comments)</span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
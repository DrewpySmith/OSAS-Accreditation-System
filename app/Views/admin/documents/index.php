<?= $this->extend('layouts/admin_modern') ?>
<?php $title = 'Review Documents'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-slide-up animation-delay-100">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Document Submissions</h2>
            <p class="text-muted-foreground mt-1">Filter and review documents submitted by organizations.</p>
        </div>
        <a href="<?= base_url('admin/documents/print?' . http_build_query($selected_filters ?? [])) ?>"
           target="_blank"
           class="inline-flex items-center h-10 px-4 rounded-md border border-white/10 bg-card text-foreground text-sm font-medium hover:bg-muted transition-all hover:-translate-y-0.5 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Reports
        </a>
    </div>

    <!-- Filters Card -->
    <div class="rounded-xl border bg-card p-5 shadow-sm animate-slide-up animation-delay-200">
        <form method="GET" action="<?= base_url('admin/documents') ?>">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-1">
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5" for="organization_id">Organization</label>
                    <select name="organization_id" id="organization_id"
                            class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All</option>
                        <?php foreach (($organizations ?? []) as $org): ?>
                            <option value="<?= esc($org['id']) ?>" <?= (($selected_filters['organization_id'] ?? '') == $org['id']) ? 'selected' : '' ?>>
                                <?= esc($org['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5" for="campus">Campus</label>
                    <select name="campus" id="campus"
                            class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All</option>
                        <?php foreach (($campuses ?? []) as $campus): ?>
                            <option value="<?= esc($campus) ?>" <?= (($selected_filters['campus'] ?? '') === $campus) ? 'selected' : '' ?>>
                                <?= esc($campus) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5" for="status">Status</label>
                    <select name="status" id="status"
                            class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All</option>
                        <?php foreach (['pending', 'reviewed', 'approved', 'rejected'] as $st): ?>
                            <option value="<?= esc($st) ?>" <?= (($selected_filters['status'] ?? '') === $st) ? 'selected' : '' ?>>
                                <?= ucfirst($st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5" for="document_type">Document Type</label>
                    <select name="document_type" id="document_type"
                            class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <?php foreach (($document_types ?? []) as $val => $label): ?>
                            <option value="<?= esc($val) ?>" <?= (($selected_filters['document_type'] ?? '') === $val) ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1.5" for="academic_year">Academic Year</label>
                    <select name="academic_year" id="academic_year"
                            class="w-full h-9 rounded-md border border-white/10 bg-background text-foreground px-2.5 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Years</option>
                        <?php foreach (($academic_years ?? []) as $ay): ?>
                            <option value="<?= esc($ay['year']) ?>" <?= (($selected_filters['academic_year'] ?? '') === $ay['year']) ? 'selected' : '' ?>>
                                A.Y. <?= esc($ay['year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 h-9 inline-flex items-center justify-center rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors">
                        Apply
                    </button>
                    <a href="<?= base_url('admin/documents') ?>"
                       class="h-9 px-3 inline-flex items-center justify-center rounded-md border border-white/10 bg-card text-muted-foreground text-sm hover:bg-muted transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="rounded-xl border bg-card shadow overflow-hidden animate-slide-up animation-delay-300">
        <div class="overflow-x-auto">
            <table class="w-full text-sm caption-bottom">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">ID</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Title</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Type</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Organization</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Campus</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">A.Y.</th>
                        <th class="h-11 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                        <th class="h-11 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="8" class="h-32 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p>No documents found for the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <?php $st = $doc['status'] ?? 'pending'; ?>
                            <tr class="border-b border-white/5 hover:bg-muted/30 transition-colors">
                                <td class="p-4 font-medium text-muted-foreground">#<?= esc($doc['id']) ?></td>
                                <td class="p-4 font-semibold max-w-xs truncate"><?= esc($doc['document_title']) ?></td>
                                <td class="p-4 text-muted-foreground text-xs"><?= esc($document_types[$doc['document_type']] ?? str_replace('_', ' ', ucwords($doc['document_type']))) ?></td>
                                <td class="p-4 font-medium"><?= esc($doc['org_name'] ?? '') ?></td>
                                <td class="p-4">
                                    <span class="text-blue-400 font-medium"><?= esc($doc['campus'] ?? 'N/A') ?></span>
                                </td>
                                <td class="p-4 text-muted-foreground"><?= esc($doc['academic_year'] ?? '') ?></td>
                                <td class="p-4">
                                    <?php
                                    $badgeClasses = match($st) {
                                        'approved' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'reviewed' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        default     => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'
                                    };
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $badgeClasses ?>">
                                        <?= ucfirst(esc($st)) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= base_url('admin/documents/view/' . $doc['id']) ?>"
                                           class="inline-flex items-center h-8 px-3 rounded-md text-xs font-medium border border-white/10 text-muted-foreground hover:text-foreground hover:bg-muted transition-colors gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View
                                        </a>
                                        <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>"
                                           class="inline-flex items-center h-8 px-3 rounded-md text-xs font-medium bg-blue-600/10 text-blue-400 hover:bg-blue-600/20 transition-colors gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/org_modern') ?>
<?php $title = 'Accreditation Status'; ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Accreditation Status</h2>
            <p class="text-muted-foreground mt-1">Track your document requirements for A.Y. <?= esc($academic_year) ?>.
            </p>
        </div>
        <span
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-600/10 border border-green-500/20 text-green-400">
            A.Y. <?= esc($academic_year) ?>
        </span>
    </div>

    <!-- Progress Card -->
    <div class="rounded-xl border bg-card p-6 shadow-sm">
        <div class="flex items-end justify-between mb-3">
            <div>
                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Overall Completion</p>
                <p class="text-sm text-muted-foreground mt-0.5">Submit all required documents to complete accreditation
                </p>
            </div>
            <span
                class="text-4xl font-black <?= $progress >= 100 ? 'text-green-400' : ($progress >= 50 ? 'text-yellow-400' : 'text-red-400') ?>">
                <?= $progress ?>%
            </span>
        </div>
        <div class="w-full bg-white/5 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-700 ease-out <?= $progress >= 100 ? 'bg-green-500' : ($progress >= 50 ? 'bg-yellow-500' : 'bg-red-500') ?>"
                style="width: <?= $progress ?>%"></div>
        </div>
        <p class="text-xs text-muted-foreground mt-2">
            <?= $progress >= 100 ? 'All requirements met — you are fully accredited!' : (round($progress) . '% complete — keep submitting documents.') ?>
        </p>
    </div>

    <!-- Requirement Checklist -->
    <div class="rounded-xl border bg-card p-6 shadow-sm">
        <h3 class="text-base font-semibold mb-4 uppercase tracking-wider text-muted-foreground">Requirement Checklist
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php
            $items = array_filter($document_types ?? [], function ($key) {
                return $key !== 'other';
            }, ARRAY_FILTER_USE_KEY);
            foreach ($items as $field => $label):
                $isVerified = isset($checklist[$field]) && $checklist[$field] == 1;
                ?>
                <div
                    class="flex items-center gap-3 p-4 rounded-lg border transition-all <?= $isVerified ? 'border-green-500/30 bg-green-500/5' : 'border-white/5 bg-background' ?>">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center <?= $isVerified ? 'bg-green-500 shadow-lg shadow-green-500/30' : 'bg-muted border-2 border-white/10' ?>">
                        <?php if ($isVerified): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        <?php else: ?>
                            <div class="w-2 h-2 rounded-full bg-muted-foreground/30"></div>
                        <?php endif; ?>
                    </div>
                    <span class="text-sm font-medium <?= $isVerified ? 'text-green-300' : 'text-muted-foreground' ?>">
                        <?= esc($label) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Certificate Section -->
    <?php if ($is_complete): ?>
        <div class="rounded-xl border border-green-500/30 bg-green-500/5 p-8 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-500/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-green-300 mb-2">Congratulations!</h3>
            <p class="text-muted-foreground mb-6 max-w-md mx-auto">
                Your organization is officially accredited for A.Y. <?= esc($academic_year) ?>. You can now download or
                print your official certificate.
            </p>
            <div class="flex gap-3 justify-center">
                <a href="<?= base_url('organization/accreditation/download/' . $academic_year) ?>"
                    class="inline-flex items-center h-10 px-5 rounded-md bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </a>
                <a href="<?= base_url('organization/accreditation/print/' . $academic_year) ?>" target="_blank"
                    class="inline-flex items-center h-10 px-5 rounded-md border border-white/10 bg-card text-foreground text-sm font-semibold hover:bg-muted transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Certificate
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-white/10 bg-card p-8 text-center shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-foreground mb-2">Certificate Locked</h3>
            <p class="text-muted-foreground mb-4 max-w-md mx-auto">
                Complete all the requirements above to unlock your Certificate of Accreditation. Items are verified by USG
                Admin upon submission.
            </p>
            <div
                class="inline-flex items-center gap-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                <span class="text-red-400">Required: 100%</span>
                <span>•</span>
                <span class="text-green-400">Current: <?= $progress ?>%</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
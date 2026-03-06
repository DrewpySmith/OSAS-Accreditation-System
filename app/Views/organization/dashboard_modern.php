<?= $this->extend('layouts/org_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Welcome back,
                <?= session()->get('username') ?>!
            </h1>
            <p class="text-muted-foreground">
                <?= $organization['name'] ?? 'Organization Dashboard' ?>
            </p>
        </div>
        <div class="flex gap-3">
            <button
                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                Download PDF
            </button>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:shadow-md transition-all group">
            <div class="p-6">
                <div
                    class="w-12 h-12 rounded-lg bg-secondary/10 flex items-center justify-center mb-4 group-hover:bg-secondary/20">
                    <span class="text-2xl">📤</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Upload Docs</h3>
                <p class="text-sm text-muted-foreground mb-4">Submit accreditation requirements for admin review.</p>
                <button onclick="document.getElementById('quick-upload-btn').click()"
                    class="text-secondary font-semibold text-sm hover:underline">Start Upload →</button>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:shadow-md transition-all group">
            <div class="p-6">
                <div
                    class="w-12 h-12 rounded-lg bg-secondary/10 flex items-center justify-center mb-4 group-hover:bg-secondary/20">
                    <span class="text-2xl">📋</span>
                </div>
                <h3 class="text-xl font-bold mb-2">View Submissions</h3>
                <p class="text-sm text-muted-foreground mb-4">Track the review status of your uploaded files.</p>
                <a href="<?= base_url('organization/submissions') ?>"
                    class="text-secondary font-semibold text-sm hover:underline">View History →</a>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm hover:shadow-md transition-all group">
            <div class="p-6">
                <div
                    class="w-12 h-12 rounded-lg bg-secondary/10 flex items-center justify-center mb-4 group-hover:bg-secondary/20">
                    <span class="text-2xl">📜</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Accreditation Status</h3>
                <p class="text-sm text-muted-foreground mb-4">Check your overall progress for this academic year.</p>
                <a href="<?= base_url('organization/accreditation') ?>"
                    class="text-secondary font-semibold text-sm hover:underline">Check Progress →</a>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-2xl font-bold tracking-tight mb-4 flex items-center gap-2">
            Accreditation Checklist
            <span
                class="text-xs font-normal bg-secondary/10 text-secondary px-2 py-1 rounded-full border border-secondary/20">A.Y.
                <?= esc($academic_year) ?>
            </span>
        </h2>
        <div class="rounded-xl border bg-card text-card-foreground shadow overflow-hidden">
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 divide-y md:divide-y-0 md:divide-x border-b last:border-b-0">
                <?php
                $items = array_filter($document_types ?? [], function ($key) {
                    return $key !== 'other';
                }, ARRAY_FILTER_USE_KEY);

                foreach ($items as $field => $label):
                    $isCompleted = isset($checklist[$field]) && $checklist[$field] == 1;
                    ?>
                    <div class="p-4 flex items-center gap-4 hover:bg-muted/30 transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 
                            <?= $isCompleted ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground' ?>">
                            <?= $isCompleted ? '✓' : '○' ?>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-medium truncate">
                                <?= $label ?>
                            </p>
                            <p class="text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                <?= $isCompleted ? 'Verified' : 'Pending' ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
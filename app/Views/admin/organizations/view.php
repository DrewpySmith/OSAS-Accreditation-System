<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('admin/organizations') ?>"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-white/10 bg-card hover:bg-muted h-10 w-10 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                    <i data-lucide="building-2" class="w-6 h-6 text-blue-500"></i>
                    <?= esc($organization['name']) ?>
                </h1>
                <p class="text-sm text-muted-foreground"><?= esc($organization['acronym'] ?: 'Organization Details') ?>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <?php
            $statusClasses = [
                'active' => 'bg-green-500/10 text-green-500 border-green-500/20',
                'inactive' => 'bg-red-500/10 text-red-500 border-red-500/20',
                'suspended' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20'
            ];
            $currentStatusClass = $statusClasses[$organization['status']] ?? 'bg-muted text-muted-foreground';
            ?>
            <span
                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold <?= $currentStatusClass ?>">
                <?= ucfirst($organization['status']) ?>
            </span>
            <div class="h-8 w-px bg-white/10 mx-2"></div>
            <a href="<?= base_url('admin/organizations/edit/' . $organization['id']) ?>"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-white/10 bg-card hover:bg-muted h-10 px-4 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4 mr-2"></i> Edit
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-xl border bg-card p-6 shadow-sm border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Documents</p>
                    <p class="text-3xl font-bold mt-1"><?= esc($organization['total_documents'] ?? 0) ?></p>
                </div>
                <div class="p-3 bg-blue-500/10 rounded-lg text-blue-500">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border bg-card p-6 shadow-sm border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Activities</p>
                    <p class="text-3xl font-bold mt-1"><?= esc($organization['total_activities'] ?? 0) ?></p>
                </div>
                <div class="p-3 bg-green-500/10 rounded-lg text-green-500">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border bg-card p-6 shadow-sm border-l-4 border-l-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Financial Reports</p>
                    <p class="text-3xl font-bold mt-1"><?= esc($organization['total_financial_reports'] ?? 0) ?></p>
                </div>
                <div class="p-3 bg-purple-500/10 rounded-lg text-purple-500">
                    <i data-lucide="pie-chart" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden text-card-foreground">
                <div class="p-6 border-b border-white/5 bg-muted/30">
                    <h3 class="font-semibold text-lg flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                        General Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Full Name
                                </p>
                                <p class="text-sm font-medium"><?= esc($organization['name']) ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Acronym
                                </p>
                                <p class="text-sm font-medium"><?= esc($organization['acronym'] ?: 'N/A') ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Campus</p>
                                <p class="text-sm highlight-text"><?= esc($organization['campus'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Description</p>
                                <p class="text-sm">
                                    <?= nl2br(esc($organization['description'] ?: 'No description provided.')) ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                    Registration Date</p>
                                <p class="text-sm font-medium italic">
                                    <?= !empty($organization['created_at']) ? date('F d, Y', strtotime($organization['created_at'])) : 'N/A' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accreditation Checklist -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden text-card-foreground">
                <div class="p-6 border-b border-white/5 bg-muted/30 flex justify-between items-center">
                    <h3 class="font-semibold text-lg flex items-center gap-2">
                        <i data-lucide="check-square" class="w-5 h-5 text-green-400"></i>
                        Accreditation Checklist (<?= esc($academic_year) ?>)
                    </h3>
                    <div class="text-xs text-muted-foreground italic">Toggle status manually</div>
                </div>
                <div class="divide-y divide-white/5">
                    <?php
                    $items = array_filter($document_types ?? [], function ($key) {
                        return $key !== 'other';
                    }, ARRAY_FILTER_USE_KEY);

                    foreach ($items as $field => $label):
                        $isCompleted = isset($checklist[$field]) && $checklist[$field] == 1;
                        ?>
                        <div class="p-4 flex items-start gap-4 hover:bg-muted/30 transition-colors <?= $isCompleted ? 'bg-green-500/5' : '' ?>"
                            id="item_<?= $field ?>">
                            <div class="pt-1">
                                <input type="checkbox" id="chk_<?= $field ?>" <?= $isCompleted ? 'checked' : '' ?>
                                    onchange="updateChecklist('<?= $field ?>', this.checked)"
                                    class="w-5 h-5 rounded border-white/10 bg-background text-blue-600 focus:ring-blue-500">
                            </div>
                            <div class="flex-1 space-y-1">
                                <label for="chk_<?= $field ?>"
                                    class="text-sm font-semibold cursor-pointer <?= $isCompleted ? 'text-green-500' : '' ?>">
                                    <?= $label ?>
                                </label>
                                <?php if ($field === 'financial_report'): ?>
                                    <p class="text-xs text-muted-foreground leading-relaxed">Signed by outgoing
                                        treasurer/auditor, Faculty Adviser, and Dean/Campus Director.</p>
                                <?php elseif ($field === 'accomplishment_report'): ?>
                                    <p class="text-xs text-muted-foreground leading-relaxed">Includes Results of Evaluation of
                                        Activities.</p>
                                <?php endif; ?>
                            </div>
                            <?php if ($isCompleted): ?>
                                <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar: Accreditation Status & Certificate -->
        <div class="space-y-6">
            <?php
            $checklistModel = new \App\Models\OrganizationChecklistModel();
            $isAccredited = $checklistModel->isComplete($checklist);
            ?>
            <div
                class="rounded-xl border shadow-lg overflow-hidden <?= $isAccredited ? 'bg-gradient-to-br from-blue-600 to-indigo-700 border-transparent text-white' : 'bg-card text-card-foreground' ?>">
                <div class="p-6 text-center space-y-4">
                    <div
                        class="mx-auto w-16 h-16 rounded-full flex items-center justify-center <?= $isAccredited ? 'bg-white/20' : 'bg-muted' ?>">
                        <i data-lucide="award"
                            class="w-8 h-8 <?= $isAccredited ? 'text-white' : 'text-muted-foreground opacity-30' ?>"></i>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold italic">
                            <?= $isAccredited ? 'Fully Accredited' : 'Pending Requirements' ?></h3>
                        <p class="text-xs opacity-80 mt-1">
                            <?= $isAccredited
                                ? 'This organization has fulfilled all USG accreditation requirements for the ' . esc($academic_year) . ' academic year.'
                                : 'Accreditation is pending completion of the checklist and document verification.' ?>
                        </p>
                    </div>

                    <?php if ($isAccredited): ?>
                        <div class="pt-4 space-y-3">
                            <a href="<?= base_url('admin/organizations/certificate/' . $organization['id'] . '/' . $academic_year) ?>"
                                class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium bg-white text-blue-700 hover:bg-white/90 h-11 px-4 transition-colors">
                                <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download PDF
                            </a>
                            <a href="<?= base_url('admin/organizations/certificate/print/' . $organization['id'] . '/' . $academic_year) ?>"
                                target="_blank"
                                class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium border border-white/20 hover:bg-white/10 h-11 px-4 transition-colors">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print Certificate
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="pt-4 px-4">
                            <div class="flex justify-between text-[10px] mb-1 font-bold">
                                <span>PROGRESS</span>
                                <span>
                                    <?php
                                    $completedCount = 0;
                                    foreach ($items as $f => $l)
                                        if (isset($checklist[$f]) && $checklist[$f] == 1)
                                            $completedCount++;
                                    $totalCount = count($items);
                                    echo round(($completedCount / $totalCount) * 100) . '%';
                                    ?>
                                </span>
                            </div>
                            <div class="h-1.5 w-full bg-muted rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full transition-all duration-500"
                                    style="width: <?= ($completedCount / $totalCount) * 100 ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links / External Resource Card -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 space-y-4">
                <h4 class="text-sm font-semibold uppercase tracking-wider text-muted-foreground italic">Admin Toolbox
                </h4>
                <div class="space-y-2">
                    <a href="<?= base_url('admin/statistics/organization/' . $organization['id']) ?>"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-muted transition-colors text-sm group">
                        <i data-lucide="line-chart"
                            class="w-4 h-4 text-blue-400 group-hover:scale-110 transition-transform"></i>
                        Performance Analytics
                    </a>
                    <a href="<?= base_url('admin/documents?org_id=' . $organization['id']) ?>"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-muted transition-colors text-sm group">
                        <i data-lucide="files"
                            class="w-4 h-4 text-purple-400 group-hover:scale-110 transition-transform"></i>
                        Filter All Submissions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCsrfHash = '<?= csrf_hash() ?>';

    async function updateChecklist(field, value) {
        const checkbox = document.getElementById('chk_' + field);
        const itemDiv = document.getElementById('item_' + field);

        try {
            const response = await fetch('<?= base_url("admin/organizations/checklist/" . $organization["id"]) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': currentCsrfHash
                },
                body: `field=${field}&value=${value ? 1 : 0}&academic_year=<?= $academic_year ?>`
            });

            const result = await response.json();

            if (result.status === 'success') {
                currentCsrfHash = result.csrf;
                location.reload(); // Refresh to update progress and UI states
            } else {
                alert('Failed to update: ' + (result.message || 'Unknown error'));
                checkbox.checked = !value;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred.');
            checkbox.checked = !value;
        }
    }
</script>
<?= $this->endSection() ?>
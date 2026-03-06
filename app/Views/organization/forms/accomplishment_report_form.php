<?= $this->extend('layouts/org_modern') ?>
<?php
$title = (isset($report) ? 'Edit' : 'Create') . ' Accomplishment Report';
?>

<?= $this->section('content') ?>

<div class="space-y-6 max-w-5xl mx-auto pb-20">
    <!-- Action Bar -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print bg-card p-4 rounded-xl border shadow-sm sticky top-4 z-40 backdrop-blur-md bg-card/90">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Accomplishment Report</h2>
            <p class="text-xs text-muted-foreground">Document and present your organization's successes.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="saveDraft()"
                class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Save Draft
            </button>
            <?php if (isset($report)): ?>
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center h-9 px-3 rounded-md border border-white/10 bg-background text-foreground text-xs font-semibold hover:bg-muted transition-colors gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </button>
            <?php endif; ?>
            <button type="button" onclick="submitReport()"
                class="inline-flex items-center h-9 px-6 rounded-md bg-green-600 text-white text-xs font-black hover:bg-green-700 transition-colors gap-2 shadow-lg shadow-green-600/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Submit Report
            </button>
        </div>
    </div>

    <!-- Main Form Content -->
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
                    class="text-3xl font-black mt-10 mb-6 tracking-[0.1em] border-y py-2 border-green-500/20 text-green-400 print:text-black print:border-black uppercase italic">
                    Accomplishment Report</h2>
            </div>
        </div>

        <form id="mainForm"
            action="<?= base_url('organization/accomplishment-report/' . (isset($report) ? 'update/' . $report['id'] : 'store')) ?>"
            method="POST" enctype="multipart/form-data" class="p-8 md:p-12 space-y-12">
            <?= csrf_field() ?>
            <input type="hidden" name="status" id="statusInput" value="draft">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Academic Year -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Academic Year
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="academic_year" required placeholder="e.g. 2024-2025"
                        class="w-full bg-background border rounded-lg px-4 py-3 font-bold focus:ring-2 focus:ring-green-500 outline-none transition-all"
                        value="<?= old('academic_year', $report['academic_year'] ?? '') ?>">
                </div>
                <!-- Organization Name (Read Only) -->
                <div class="space-y-2 opacity-60">
                    <label
                        class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Organization</label>
                    <input type="text"
                        class="w-full bg-muted/50 border rounded-lg px-4 py-3 font-bold cursor-not-allowed outline-none"
                        value="<?= isset($organization['name']) ? esc($organization['name']) : '' ?>" readonly>
                </div>
            </div>

            <!-- Section A: Activity Title -->
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white text-xs font-black">A</span>
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-foreground">Activity Title</h3>
                </div>
                <input type="text" name="activity_title" required placeholder="Enter the complete title of the activity"
                    class="w-full bg-background border rounded-xl px-4 py-4 text-lg font-black focus:ring-2 focus:ring-green-500 outline-none transition-all"
                    value="<?= old('activity_title', $report['activity_title'] ?? '') ?>">
            </div>

            <!-- Section B: Narrative Report -->
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white text-xs font-black">B</span>
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-foreground">Narrative Report</h3>
                </div>
                <textarea name="narrative_report" required
                    placeholder="Provide complete details about the activity, objectives met, and overall outcome..."
                    class="w-full bg-background border rounded-xl px-6 py-6 text-sm leading-relaxed focus:ring-2 focus:ring-green-500 outline-none transition-all min-h-[300px]"><?= old('narrative_report', $report['narrative_report'] ?? '') ?></textarea>
            </div>

            <!-- Supporting Documents -->
            <div class="space-y-8 pt-8 border-t border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-green-400 text-center">Supporting
                    Documents</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 no-print">
                    <!-- Section C: Pictorials -->
                    <div class="space-y-3 group">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-muted-foreground flex justify-between">
                            C. Pictorials
                            <span class="text-[9px] text-green-500 font-bold">* Images</span>
                        </label>
                        <div class="relative group">
                            <input type="file" name="pictorials[]" multiple accept="image/*" class="hidden" id="file_c"
                                onchange="updateFileCount('c', this)">
                            <label for="file_c"
                                class="flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl bg-background/50 hover:bg-muted/50 hover:border-green-500/50 transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground mb-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-[10px] font-black uppercase text-muted-foreground tracking-widest"
                                    id="count_c">Upload Photos</span>
                            </label>
                        </div>
                        <?php if (isset($report['pictorials'])): ?>
                            <div class="flex flex-wrap gap-1 p-2 rounded-lg bg-muted/20">
                                <?php foreach (json_decode($report['pictorials'], true) as $file): ?>
                                    <span
                                        class="text-[9px] px-2 py-0.5 rounded bg-background border text-muted-foreground truncate max-w-full">📄
                                        <?= esc(basename($file)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Section D: Activity Designs -->
                    <div class="space-y-3 group">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-muted-foreground flex justify-between">
                            D. Activity Designs
                            <span class="text-[9px] text-blue-500 font-bold">* Documents</span>
                        </label>
                        <div class="relative group">
                            <input type="file" name="activity_designs[]" multiple accept=".pdf,.doc,.docx"
                                class="hidden" id="file_d" onchange="updateFileCount('d', this)">
                            <label for="file_d"
                                class="flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl bg-background/50 hover:bg-muted/50 hover:border-blue-500/50 transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground mb-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-[10px] font-black uppercase text-muted-foreground tracking-widest"
                                    id="count_d">Upload Designs</span>
                            </label>
                        </div>
                        <?php if (isset($report['activity_designs'])): ?>
                            <div class="flex flex-wrap gap-1 p-2 rounded-lg bg-muted/20">
                                <?php foreach (json_decode($report['activity_designs'], true) as $file): ?>
                                    <span
                                        class="text-[9px] px-2 py-0.5 rounded bg-background border text-muted-foreground truncate max-w-full">📄
                                        <?= esc(basename($file)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Section E: Evaluation Sheets -->
                    <div class="space-y-3 group">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-muted-foreground flex justify-between">
                            E. Evaluation Sheets
                            <span class="text-[9px] text-purple-500 font-bold">* Feedback</span>
                        </label>
                        <div class="relative group">
                            <input type="file" name="evaluation_sheets[]" multiple accept=".pdf,.doc,.docx"
                                class="hidden" id="file_e" onchange="updateFileCount('e', this)">
                            <label for="file_e"
                                class="flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl bg-background/50 hover:bg-muted/50 hover:border-purple-500/50 transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-muted-foreground mb-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                </svg>
                                <span class="text-[10px] font-black uppercase text-muted-foreground tracking-widest"
                                    id="count_e">Upload Sheets</span>
                            </label>
                        </div>
                        <?php if (isset($report['evaluation_sheets'])): ?>
                            <div class="flex flex-wrap gap-1 p-2 rounded-lg bg-muted/20">
                                <?php foreach (json_decode($report['evaluation_sheets'], true) as $file): ?>
                                    <span
                                        class="text-[9px] px-2 py-0.5 rounded bg-background border text-muted-foreground truncate max-w-full">📄
                                        <?= esc(basename($file)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFileCount(section, input) {
        const count = input.files.length;
        const el = document.getElementById('count_' + section);
        if (count > 0) {
            el.textContent = count + ' file(s) selected';
            el.classList.add('text-foreground');
        } else {
            el.textContent = 'Upload Files';
            el.classList.remove('text-foreground');
        }
    }

    function saveDraft() {
        document.getElementById('statusInput').value = 'draft';
        document.getElementById('mainForm').submit();
    }

    function submitReport() {
        if (confirm('Submit this accomplishment report for approval?')) {
            document.getElementById('statusInput').value = 'submitted';
            document.getElementById('mainForm').submit();
        }
    }
</script>

<?= $this->endSection() ?>
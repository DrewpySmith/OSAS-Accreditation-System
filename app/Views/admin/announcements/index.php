<?= $this->extend('layouts/admin_modern') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Announcements</h1>
            <p class="text-muted-foreground">Compliance broadcasts — drafts, scheduling, multi-target, attachments</p>
        </div>
        <button id="new-announcement-btn" class="inline-flex items-center h-10 px-4 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Announcement
        </button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="GET" class="rounded-xl border bg-card p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs font-medium text-muted-foreground uppercase">Search</label>
            <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Title..." class="w-full h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
        </div>
        <div>
            <label class="text-xs font-medium text-muted-foreground uppercase">Priority</label>
            <select name="priority" class="h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
                <option value="">All</option>
                <option value="critical" <?= ($filters['priority']??'')==='critical'?'selected':'' ?>>Critical</option>
                <option value="urgent" <?= ($filters['priority']??'')==='urgent'?'selected':'' ?>>Urgent</option>
                <option value="normal" <?= ($filters['priority']??'')==='normal'?'selected':'' ?>>Normal</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-muted-foreground uppercase">Status</label>
            <select name="status" class="h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
                <option value="">All</option>
                <option value="draft" <?= ($filters['status']??'')==='draft'?'selected':'' ?>>Draft</option>
                <option value="published" <?= ($filters['status']??'')==='published'?'selected':'' ?>>Published</option>
                <option value="expired" <?= ($filters['status']??'')==='expired'?'selected':'' ?>>Expired</option>
                <option value="archived" <?= ($filters['status']??'')==='archived'?'selected':'' ?>>Archived</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-muted-foreground uppercase">Campus</label>
            <select name="campus" class="h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
                <option value="">All</option>
                <?php foreach ($campuses as $c): ?><option value="<?= esc($c) ?>" <?= ($filters['campus']??'')===$c?'selected':'' ?>><?= esc($c) ?></option><?php endforeach; ?>
            </select>
        </div>
        <button class="h-9 px-4 rounded-md bg-white/10 text-sm hover:bg-white/15">Filter</button>
        <a href="<?= base_url('admin/announcements') ?>" class="h-9 px-3 inline-flex items-center rounded-md border border-white/10 text-sm">Clear</a>
    </form>

    <?php if ($announcements): ?>
        <div class="rounded-xl border bg-card shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground">Title</th>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground hidden md:table-cell">Targets</th>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground">Priority</th>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground hidden lg:table-cell">Status</th>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground">Ack</th>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground hidden sm:table-cell">Date</th>
                        <th class="h-11 px-4 text-left font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $a): ?>
                        <tr class="border-b border-white/5 hover:bg-muted/30">
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <?php if ($a['is_pinned']): ?><span class="text-yellow-400" title="Pinned">📌</span><?php endif; ?>
                                    <span class="w-2 h-2 rounded-full flex-shrink-0 <?= $a['priority']==='critical'?'bg-red-500':($a['priority']==='urgent'?'bg-yellow-500':'bg-green-500') ?>"></span>
                                    <span class="font-medium line-clamp-1"><?= esc($a['title']) ?></span>
                                </div>
                                <div class="text-xs text-muted-foreground line-clamp-1 mt-1"><?= esc(mb_substr(strip_tags($a['message']),0,70)) ?></div>
                                <?php if ($a['action_label']): ?><span class="text-[11px] text-blue-400">↗ <?= esc($a['action_label']) ?></span><?php endif; ?>
                                <?php if (!empty($a['attachments'])): ?><span class="text-[11px] text-muted-foreground">📎 <?= count($a['attachments']) ?> file(s)</span><?php endif; ?>
                            </td>
                            <td class="p-4 hidden md:table-cell">
                                <div class="flex flex-wrap gap-1 max-w-[220px]">
                                    <?php if ($a['target_type']==='all'): ?><span class="px-2 py-0.5 rounded-full text-xs bg-blue-500/10 text-blue-400 border border-blue-500/20">All</span>
                                    <?php else: ?>
                                        <?php foreach (($a['targets']??[]) as $t): ?><span class="px-2 py-0.5 rounded-full text-xs border <?= $t['target_type']==='campus'?'bg-purple-500/10 text-purple-400 border-purple-500/20':'bg-orange-500/10 text-orange-400 border-orange-500/20' ?>"><?= esc($t['target_value']) ?></span><?php endforeach; ?>
                                        <?php if (empty($a['targets']) && $a['target_value']): ?><span class="px-2 py-0.5 rounded-full text-xs bg-white/5 border border-white/10"><?= esc($a['target_value']) ?></span><?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4"><span class="px-2 py-0.5 rounded-full text-xs font-medium border <?= $a['priority']==='critical'?'bg-red-500/15 text-red-400 border-red-500/20':($a['priority']==='urgent'?'bg-yellow-500/15 text-yellow-400 border-yellow-500/20':'bg-green-500/10 text-green-400 border-green-500/20') ?>"><?= ucfirst($a['priority']) ?></span></td>
                            <td class="p-4 hidden lg:table-cell"><span class="px-2 py-0.5 rounded-full text-xs border <?= $a['status']==='published'?'bg-green-500/10 text-green-400 border-green-500/20':($a['status']==='draft'?'bg-yellow-500/10 text-yellow-400 border-yellow-500/20':'bg-muted text-muted-foreground border-white/10') ?>"><?= ucfirst($a['status']) ?></span></td>
                            <td class="p-4">
                                <button onclick="openCompliance(<?= $a['id'] ?>)" class="text-xs px-2 py-1 rounded-md bg-white/5 hover:bg-white/10 border border-white/10">
                                    <?= $a['ackedCount'] ?>/<?= $a['totalTargets'] ?> <span class="text-muted-foreground">(<?= $a['totalTargets']? round($a['ackedCount']*100/$a['totalTargets']):0 ?>%)</span>
                                </button>
                            </td>
                            <td class="p-4 hidden sm:table-cell text-muted-foreground text-xs"><?= date('M d, Y', strtotime($a['created_at'])) ?><?php if($a['expires_at']): ?><br><span class="text-[11px]">exp <?= date('M d', strtotime($a['expires_at'])) ?></span><?php endif; ?></td>
                            <td class="p-4">
                                <div class="flex gap-1">
                                    <button onclick="editAnnouncement(<?= $a['id'] ?>)" class="h-8 w-8 grid place-items-center rounded-md hover:bg-white/5" title="Edit">✎</button>
                                    <button onclick="toggleActive(<?= $a['id'] ?>)" class="h-8 w-8 grid place-items-center rounded-md hover:bg-white/5" title="Toggle">◐</button>
                                    <button onclick="resendAnnouncement(<?= $a['id'] ?>)" class="h-8 w-8 grid place-items-center rounded-md hover:bg-white/5" title="Resend email">✉</button>
                                    <button onclick="deleteAnnouncement(<?= $a['id'] ?>)" class="h-8 w-8 grid place-items-center rounded-md hover:bg-red-500/10 text-red-400" title="Delete">🗑</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="rounded-xl border bg-card p-16 text-center"><h3 class="font-semibold">No announcements</h3><p class="text-sm text-muted-foreground">Create one to get started</p></div>
    <?php endif; ?>
</div>

<div id="announcement-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm animate-fadeIn" onclick="closeModal()"></div>
    <div class="bg-card rounded-3xl shadow-2xl overflow-hidden border border-white/10 text-card-foreground flex flex-col animate-modalSlideIn" style="position:fixed; left:50%; top:50%; transform:translate(-50%,-50%); width:calc(100% - 2rem); max-width:42rem; max-height:90vh;">
        <div id="ann-success" class="absolute inset-0 bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center z-50 hidden animate-fadeIn">
            <div class="text-center space-y-4">
                <div class="inline-flex w-20 h-20 bg-white rounded-full items-center justify-center shadow-xl animate-scaleIn"><svg class="w-12 h-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                <h3 class="text-3xl font-bold text-white">Saving...</h3><p class="text-blue-100">Publishing announcement</p>
            </div>
        </div>
        <div class="relative bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8 flex-shrink-0">
            <button onclick="closeModal()" class="absolute top-6 right-6 text-white/80 hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg></div>
                <div><h3 id="modal-title" class="text-2xl font-bold text-white tracking-tight">New Announcement</h3><p class="text-blue-100/80 text-sm mt-0.5">Broadcast to organizations by campus or individually</p></div>
            </div>
            <div class="flex items-center gap-2 mt-8"><div id="step-ind-1" class="flex-1 h-1.5 rounded-full bg-white shadow-[0_0_10px_rgba(255,255,255,0.5)] transition-all duration-500"></div><div id="step-ind-2" class="flex-1 h-1.5 rounded-full bg-white/20 transition-all duration-500"></div></div>
        </div>
        <form id="announcement-form" method="POST" action="<?= base_url('admin/announcements/store') ?>" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            <input type="hidden" name="announcement_id" id="announcement-id">
            <div class="p-8 relative min-h-[380px]">
                <div id="ann-error" class="hidden p-4 rounded-xl text-sm bg-red-500/10 border border-red-500/20 text-red-500 mb-6 animate-fadeIn"></div>
                <div id="ann-step-1" class="space-y-6 transition-all duration-500 opacity-100 translate-x-0">
                    <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Title *</label><input type="text" name="title" id="ann-title" required placeholder="e.g. Accreditation Deadline Extended" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm"></div>
                    <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Message *</label><textarea name="message" id="ann-message" rows="4" required placeholder="Write the announcement details..." class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm resize-none"></textarea></div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Priority</label><select name="priority" id="ann-priority" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm"><option value="normal">Normal</option><option value="urgent">Urgent</option><option value="critical">Critical</option></select></div>
                        <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Status</label><select name="status" id="ann-status" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm"><option value="published">Published</option><option value="draft">Draft</option><option value="expired">Expired</option><option value="archived">Archived</option></select></div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Publish at</label><input type="datetime-local" name="publish_at" id="ann-publish" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm"></div>
                        <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Expires at</label><input type="datetime-local" name="expires_at" id="ann-expires" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm"></div>
                    </div>
                    <div class="flex items-center gap-2"><input type="checkbox" name="is_pinned" id="ann-pinned" value="1" class="rounded border-white/10"><label for="ann-pinned" class="text-sm font-medium">Pin to top (critical announcements)</label></div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Action label</label><input type="text" name="action_label" id="ann-action-label" placeholder="Go to Submissions" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm"></div>
                        <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Action URL</label><input type="text" name="action_url" id="ann-action-url" placeholder="/organization/submissions" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm"></div>
                    </div>
                    <div class="flex justify-end pt-2"><button type="button" onclick="annNextStep()" class="group flex items-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/20 transition-all active:scale-95 text-sm">Next <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button></div>
                </div>
                <div id="ann-step-2" class="space-y-6 transition-all duration-500 opacity-0 translate-x-12 absolute inset-x-8 pointer-events-none">
                    <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Target</label><select name="target_type" id="target-type" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm" onchange="toggleTargetInputs()"><option value="all">All Organizations</option><option value="campus">By Campus (multi)</option><option value="organization">Specific Organizations (multi)</option><option value="mixed">Mixed (campuses + orgs)</option></select></div>
                    <div id="campus-checkboxes" class="hidden space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Select Campuses</label><div class="grid grid-cols-2 gap-2"><?php foreach ($campuses as $c): ?><label class="flex items-center gap-2 text-sm border border-white/10 rounded-xl px-3 py-2.5 bg-muted/50 cursor-pointer"><input type="checkbox" name="target_campuses[]" value="<?= esc($c) ?>" class="campus-cb rounded"> <?= esc($c) ?></label><?php endforeach; ?></div></div>
                    <div id="org-multiselect" class="hidden space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Select Organizations</label><select name="target_orgs[]" id="target-orgs" multiple size="6" class="w-full rounded-xl border border-white/10 bg-muted/50 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"></select><p class="text-[11px] text-muted-foreground ml-1">Hold Ctrl/Cmd to select multiple</p></div>
                    <div class="space-y-2"><label class="text-sm font-semibold text-muted-foreground ml-1">Attachments (max 3, 10MB each)</label><input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" class="w-full px-4 py-3 rounded-xl border border-white/10 bg-muted/50 text-sm file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:text-xs"><div id="existing-attachments" class="space-y-1 text-xs"></div><p class="text-[11px] text-muted-foreground ml-1">PDF, JPG, PNG, WebP, DOC/DOCX</p></div>
                    <div class="flex items-center gap-3 pt-4">
                        <button type="button" onclick="annPrevStep()" class="flex items-center gap-2 px-6 py-3 bg-muted hover:bg-muted/80 text-muted-foreground rounded-xl font-bold transition-all active:scale-95 text-sm"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Back</button>
                        <button type="submit" id="ann-submit-btn" class="flex-1 flex items-center justify-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/20 transition-all active:scale-95 text-sm"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> <span id="ann-submit-text">Create Announcement</span></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="compliance-drawer" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeCompliance()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-card border-l border-white/10 p-6 overflow-y-auto">
        <div class="flex justify-between items-center mb-4"><h3 class="font-semibold">Compliance</h3><button onclick="closeCompliance()" class="h-8 w-8 grid place-items-center rounded-md hover:bg-white/5">✕</button></div>
        <div id="compliance-content" class="space-y-4 text-sm"></div>
    </div>
</div>

<script>
const csrfHeaderName='<?= csrf_header() ?>'; let csrfHash='<?= csrf_hash() ?>';
let annStep=1;
function setAnnStep(n){
    annStep=n;
    const s1=document.getElementById('ann-step-1');
    const s2=document.getElementById('ann-step-2');
    if(n===1){
        s1.className='space-y-6 transition-all duration-500 opacity-100 translate-x-0';
        s2.className='space-y-6 transition-all duration-500 opacity-0 translate-x-12 absolute inset-x-8 pointer-events-none';
    } else {
        s1.className='space-y-6 transition-all duration-500 opacity-0 -translate-x-12 absolute inset-x-8 pointer-events-none';
        s2.className='space-y-6 transition-all duration-500 opacity-100 translate-x-0';
    }
    document.getElementById('step-ind-1').className = n>=1 ? 'flex-1 h-1.5 rounded-full bg-white shadow-[0_0_10px_rgba(255,255,255,0.5)] transition-all duration-500' : 'flex-1 h-1.5 rounded-full bg-white/20 transition-all duration-500';
    document.getElementById('step-ind-2').className = n>=2 ? 'flex-1 h-1.5 rounded-full bg-white shadow-[0_0_10px_rgba(255,255,255,0.5)] transition-all duration-500' : 'flex-1 h-1.5 rounded-full bg-white/20 transition-all duration-500';
}
function annNextStep(){
    const title=document.getElementById('ann-title').value.trim();
    const msg=document.getElementById('ann-message').value.trim();
    if(!title||!msg){ const e=document.getElementById('ann-error'); e.textContent='Title and message are required.'; e.classList.remove('hidden'); return; }
    document.getElementById('ann-error').classList.add('hidden');
    setAnnStep(2);
}
function annPrevStep(){ setAnnStep(1); }
function toggleTargetInputs(){
    const v=document.getElementById('target-type').value;
    document.getElementById('campus-checkboxes').classList.toggle('hidden', !(v==='campus'||v==='mixed'));
    document.getElementById('org-multiselect').classList.toggle('hidden', !(v==='organization'||v==='mixed'));
    if(v==='organization'||v==='mixed') loadOrgs();
}
function loadOrgs(){
    fetch('<?= base_url('admin/announcements/orgs') ?>',{headers:{'X-Requested-With':'XMLHttpRequest',[csrfHeaderName]:csrfHash}}).then(r=>r.json()).then(d=>{csrfHash=d.csrf; const s=document.getElementById('target-orgs'); s.innerHTML=d.organizations.map(o=>`<option value="${o.id}">${o.name} (${o.acronym} - ${o.campus})</option>`).join('');});
}
document.getElementById('new-announcement-btn').addEventListener('click',()=>{
    document.getElementById('modal-title').textContent='New Announcement';
    document.getElementById('ann-submit-text').textContent='Create Announcement';
    document.getElementById('announcement-form').action='<?= base_url('admin/announcements/store') ?>';
    document.getElementById('announcement-id').value='';
    document.getElementById('ann-title').value=''; document.getElementById('ann-message').value='';
    document.getElementById('ann-priority').value='normal'; document.getElementById('ann-status').value='published';
    document.getElementById('ann-pinned').checked=false; document.getElementById('ann-publish').value=''; document.getElementById('ann-expires').value='';
    document.getElementById('ann-action-label').value=''; document.getElementById('ann-action-url').value='';
    document.getElementById('target-type').value='all'; toggleTargetInputs();
    document.querySelectorAll('.campus-cb').forEach(cb=>cb.checked=false);
    document.getElementById('existing-attachments').innerHTML='';
    document.getElementById('ann-error').classList.add('hidden');
    document.getElementById('ann-success').classList.add('hidden');
    setAnnStep(1);
    document.getElementById('announcement-modal').classList.remove('hidden');
});
function editAnnouncement(id){
    fetch('<?= base_url('admin/announcements/get') ?>/'+id,{headers:{'X-Requested-With':'XMLHttpRequest',[csrfHeaderName]:csrfHash}}).then(r=>r.json()).then(d=>{
        csrfHash=d.csrf; const a=d.announcement;
        document.getElementById('modal-title').textContent='Edit Announcement';
        document.getElementById('ann-submit-text').textContent='Update Announcement';
        document.getElementById('announcement-form').action='<?= base_url('admin/announcements/update') ?>/'+id;
        document.getElementById('ann-title').value=a.title; document.getElementById('ann-message').value=a.message;
        document.getElementById('ann-priority').value=a.priority; document.getElementById('ann-status').value=a.status;
        document.getElementById('ann-pinned').checked=a.is_pinned==1;
        document.getElementById('ann-publish').value=a.publish_at? a.publish_at.replace(' ','T').slice(0,16):'';
        document.getElementById('ann-expires').value=a.expires_at? a.expires_at.replace(' ','T').slice(0,16):'';
        document.getElementById('ann-action-label').value=a.action_label||''; document.getElementById('ann-action-url').value=a.action_url||'';
        const targets=a.targets||[]; const hasCampus=targets.some(t=>t.target_type==='campus'); const hasOrg=targets.some(t=>t.target_type==='organization'); const hasAll=targets.some(t=>t.target_type==='all');
        if(hasAll) document.getElementById('target-type').value='all';
        else if(hasCampus&&hasOrg) document.getElementById('target-type').value='mixed';
        else if(hasCampus) document.getElementById('target-type').value='campus';
        else if(hasOrg) document.getElementById('target-type').value='organization';
        else document.getElementById('target-type').value=a.target_type;
        toggleTargetInputs();
        setTimeout(()=>{
            document.querySelectorAll('.campus-cb').forEach(cb=>cb.checked=targets.some(t=>t.target_type==='campus'&&t.target_value===cb.value));
            if(hasOrg||document.getElementById('target-type').value==='mixed'||document.getElementById('target-type').value==='organization'){
                loadOrgs();
                setTimeout(()=>{
                    const s=document.getElementById('target-orgs');
                    const orgIds=targets.filter(t=>t.target_type==='organization').map(t=>t.target_value);
                    Array.from(s.options).forEach(o=>{ if(orgIds.includes(o.value)) o.selected=true; });
                },400);
            }
        },200);
        const ea=document.getElementById('existing-attachments');
        ea.innerHTML=(a.attachments||[]).map(at=>`<label class="flex items-center gap-2 p-2 rounded-md bg-muted/30 border border-white/5"><input type="checkbox" name="remove_attachments[]" value="${at.id}"> ${at.filename||at.file_name} <span class="text-muted-foreground">(${((at.filesize||at.file_size||0)/1024).toFixed(0)}KB)</span> <span class="ml-auto text-[11px]">remove?</span></label>`).join('') || '<span class="text-muted-foreground">No attachments</span>';
        document.getElementById('ann-error').classList.add('hidden');
        document.getElementById('ann-success').classList.add('hidden');
        setAnnStep(1);
        document.getElementById('announcement-modal').classList.remove('hidden');
    });
}
function closeModal(){ document.getElementById('announcement-modal').classList.add('hidden'); document.getElementById('ann-success').classList.add('hidden'); }
function toggleActive(id){ if(!confirm('Toggle status?')) return; fetch('<?= base_url('admin/announcements/toggle') ?>/'+id,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded',[csrfHeaderName]:csrfHash},body:'<?= csrf_token() ?>='+csrfHash}).then(r=>r.json()).then(d=>{csrfHash=d.csrf; if(d.success) location.reload();});}
function deleteAnnouncement(id){ if(!confirm('Delete?')) return; fetch('<?= base_url('admin/announcements/delete') ?>/'+id,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded',[csrfHeaderName]:csrfHash},body:'<?= csrf_token() ?>='+csrfHash}).then(r=>r.json()).then(d=>{csrfHash=d.csrf; if(d.success) location.reload();});}
function resendAnnouncement(id){ fetch('<?= base_url('admin/announcements/resend') ?>/'+id,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded',[csrfHeaderName]:csrfHash},body:'<?= csrf_token() ?>='+csrfHash}).then(r=>r.json()).then(d=>{csrfHash=d.csrf; alert(d.success?'Resent':'Failed');});}
function openCompliance(id){ document.getElementById('compliance-drawer').classList.remove('hidden'); document.getElementById('compliance-content').innerHTML='Loading...'; fetch('<?= base_url('admin/announcements/compliance') ?>/'+id,{headers:{'X-Requested-With':'XMLHttpRequest',[csrfHeaderName]:csrfHash}}).then(r=>r.json()).then(d=>{csrfHash=d.csrf; const c=d.compliance; document.getElementById('compliance-content').innerHTML=`<div class="p-3 rounded-lg bg-white/5 border border-white/10"><div class="font-medium">${c.ackedCount}/${c.total} acknowledged (${c.total?Math.round(c.ackedCount*100/c.total):0}%)</div><div class="w-full h-2 bg-white/10 rounded-full mt-2"><div class="h-2 bg-green-500 rounded-full" style="width:${c.total?c.ackedCount*100/c.total:0}%"></div></div></div><h4 class="font-medium text-green-400">Acknowledged (${c.acked.length})</h4>${c.acked.map(o=>`<div class="p-2 rounded-md bg-green-500/10 border border-green-500/20 text-xs">${o.name} (${o.acronym} - ${o.campus})</div>`).join('')||'<p class="text-muted-foreground text-xs">None</p>'}<h4 class="font-medium text-red-400 mt-3">Pending (${c.pending.length})</h4>${c.pending.map(o=>`<div class="p-2 rounded-md bg-red-500/10 border border-red-500/20 text-xs">${o.name} (${o.acronym} - ${o.campus})</div>`).join('')||'<p class="text-muted-foreground text-xs">All acknowledged</p>'}`;});}
function closeCompliance(){ document.getElementById('compliance-drawer').classList.add('hidden'); }
document.getElementById('announcement-form').addEventListener('submit',function(e){
    e.preventDefault(); const fd=new FormData(this);
    const btn=document.getElementById('ann-submit-btn'); const txt=document.getElementById('ann-submit-text'); const orig=txt.textContent;
    btn.disabled=true; txt.textContent='Processing...';
    const errEl=document.getElementById('ann-error');
    fetch(this.action,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest',[csrfHeaderName]:csrfHash},body:fd}).then(r=>r.json()).then(d=>{
        csrfHash=d.csrf;
        if(d.success){
            document.getElementById('ann-success').classList.remove('hidden');
            setTimeout(()=>location.reload(),1200);
        } else {
            errEl.textContent=d.errors? Object.values(d.errors).join(', ') : (d.message||'Failed to save');
            errEl.classList.remove('hidden');
            btn.disabled=false; txt.textContent=orig;
        }
    }).catch(()=>{ errEl.textContent='Network error'; errEl.classList.remove('hidden'); btn.disabled=false; txt.textContent=orig; });
});
</script>
<?= $this->endSection() ?>

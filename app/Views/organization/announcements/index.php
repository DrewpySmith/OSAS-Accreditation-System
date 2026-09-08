<?= $this->extend('layouts/org_modern') ?>
<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between gap-4">
        <div><h1 class="text-3xl font-bold tracking-tight">Announcements</h1><p class="text-muted-foreground">Critical broadcasts require explicit acknowledgement</p></div>
        <?php if ($unread_count>0): ?><span class="h-7 px-3 inline-flex items-center rounded-full text-xs font-bold bg-red-500/10 border border-red-500/20 text-red-400"><?= $unread_count ?> pending ack</span><?php endif; ?>
    </div>

    <form method="GET" class="flex flex-wrap gap-2 items-center">
        <select name="filter" class="h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
            <option value="all" <?= $filter==='all'?'selected':'' ?>>All</option>
            <option value="pending" <?= $filter==='pending'?'selected':'' ?>>Pending Ack</option>
            <option value="acknowledged" <?= $filter==='acknowledged'?'selected':'' ?>>Acknowledged</option>
            <option value="unread" <?= $filter==='unread'?'selected':'' ?>>Unread</option>
        </select>
        <select name="priority" class="h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
            <option value="">All priorities</option>
            <option value="critical" <?= $priority==='critical'?'selected':'' ?>>Critical</option>
            <option value="urgent" <?= $priority==='urgent'?'selected':'' ?>>Urgent</option>
            <option value="normal" <?= $priority==='normal'?'selected':'' ?>>Normal</option>
        </select>
        <input type="text" name="search" value="<?= esc($search??'') ?>" placeholder="Search title..." class="h-9 rounded-md border border-white/10 bg-background px-2.5 text-sm">
        <button class="h-9 px-3 rounded-md bg-white/10 text-sm">Filter</button>
        <a href="<?= base_url('organization/announcements') ?>" class="h-9 px-3 inline-flex items-center rounded-md border border-white/10 text-sm">Clear</a>
    </form>

    <?php if (!empty($pinned)): ?>
        <div class="space-y-3">
            <?php foreach ($pinned as $a): ?>
                <div class="rounded-xl border-2 <?= $a['priority']==='critical'?'border-red-500/40 bg-red-500/10':'border-yellow-500/40 bg-yellow-500/10' ?> p-5">
                    <div class="flex items-start gap-3">
                        <span class="text-xl"><?= $a['priority']==='critical'?'🚨':'⚠️' ?></span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap"><span class="px-2 py-0.5 rounded-full text-[11px] font-bold border <?= $a['priority']==='critical'?'bg-red-500 text-white border-red-600':'bg-yellow-500 text-black border-yellow-600' ?>"><?= strtoupper($a['priority']) ?></span><?php if($a['is_pinned']): ?><span class="px-2 py-0.5 rounded-full text-[11px] bg-white/10 border border-white/10">Pinned</span><?php endif; ?><span class="text-[11px] text-muted-foreground"><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></span><?php if($a['expires_at']): ?><span class="text-[11px] text-red-400">Expires <?= date('M d', strtotime($a['expires_at'])) ?></span><?php endif; ?></div>
                            <h3 class="font-bold mt-1"><?= esc($a['title']) ?></h3>
                            <p class="text-sm text-muted-foreground mt-1 leading-relaxed whitespace-pre-wrap"><?= esc($a['message']) ?></p>
                            <?php if (!empty($a['attachments'])): ?><div class="mt-2 flex flex-wrap gap-2"><?php foreach($a['attachments'] as $at): ?><a href="<?= base_url('organization/announcements/attachment/'.$at['id']) ?>" class="text-xs px-2 py-1 rounded-md bg-white/10 border border-white/10 hover:bg-white/15">📎 <?= esc($at['filename'] ?? $at['file_name'] ?? 'file') ?></a><?php endforeach; ?></div><?php endif; ?>
                            <?php if ($a['action_label'] && $a['action_url']): ?><a href="<?= esc($a['action_url']) ?>" class="inline-flex mt-3 h-8 px-3 rounded-md bg-green-600 text-white text-xs font-medium items-center"><?= esc($a['action_label']) ?> →</a><?php endif; ?>
                        </div>
                        <button onclick="acknowledge(<?= $a['id'] ?>, this)" class="h-9 px-4 rounded-md bg-green-600 text-white text-sm font-medium hover:bg-green-700 flex-shrink-0">Acknowledge</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($announcements): ?>
        <div class="space-y-3">
            <?php foreach ($announcements as $a): ?>
                <div class="rounded-xl border p-5 transition-all hover:border-white/20 <?= $a['is_ack']?'border-white/5 bg-card opacity-70':'border-white/10 bg-card' ?>">
                    <div class="flex items-start gap-3">
                        <span class="w-2 h-2 rounded-full mt-2 flex-shrink-0 <?= $a['is_ack']?'bg-muted-foreground/30':($a['priority']==='critical'?'bg-red-500 animate-pulse':($a['priority']==='urgent'?'bg-yellow-500':'bg-green-500')) ?>"></span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap"><h3 class="font-semibold text-sm"><?= esc($a['title']) ?></h3><span class="px-2 py-0.5 rounded-full text-[10px] border <?= $a['priority']==='critical'?'bg-red-500/15 text-red-400 border-red-500/20':($a['priority']==='urgent'?'bg-yellow-500/15 text-yellow-400 border-yellow-500/20':'bg-green-500/10 text-green-400 border-green-500/20') ?>"><?= ucfirst($a['priority']) ?></span><span class="text-[10px] text-muted-foreground"><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></span><?php if($a['is_ack']): ?><span class="text-[10px] text-green-400">✓ Acknowledged</span><?php endif; ?></div>
                            <p class="text-sm text-muted-foreground leading-relaxed mt-1 whitespace-pre-wrap"><?= esc($a['message']) ?></p>
                            <?php if (!empty($a['attachments'])): ?><div class="mt-2 flex flex-wrap gap-2"><?php foreach($a['attachments'] as $at): ?><a href="<?= base_url('organization/announcements/attachment/'.$at['id']) ?>" class="text-xs px-2 py-1 rounded-md bg-white/5 border border-white/10 hover:bg-white/10">📎 <?= esc($at['filename'] ?? $at['file_name'] ?? 'file') ?></a><?php endforeach; ?></div><?php endif; ?>
                            <?php if ($a['action_label'] && $a['action_url']): ?><a href="<?= esc($a['action_url']) ?>" class="inline-flex mt-2 h-7 px-2.5 rounded-md bg-white/10 text-xs items-center hover:bg-white/15"><?= esc($a['action_label']) ?> →</a><?php endif; ?>
                        </div>
                        <?php if (!$a['is_ack']): ?><button onclick="acknowledge(<?= $a['id'] ?>, this)" class="h-8 px-3 rounded-md bg-green-600 text-white text-xs font-medium hover:bg-green-700 flex-shrink-0">Acknowledge</button><?php else: ?><span class="text-xs text-green-400 flex-shrink-0">✓ Done</span><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (empty($pinned)): ?>
        <div class="rounded-xl border bg-card p-16 text-center"><h3 class="font-semibold">No announcements</h3><p class="text-sm text-muted-foreground">No announcements match your filters</p></div>
    <?php endif; ?>
</div>
<script>
const csrfHeaderName='<?= csrf_header() ?>'; let csrfHash='<?= csrf_hash() ?>';
function acknowledge(id, btn){
    btn.disabled=true; btn.textContent='...';
    fetch('<?= base_url('organization/announcements/acknowledge') ?>/'+id,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded',[csrfHeaderName]:csrfHash},body:'<?= csrf_token() ?>='+csrfHash}).then(r=>r.json()).then(d=>{
        csrfHash=d.csrf;
        if(d.success){ btn.textContent='✓ Done'; btn.className='text-xs text-green-400 flex-shrink-0'; btn.disabled=true; const card=btn.closest('.rounded-xl'); if(card) { card.classList.add('opacity-70'); card.querySelector('.w-2')?.classList.replace('bg-green-500','bg-muted-foreground/30'); } }
        else { btn.disabled=false; btn.textContent='Acknowledge'; }
    });
}
setInterval(()=>{fetch('<?= base_url('organization/announcements/unread-count') ?>',{headers:{'X-Requested-With':'XMLHttpRequest',[csrfHeaderName]:csrfHash}}).then(r=>r.json()).then(d=>{csrfHash=d.csrf;});},30000);
</script>
<?= $this->endSection() ?>

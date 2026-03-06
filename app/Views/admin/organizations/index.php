<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-green-500/10 border border-green-500/20 text-green-700 p-4 rounded-md mb-6 flex items-center">
        <i data-lucide="check-circle" class="mr-2 h-5 w-5 text-green-500"></i>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-destructive/10 border border-destructive/20 text-destructive p-4 rounded-md mb-6 flex items-center">
        <i data-lucide="alert-circle" class="mr-2 h-5 w-5 text-destructive"></i>
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Mount point for React OrganizationsList component -->
<div id="react-organizations-list" data-props="<?= htmlspecialchars(json_encode([
    'initialOrganizations' => $organizations,
    'campuses' => $campuses,
    'selectedCampus' => $selected_campus,
    'printUrl' => base_url('admin/organizations/print' . (!empty($selected_campus) ? '?campus=' . $selected_campus : '')),
    'createUrl' => base_url('admin/organizations/create'),
    'viewUrlBase' => base_url('admin/organizations/view'),
    'editUrlBase' => base_url('admin/organizations/edit'),
    'deleteUrlBase' => base_url('admin/organizations/delete')
]), ENT_QUOTES, 'UTF-8') ?>"></div>

<?= $this->endSection() ?>
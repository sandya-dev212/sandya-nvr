<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2>Dashboard</h2>
<?php if (empty($cameras)): ?>
  <div class="card muted">Belum ada kamera (atau belum di-assign).</div>
<?php else: ?>
  <div class="grid">
    <?php foreach($cameras as $c): ?>
      <div class="card">
        <div><strong><?= esc($c['name']) ?></strong></div>
        <div class="muted">Mode: <?= esc($c['mode']) ?> | Status: <?= $c['status_online']?'Online':'Offline' ?></div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>

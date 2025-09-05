<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Cameras</h2>

<?php if(!empty($msg)): ?>
  <div class="card" style="border-color:#10b981;color:#065f46"><?= $msg ?></div>
<?php endif; ?>
<?php if(!empty($err)): ?>
  <div class="card" style="border-color:#ef4444;color:#991b1b"><?= esc($err) ?></div>
<?php endif; ?>

<div class="card"><a href="/cameras/create">+ Add Camera</a></div>

<?php if (empty($cameras)): ?>
  <div class="card muted">Belum ada kamera.</div>
<?php else: ?>
  <div class="card" style="overflow:auto">
    <table border="0" cellpadding="8" cellspacing="0" style="width:100%">
      <thead>
        <tr style="text-align:left;border-bottom:1px solid #e5e7eb">
          <th>Name</th><th>IP</th><th>Path</th><th>Mode</th>
          <th>FPS</th><th>Audio</th><th>Transport</th><th>Last Seen</th>
          <th style="width:180px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($cameras as $c): ?>
          <tr style="border-bottom:1px solid #f1f5f9">
            <td><strong><?= esc($c['name']) ?></strong></td>
            <td><?= esc($c['ip']) ?><?= $c['port']?':'.(int)$c['port']:'' ?></td>
            <td><code><?= esc($c['path']) ?></code></td>
            <td><?= esc($c['mode']) ?></td>
            <td><?= esc($c['fps'] ?? '-') ?></td>
            <td><?= $c['audio']?'On':'Off' ?></td>
            <td><?= esc(strtoupper($c['transport'])) ?></td>
            <td><?= esc($c['last_seen'] ?? '-') ?></td>
            <td>
              <a href="/cameras/edit/<?= (int)$c['id'] ?>">Edit</a> |
              <form method="post" action="/cameras/delete/<?= (int)$c['id'] ?>" style="display:inline" onsubmit="return confirm('Delete camera?')">
                <?= csrf_field() ?>
                <button type="submit" style="background:#ef4444;color:#fff;border:0;border-radius:8px;padding:4px 8px">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card"><?= $pager->links() ?></div>
<?php endif; ?>

<?= $this->endSection() ?>

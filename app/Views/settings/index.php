<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2>Settings (read-only dulu)</h2>
<div class="card">
  <table>
    <?php foreach($items as $it): ?>
      <tr><td style="padding:6px 12px"><code><?= esc($it['key']) ?></code></td><td><?= esc($it['value']) ?></td></tr>
    <?php endforeach; ?>
  </table>
</div>
<?= $this->endSection() ?>

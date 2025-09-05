<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2><?= $mode==='create'?'Add Camera':'Edit Camera' ?></h2>

<?php if(!empty($errors)): ?>
  <div class="card" style="border-color:#ef4444;color:#b91c1c">
    <ul>
      <?php foreach($errors as $k=>$v): ?>
        <li><strong><?= esc($k) ?></strong>: <?= esc($v) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="<?= $mode==='create'?'/cameras/create':'/cameras/edit/'.(int)($data['id']??0) ?>">
  <?= csrf_field() ?>
  <div class="card">

    <label>Nama Kamera<br>
      <input name="name" required value="<?= esc($data['name'] ?? '') ?>" style="width:360px">
    </label><br><br>

    <label>Camera IP<br>
      <input name="ip" required placeholder="192.168.1.10" value="<?= esc($data['ip'] ?? '') ?>" style="width:220px">
    </label><br><br>

    <label>Path (contoh: /, /index, /cam/realmonitor?channel=1&subtype=0)<br>
      <input name="path" placeholder="/" value="<?= esc($data['path'] ?? '/') ?>" style="width:600px">
    </label><br><br>

    <label>Port (opsional, default 554)<br>
      <input name="port" type="number" min="1" max="65535" value="<?= esc($data['port'] ?? '') ?>" style="width:160px">
    </label><br><br>

    <label>Transport<br>
      <?php $tr = $data['transport'] ?? 'tcp'; ?>
      <select name="transport">
        <option value="tcp" <?= $tr==='tcp'?'selected':'' ?>>TCP</option>
        <option value="udp" <?= $tr==='udp'?'selected':'' ?>>UDP</option>
      </select>
    </label><br><br>

    <label>Mode<br>
      <?php $md = $data['mode'] ?? 'disabled'; ?>
      <select name="mode">
        <option value="recording" <?= $md==='recording'?'selected':'' ?>>Recording</option>
        <option value="watch"     <?= $md==='watch'?'selected':'' ?>>Watch only</option>
        <option value="disabled"  <?= $md==='disabled'?'selected':'' ?>>Disabled</option>
      </select>
    </label><br><br>

    <label>FPS (1–60)<br>
      <input name="fps" type="number" min="1" max="60" value="<?= esc($data['fps'] ?? '') ?>" style="width:120px">
    </label><br><br>

    <label>Audio<br>
      <?php $aud = (string)($data['audio'] ?? '1'); ?>
      <select name="audio">
        <option value="1" <?= $aud==='1'?'selected':'' ?>>On</option>
        <option value="0" <?= $aud==='0'?'selected':'' ?>>Off</option>
      </select>
    </label><br><br>

    <label>Max Days (0=off)<br>
      <input name="max_days" type="number" min="0" value="<?= esc($data['max_days'] ?? '') ?>" style="width:160px">
    </label><br><br>

    <label>Max Storage (MB, 0=off)<br>
      <input name="max_storage_mb" type="number" min="0" value="<?= esc($data['max_storage_mb'] ?? '') ?>" style="width:200px">
    </label><br><br>

    <label>Notes<br>
      <textarea name="notes" rows="3" style="width:600px"><?= esc($data['notes'] ?? '') ?></textarea>
    </label><br><br>

    <button type="submit"><?= $mode==='create'?'Create':'Update' ?></button>
    <a href="/cameras" style="margin-left:12px">Cancel</a>
  </div>
</form>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2>Login</h2>
<?php if(session()->getFlashdata('error')): ?>
  <div class="card" style="border-color:#ef4444;color:#b91c1c"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<form method="post">
  <div class="card">
    <label>Username<br><input name="username" required></label><br><br>
    <label>Password<br><input name="password" type="password" required></label><br><br>
    <button type="submit">Masuk</button>
  </div>
  <p class="muted">LDAP login akan ditambah nanti. Untuk sekarang local login dulu.</p>
</form>
<?= $this->endSection() ?>

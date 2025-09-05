<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Login</h2>

<?php if(!empty($error)): ?>
  <div class="card" style="border-color:#ef4444;color:#b91c1c">
    <?= esc($error) ?>
  </div>
<?php endif; ?>

<form method="post" action="/login">
  <?= csrf_field() ?>
  <div class="card">
    <label>Username<br>
      <input name="username" required autocomplete="username" value="<?= esc(old('username')) ?>">
    </label><br><br>

    <label>Password<br>
      <input name="password" type="password" required autocomplete="current-password">
    </label><br><br>

    <button type="submit">Masuk</button>
  </div>
  <p class="muted">LDAP login akan ditambah nanti. Untuk sekarang local login dulu.</p>
</form>

<?= $this->endSection() ?>

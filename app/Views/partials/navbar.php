<?php $isAdmin = (int)(session()->get('is_admin') ?? 0); ?>
<nav>
  <a href="/dashboard">Dashboard</a>
  <?php if($isAdmin): ?>
    <a href="/settings">Settings</a>
  <?php endif; ?>
  <?php if(session()->get('uid')): ?>
    <a href="/logout">Logout (<?= esc(session()->get('uname')) ?>)</a>
  <?php else: ?>
    <a href="/login">Login</a>
  <?php endif; ?>
</nav>

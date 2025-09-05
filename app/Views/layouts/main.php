<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sandya NVR</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial,sans-serif;margin:0}
    header,footer{background:#0f172a;color:#fff;padding:10px 16px}
    nav a{color:#fff;margin-right:12px;text-decoration:none}
    .wrap{padding:16px}
    .card{border:1px solid #e5e7eb;border-radius:12px;padding:12px;margin-bottom:12px}
    .muted{color:#64748b}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px}
  </style>
</head>
<body>
<header>
  <?php echo view('partials/navbar'); ?>
</header>
<div class="wrap">
  <?= $this->renderSection('content') ?>
</div>
<footer>
  <?php echo view('partials/footer', ['stats'=>$stats ?? sys_stats()]); ?>
</footer>
</body>
</html>

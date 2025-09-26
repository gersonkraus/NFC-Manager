<?php start_session(); ?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NFC Manager</title>
<link rel="stylesheet" href="<?=e(base_url())?>/admin.css">
</head><body>
<header>
  <div class="wrap">NFC Manager</div>
</header>
<div class="wrap">
  <nav style="margin:8px 0 16px">
    <a href="<?=e(base_url())?>/admin" class="btn">Dashboard</a>
    <a href="<?=e(base_url())?>/admin/tags" class="btn">TAGs</a>
    <a href="<?=e(base_url())?>/admin/scans" class="btn">Leituras</a>
    <a href="<?=e(base_url())?>/admin/logout" class="btn" style="background:#a33">Sair</a>
  </nav>
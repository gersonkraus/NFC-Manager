<?php require_once __DIR__.'/../lib/helpers.php'; require_once __DIR__.'/../lib/db.php'; start_session(); ?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<link rel="stylesheet" href="<?=e(base_url())?>/admin.css">
</head><body>
<div class="wrap">
  <div class="card" style="max-width:420px;margin:60px auto">
    <h2>Entrar</h2>
    <?php if (($_GET['e'] ?? '')==='1'): ?>
      <p style="color:#a33">Usuário ou senha inválidos.</p>
    <?php endif; ?>
    <form method="post" action="<?=e(base_url())?>/admin/login/submit">
      <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
      <label>Usuário</label>
      <input name="user" value="admin" required>
      <label>Senha</label>
      <input type="password" name="pass" required>
      <p class="muted">Padrão: admin / admin123 (troque depois!)</p>
      <input type="submit" value="Entrar">
    </form>
  </div>
</div>
</body></html>
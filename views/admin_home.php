<?php require_once __DIR__.'/_admin_header.php'; ?>
<?php require_login(); $pdo = db(); ?>
<div class="row">
  <div class="col">
    <div class="card">
      <h3>Como usar</h3>
      <ol>
        <li>Crie uma TAG em <b>TAGs → Novo</b>.</li>
        <li>Grave na TAG NFC a URL: <code><?=e(base_url())?>/t/SEU-SLUG</code>.</li>
        <li>Ao encostar no celular, a leitura será registrada e o usuário será redirecionado.</li>
      </ol>
      <p class="muted">Para geolocalização precisa de permissão do navegador do usuário no momento da leitura.</p>
    </div>
  </div>
  <div class="col">
    <div class="card">
      <h3>Resumo</h3>
      <?php
        $tags = $pdo->query("SELECT COUNT(*) c FROM tags")->fetch()['c'] ?? 0;
        $scans = $pdo->query("SELECT COUNT(*) c FROM scans")->fetch()['c'] ?? 0;
      ?>
      <p><b>Total TAGs:</b> <?= (int)$tags ?> | <b>Leituras:</b> <?= (int)$scans ?></p>
    </div>
  </div>
</div>
<?php require __DIR__.'/_admin_footer.php'; ?>
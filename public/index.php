<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/../lib/helpers.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($base !== '' && str_starts_with($path, $base)) {
  $path = substr($path, strlen($base));
}
$path = '/'.ltrim($path, '/');

// Static asset for admin (very small CSS)
if ($path === '/admin.css') {
  header('Content-Type: text/css');
  echo "body{font-family:system-ui,Arial,sans-serif;margin:0;background:#f6f7f9}
  header{background:#136758;color:#fff;padding:12px 16px;font-weight:600}
  .wrap{max-width:1000px;margin:0 auto;padding:16px}
  a.btn,button, input[type=submit]{background:#136758;color:#fff;border:none;border-radius:10px;padding:8px 12px;cursor:pointer}
  table{width:100%;border-collapse:collapse;background:#fff}
  th,td{padding:10px;border-bottom:1px solid #eee;font-size:14px}
  .card{background:#fff;border-radius:14px;padding:16px;box-shadow:0 6px 18px rgba(0,0,0,.06);margin:16px 0}
  .row{display:flex;gap:12px;flex-wrap:wrap}
  .row .col{flex:1 1 300px}
  label{display:block;margin:8px 0 4px}
  input,select,textarea{width:100%;padding:8px;border:1px solid #ddd;border-radius:10px}
  .muted{color:#666;font-size:12px}
  nav a{margin-right:10px}
  "; exit;
}

# Route definitions
if ($path === '/' || $path === '/index.php') {
  header("Location: ".base_url()."/admin");
  exit;
}

# Reader route /t/{slug}
if (preg_match('#^/t/([A-Za-z0-9_-]{4,20})$#', $path, $m)) {
  $slug = $m[1];
  $pdo = db();
  $stmt = $pdo->prepare("SELECT * FROM tags WHERE slug=? AND status='active'");
  $stmt->execute([$slug]);
  $tag = $stmt->fetch();
  if (!$tag) { http_response_code(404); echo "TAG não encontrada ou inativa"; exit; }

  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';
  $pdo->prepare("INSERT INTO scans (tag_id, scanned_at, ip, user_agent) VALUES (?, NOW(), ?, ?)")
      ->execute([$tag['id'], $ip, $ua]);
  $scan_id = $pdo->lastInsertId();

  $redirect = $tag['target_type'] === 'url' ? $tag['target_value'] : (base_url()."/content/".$slug);

  ?>
  <!doctype html><html lang="pt-br"><head><meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="refresh" content="2;url=<?=e($redirect)?>">
  <title>Lendo TAG...</title></head><body>
  <p style="font-family:system-ui; padding:16px">Abrindo conteúdo...</p>
  <script>
  (function(){
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(function(pos){
      fetch("<?=e(base_url())?>/api/scan-geo", {
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({
          scan_id: <?=json_encode((int)$scan_id)?>,
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
          accuracy: Math.round(pos.coords.accuracy)
        })
      }).catch(function(){});
    }, function(){}, {maximumAge:0, timeout:1500, enableHighAccuracy:false});
  })();
  </script>
  </body></html>
  <?php
  exit;
}

// Conteúdo (inclui renderer para profile)
if (preg_match('#^/content/([A-Za-z0-9_-]{4,20})$#', $path, $m)) {
  $slug = $m[1];
  $pdo = db();
  $stmt = $pdo->prepare("SELECT * FROM tags WHERE slug=?");
  $stmt->execute([$slug]);
  $tag = $stmt->fetch();
  if (!$tag) { http_response_code(404); echo "TAG não encontrada"; exit; }

  $type = $tag['target_type'] ?? 'url';
  $val  = $tag['target_value'] ?? '';

  // Se for 'url', só redireciona (defensivo)
  if ($type === 'url') {
    header('Location: '.$val);
    exit;
  }

  // Se for 'profile', renderiza cartão de visita
  if ($type === 'profile') {
    $p = json_decode($val, true) ?: [];
    $name    = $p['name']    ?? 'Cliente';
    $role    = $p['role']    ?? '';
    $company = $p['company'] ?? '';
    $photo   = $p['photo']   ?? '';
    $phone   = $p['phone']   ?? '';
    $wa      = $p['whatsapp']?? '';
    $email   = $p['email']   ?? '';
    $site    = $p['website'] ?? '';
    $addr    = $p['address'] ?? '';
    $bio     = $p['bio']     ?? '';
    $color   = $p['accent_color'] ?? '#136758';

    // normaliza whatsapp (só dígitos) para link
    $wa_digits = preg_replace('/\D+/', '', $wa);
    $wa_link = $wa_digits ? ("https://wa.me/".$wa_digits) : '';

    $vcard_url = base_url()."/vcard/".$slug;
    ?><!doctype html><html lang="pt-br"><head><meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($name) ?> · Cartão de Visita</title>
    <style>
      :root{ --c: <?= e($color) ?>; }
      body{font-family:system-ui,Arial,sans-serif;margin:0;background:#f6f7f9}
      .wrap{max-width:720px;margin:0 auto;padding:20px}
      .card{background:#fff;border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.08);overflow:hidden}
      .hero{background:var(--c);height:120px}
      .content{padding:16px 20px 24px}
      .profile{display:flex;gap:16px;margin-top:-60px;align-items:center}
      .profile img{width:110px;height:110px;border-radius:16px;object-fit:cover;border:4px solid #fff;box-shadow:0 6px 16px rgba(0,0,0,.12)}
      .name{font-size:22px;font-weight:700}
      .sub{color:#555;margin-top:2px}
      .grid{display:grid;grid-template-columns:1fr;gap:10px;margin-top:16px}
      @media(min-width:680px){ .grid{grid-template-columns:1fr 1fr} }
      .row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
      a.btn{display:inline-block;text-decoration:none;background:var(--c);color:#fff;padding:10px 14px;border-radius:12px}
      .btn.ghost{background:#eef6f1;color:#0c3b2c}
      .field{background:#fafafa;border:1px solid #eee;border-radius:12px;padding:10px 12px}
      .label{font-size:12px;color:#666;margin-bottom:4px}
      .val a{color:#0a3;word-break:break-word}
      .muted{color:#666}
    </style>
    </head><body><div class="wrap">
      <div class="card">
        <div class="hero"></div>
        <div class="content">
          <div class="profile">
            <?php if ($photo): ?><img src="<?= e($photo) ?>" alt="Foto"><?php endif; ?>
            <div>
              <div class="name"><?= e($name) ?></div>
              <div class="sub">
                <?= e($role) ?><?= $role && $company ? " · " : "" ?><?= e($company) ?>
              </div>
              <div class="row" style="margin-top:10px">
                <?php if ($phone): ?><a class="btn" href="tel:<?= e($phone) ?>">Ligar</a><?php endif; ?>
                <?php if ($wa_link): ?><a class="btn" href="<?= e($wa_link) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
                <?php if ($email): ?><a class="btn" href="mailto:<?= e($email) ?>">E-mail</a><?php endif; ?>
                <a class="btn ghost" href="<?= e($vcard_url) ?>">Salvar Contato (vCard)</a>
              </div>
            </div>
          </div>

          <?php if ($bio): ?>
            <div class="field" style="margin-top:16px">
              <div class="label">Sobre</div>
              <div class="val"><?= nl2br(e($bio)) ?></div>
            </div>
          <?php endif; ?>

          <div class="grid">
            <?php if ($site): ?>
              <div class="field">
                <div class="label">Site</div>
                <div class="val"><a href="<?= e($site) ?>" target="_blank" rel="noopener"><?= e($site) ?></a></div>
              </div>
            <?php endif; ?>
            <?php if ($addr): ?>
              <div class="field">
                <div class="label">Endereço</div>
                <div class="val">
                  <?= nl2br(e($addr)) ?><br>
                  <a href="<?= 'https://www.google.com/maps/search/?api=1&query='.urlencode($addr) ?>" target="_blank" rel="noopener">Ver no Mapa</a>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="muted" style="margin-top:14px">
            TAG: <code><?= e($slug) ?></code>
          </div>
        </div>
      </div>
    </div></body></html>
    <?php
    exit;
  }

  // Outros tipos simples: 'message' (mostra texto cru), 'file' (link)
  if ($type === 'message') {
    $content = $val ?: 'Sem mensagem.';
    ?><!doctype html><html lang="pt-br"><head><meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"><title>Mensagem</title></head><body>
    <div style="font-family:system-ui;max-width:700px;margin:24px auto;padding:16px">
      <h1>Mensagem</h1>
      <pre style="white-space:pre-wrap"><?= e($content) ?></pre>
    </div>
    </body></html><?php
    exit;
  }

  if ($type === 'file') {
    // espera-se uma URL no valor
    header("Location: ".$val);
    exit;
  }

  // fallback genérico
  $content = $val ?: 'Sem conteúdo.';
  ?><!doctype html><html lang="pt-br"><head><meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"><title>Conteúdo</title></head><body>
  <div style="font-family:system-ui;max-width:700px;margin:24px auto;padding:16px">
    <h1>Conteúdo</h1>
    <pre style="white-space:pre-wrap"><?= e($content) ?></pre>
  </div>
  </body></html><?php
  exit;
}


# API: scan-geo
if ($path === '/api/scan-geo') {
  $data = json_decode(file_get_contents('php://input'), true);
  if (!isset($data['scan_id'])) { http_response_code(400); echo "missing"; exit; }
  $pdo = db();
  $stmt = $pdo->prepare("UPDATE scans SET lat=?, lng=?, accuracy_m=? WHERE id=?");
  $stmt->execute([$data['lat'] ?? None, $data['lng'] ?? None, $data['accuracy'] ?? None, $data['scan_id']]);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>true]);
  exit;
}

# Admin group
if ($path === '/admin/login') {
  require __DIR__.'/../views/admin_login.php'; exit;
}
if ($path === '/admin/logout') {
  start_session(); session_destroy();
  header("Location: ".base_url()."/admin/login"); exit;
}
if ($path === '/admin/login/submit' && $_SERVER['REQUEST_METHOD']==='POST') {
  check_csrf();
  $user = $_POST['user'] ?? '';
  $pass = $_POST['pass'] ?? '';
  $pdo = db();
  $stmt = $pdo->query("SELECT * FROM admins WHERE username='admin' LIMIT 1");
  $admin = $stmt->fetch();
  $ok = $admin && password_verify($pass, $admin['password_hash']) and $user==='admin';
  if ($ok) {
    start_session(); $_SESSION['admin']=true;
    header("Location: ".base_url()."/admin"); exit;
  } else {
    header("Location: ".base_url()."/admin/login?e=1"); exit;
  }
}

if ($path === '/admin') { require_login(); require __DIR__.'/../views/admin_home.php'; exit; }

# CRUD TAGs
if ($path === '/admin/tags') { require_login(); require __DIR__.'/../views/admin_tags_list.php'; exit; }
if ($path === '/admin/tags/new') { require_login(); require __DIR__.'/../views/admin_tags_new.php'; exit; }
if ($path === '/admin/tags/create' && $_SERVER['REQUEST_METHOD']==='POST') {
  require_login(); check_csrf();
  $pdo = db();
  $slug = $_POST['slug'] ?: gen_slug();
  $stmt = $pdo->prepare("INSERT INTO tags (slug, uid_hex, label, status, target_type, target_value, created_at, updated_at) VALUES (?,?,?,?,?,?,NOW(),NOW())");
  $stmt->execute([
    strtoupper($slug),
    $_POST['uid_hex'] ?: null,
    $_POST['label'] ?: null,
    $_POST['status'] ?? 'active',
    $_POST['target_type'] ?? 'url',
    $_POST['target_value'] ?? null,
  ]);
  header("Location: ".base_url()."/admin/tags"); exit;
}
if (preg_match('#^/admin/tags/edit/(\d+)$#', $path, $m)) { require_login(); $_GET['id']=$m[1]; require __DIR__.'/../views/admin_tags_edit.php'; exit; }
if ($path === '/admin/tags/update' && $_SERVER['REQUEST_METHOD']==='POST') {
  require_login(); check_csrf();
  $pdo = db();
  $stmt = $pdo->prepare("UPDATE tags SET slug=?, uid_hex=?, label=?, status=?, target_type=?, target_value=?, updated_at=NOW() WHERE id=?");
  $stmt->execute([
    strtoupper($_POST['slug'] ?? ''),
    $_POST['uid_hex'] ?: null,
    $_POST['label'] ?: null,
    $_POST['status'] ?? 'active',
    $_POST['target_type'] ?? 'url',
    $_POST['target_value'] ?? null,
    (int)$_POST['id']
  ]);
  header("Location: ".base_url()."/admin/tags"); exit;
}
if (preg_match('#^/admin/tags/delete/(\d+)$#', $path, $m)) {
  require_login();
  $pdo = db();
  $pdo->prepare("DELETE FROM tags WHERE id=?")->execute([(int)$m[1]]);
  header("Location: ".base_url()."/admin/tags"); exit;
}
// vCard: /vcard/{slug}
if (preg_match('#^/vcard/([A-Za-z0-9_-]{4,20})$#', $path, $m)) {
  $slug = $m[1];
  $pdo = db();
  $stmt = $pdo->prepare("SELECT * FROM tags WHERE slug=?");
  $stmt->execute([$slug]);
  $tag = $stmt->fetch();
  if (!$tag || $tag['target_type']!=='profile') { http_response_code(404); echo "Perfil não encontrado"; exit; }

  $p = json_decode($tag['target_value'] ?? '', true) ?: [];
  $name = $p['name'] ?? 'Contato';
  $role = $p['role'] ?? '';
  $company = $p['company'] ?? '';
  $phone = $p['phone'] ?? '';
  $email = $p['email'] ?? '';
  $site  = $p['website'] ?? '';
  $addr  = $p['address'] ?? '';

  $fn  = $name;
  $org = $company;
  $title = $role;

  $lines = [
    "BEGIN:VCARD",
    "VERSION:3.0",
    "FN:".$fn,
  ];
  if ($org)   $lines[] = "ORG:".$org;
  if ($title) $lines[] = "TITLE:".$title;
  if ($phone) $lines[] = "TEL;TYPE=CELL:".$phone;
  if ($email) $lines[] = "EMAIL;TYPE=INTERNET:".$email;
  if ($site)  $lines[] = "URL:".$site;
  if ($addr)  $lines[] = "ADR;TYPE=WORK:;;".$addr.";;;;";
  $lines[] = "END:VCARD";

  $vcf = implode("\r\n", $lines)."\r\n";

  header('Content-Type: text/vcard; charset=utf-8');
  header('Content-Disposition: attachment; filename="'.preg_replace('/\W+/', '-', strtolower($name)).'.vcf"');
  echo $vcf;
  exit;
}


# Scans list + map
if ($path === '/admin/scans') { require_login(); require __DIR__.'/../views/admin_scans.php'; exit; }

http_response_code(404);
echo "404";
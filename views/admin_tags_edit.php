<?php require __DIR__.'/_admin_header.php'; require_login(); $pdo=db();
  $id = (int)($_GET['id'] ?? 0);
  $stmt = $pdo->prepare("SELECT * FROM tags WHERE id=?"); $stmt->execute([$id]); $r = $stmt->fetch();
  if (!$r) { echo "<p>TAG não encontrada.</p>"; require __DIR__.'/_admin_footer.php'; exit; }
?>
<div class="card">
  <h3>Editar TAG #<?= (int)$r['id'] ?></h3>
  <form method="post" action="<?=e(base_url())?>/admin/tags/update">
    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
    <div class="row">
      <div class="col">
        <label>Slug</label>
        <input name="slug" value="<?= e($r['slug']) ?>">
      </div>
      <div class="col">
        <label>UID físico (opcional)</label>
        <input name="uid_hex" value="<?= e($r['uid_hex']) ?>">
      </div>
    </div>
    <label>Label</label>
    <input name="label" value="<?= e($r['label']) ?>">
    <label>Status</label>
    <select name="status">
      <?php foreach (['active','lost','inactive'] as $s): ?>
        <option value="<?=$s?>" <?= $s===$r['status']?'selected':'' ?>><?=$s?></option>
      <?php endforeach; ?>
    </select>
    <label>Tipo de destino</label>
    <select name="target_type">
      <?php foreach (['url','profile','message','file'] as $s): ?>
        <option value="<?=$s?>" <?= $s===$r['target_type']?'selected':'' ?>><?=$s?></option>
      <?php endforeach; ?>
    </select>
    <label>Valor do destino</label>
    <textarea name="target_value" rows="4"><?= e($r['target_value']) ?></textarea>
    <input type="submit" value="Salvar">
  </form>
</div>
<?php require __DIR__.'/_admin_footer.php'; ?>
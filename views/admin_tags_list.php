<?php require __DIR__.'/_admin_header.php'; require_login(); $pdo=db(); ?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <h3>TAGs</h3>
    <a href="<?=e(base_url())?>/admin/tags/new" class="btn">Novo</a>
  </div>
  <table>
    <tr><th>ID</th><th>Slug</th><th>Label</th><th>Status</th><th>Destino</th><th>Ações</th></tr>
    <?php foreach ($pdo->query("SELECT * FROM tags ORDER BY id DESC") as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><code><?= e($r['slug']) ?></code></td>
        <td><?= e($r['label']) ?></td>
        <td><?= e($r['status']) ?></td>
        <td><?= e($r['target_type']) ?></td>
        <td>
          <a class="btn" href="<?=e(base_url())?>/admin/tags/edit/<?= (int)$r['id'] ?>">Editar</a>
          <a class="btn" style="background:#a33" href="<?=e(base_url())?>/admin/tags/delete/<?= (int)$r['id'] ?>" onclick="return confirm('Excluir?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__.'/_admin_footer.php'; ?>
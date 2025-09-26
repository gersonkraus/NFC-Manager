<?php require __DIR__.'/_admin_header.php'; require_login(); ?>
<div class="card">
  <h3>Nova TAG</h3>
  <form method="post" action="<?=e(base_url())?>/admin/tags/create">
    <input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
    <div class="row">
      <div class="col">
        <label>Slug (deixe em branco para gerar)</label>
        <input name="slug" placeholder="ex: AB12CD">
      </div>
      <div class="col">
        <label>UID físico (opcional)</label>
        <input name="uid_hex" placeholder="HEX da TAG (se quiser registrar)">
      </div>
    </div>
    <label>Label</label>
    <input name="label" placeholder="ex: Chaveiro do Kaique">
    <label>Status</label>
    <select name="status">
      <option value="active">active</option>
      <option value="lost">lost</option>
      <option value="inactive">inactive</option>
    </select>
    <label>Tipo de destino</label>
    <select name="target_type" id="tt">
      <option value="url">url</option>
      <option value="profile">profile</option>
      <option value="message">message</option>
      <option value="file">file</option>
    </select>
    <label>Valor do destino</label>
    <textarea name="target_value" rows="4" placeholder="Se 'url', coloque o link completo; se outro, um texto/JSON"></textarea>
    <input type="submit" value="Salvar">
  </form>
</div>
<?php require __DIR__.'/_admin_footer.php'; ?>
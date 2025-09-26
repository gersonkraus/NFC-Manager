<?php require __DIR__.'/_admin_header.php'; require_login(); $pdo=db(); ?>
<div class="card">
  <h3>Leituras</h3>
  <div id="map" style="height:360px;border-radius:12px;margin:6px 0"></div>
  <p class="muted">O mapa mostra leituras com localização concedida pelo usuário.</p>
  <table>
    <tr><th>Data/Hora</th><th>TAG</th><th>IP</th><th>Lat</th><th>Lng</th><th>Acc(m)</th><th>UA</th></tr>
    <?php
      $rows = $pdo->query("SELECT s.*, t.slug FROM scans s LEFT JOIN tags t ON t.id=s.tag_id ORDER BY s.id DESC LIMIT 200")->fetchAll();
      foreach ($rows as $r):
    ?>
      <tr>
        <td><?= e($r['scanned_at']) ?></td>
        <td><code><?= e($r['slug']) ?></code></td>
        <td><?= e($r['ip']) ?></td>
        <td><?= e($r['lat']) ?></td>
        <td><?= e($r['lng']) ?></td>
        <td><?= e($r['accuracy_m']) ?></td>
        <td><?= e(substr($r['user_agent'] ?? '', 0, 60)) ?>...</td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
  (function(){
    var map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19, attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    var pts = [];
    <?php foreach ($rows as $r): if (!empty($r['lat']) && !empty($r['lng'])): ?>
      pts.push([<?= $r['lat'] ?>, <?= $r['lng'] ?>, "<?= e($r['slug']) ?>"]);
    <?php endif; endforeach; ?>
    if (pts.length) {
      var group = [];
      pts.forEach(function(p){
        var m = L.marker([p[0], p[1]]).bindPopup("TAG: "+p[2]);
        m.addTo(map); group.push(m.getLatLng());
      });
      map.fitBounds(L.latLngBounds(group).pad(0.2));
    } else {
      map.setView([-14.2, -51.9], 4); // Brasil
    }
  })();
</script>

<?php require __DIR__.'/_admin_footer.php'; ?>
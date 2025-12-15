<?php
require_login();
$u=current_user();
if($u['role']==='klientas'){
  $st=$pdo->prepare("SELECT * FROM uzsakymas WHERE kliento_id=? ORDER BY sukurimo_data DESC");
  $st->execute([$u['id']]);
} elseif($u['role']==='technikas'){
  $st=$pdo->prepare("SELECT * FROM uzsakymas WHERE techniko_id=? ORDER BY sukurimo_data DESC");
  $st->execute([$u['id']]);
} else {
  $st=$pdo->query("SELECT * FROM uzsakymas ORDER BY sukurimo_data DESC");
}
$orders=$st->fetchAll();

ob_start(); ?>
<section class="card">
  <h2>Užsakymai</h2>
  <table>
    <tr><th>ID</th><th>Aprašymas</th><th>Būsena</th><th>Klientas</th><th>Technikas</th><th>Veiksmai</th></tr>
    <?php foreach($orders as $o):
      $client=$pdo->query("SELECT el_pastas FROM naudotojas WHERE id=".(int)$o['kliento_id'])->fetchColumn();
      $tech=$o['techniko_id'] ? $pdo->query("SELECT el_pastas FROM naudotojas WHERE id=".(int)$o['techniko_id'])->fetchColumn() : '-';
    ?>
      <tr>
        <td><?= (int)$o['id'] ?></td>
        <td><?= htmlspecialchars($o['aprasymas']) ?></td>
        <td><span class="badge <?= htmlspecialchars($o['busena']) ?>"><?= htmlspecialchars($o['busena']) ?></span></td>
        <td><?= htmlspecialchars($client) ?></td>
        <td><?= htmlspecialchars($tech) ?></td>
        <td>
          <?php if(current_user()['role']==='technikas' && $o['techniko_id']==current_user()['id'] && $o['busena']!=='ivykdytas'): ?>
            <a class="btn small" href="/index.php?route=complete&id=<?= (int)$o['id'] ?>">✓ Įvykdyta</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</section>
<?php view(ob_get_clean()); ?>

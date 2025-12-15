<?php
require_login();
if(!is_role('administratorius')) die('Tik administratoriui');

function users_by_role(PDO $pdo, $role){
  $st = $pdo->prepare("SELECT id, vardas, pavarde, el_pastas FROM naudotojas WHERE role=? ORDER BY vardas");
  $st->execute([$role]);
  return $st->fetchAll();
}

$orders = $pdo->query("SELECT id, aprasymas, busena, techniko_id FROM uzsakymas ORDER BY sukurimo_data DESC")->fetchAll();
$techs = users_by_role($pdo, 'technikas');

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  ob_start(); ?>
  <section class="card small">
    <h2>Priskirti / keisti techniką</h2>
    <form method="post">
      <label>Užsakymas:
        <select name="order_id">
          <?php foreach($orders as $o): ?>
            <option value="<?= (int)$o['id'] ?>">#<?= (int)$o['id'] ?> – <?= htmlspecialchars($o['aprasymas']) ?> (<?= htmlspecialchars($o['busena']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </label><br>
      <label>Technikas:
        <select name="tech_id">
          <option value="">(nepriskirti)</option>
          <?php foreach($techs as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['el_pastas']) ?></option>
          <?php endforeach; ?>
        </select>
      </label><br><br>
      <button type="submit" class="btn">Išsaugoti</button>
    </form>
  </section>
  <?php
  view(ob_get_clean());
} else {
  $order_id = (int)$_POST['order_id'];
  $tech_id = ($_POST['tech_id']===''? null : (int)$_POST['tech_id']);
  $st = $pdo->prepare("UPDATE uzsakymas SET techniko_id=?, busena=IF(? IS NULL, busena, IF(busena='pateiktas','vykdomas',busena)) WHERE id=?");
  $st->execute([$tech_id,$tech_id,$order_id]);
  flash('Priskyrimas atnaujintas.');
  header('Location: /index.php?route=orders');
}

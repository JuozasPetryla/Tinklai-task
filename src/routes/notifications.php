<?php
require_login();
if(!is_role('klientas')) die('Tik klientui');

$st = $pdo->prepare("SELECT * FROM pranesimas WHERE kliento_id=? ORDER BY data DESC");
$st->execute([current_user()['id']]);
$notes = $st->fetchAll();

ob_start(); ?>
<section class="card">
  <h2>Pranešimai</h2>
  <table>
    <tr><th>Data</th><th>Užsakymas</th><th>Turinys</th></tr>
    <?php foreach($notes as $n): ?>
      <tr>
        <td><?= htmlspecialchars($n['data']) ?></td>
        <td>#<?= (int)$n['uzsakymas_id'] ?></td>
        <td><?= htmlspecialchars($n['turinys']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</section>
<?php view(ob_get_clean()); ?>

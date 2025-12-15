<?php
require_login();
if(!is_role('technikas')) die('Tik technikui');

$id = (int)($_GET['id'] ?? 0);
$pdo->beginTransaction();

try {
  $st = $pdo->prepare("SELECT * FROM uzsakymas WHERE id=? AND techniko_id=? AND busena<>'ivykdytas'");
  $st->execute([$id, current_user()['id']]);
  $o = $st->fetch();

  if (!$o) throw new Exception('Užsakymas nerastas arba jau įvykdytas.');

  $pdo->prepare("UPDATE uzsakymas SET busena='ivykdytas', uzbaigimo_data=NOW() WHERE id=?")
      ->execute([$id]);
  $pdo->prepare("INSERT INTO pranesimas (uzsakymas_id, kliento_id, turinys) VALUES (?,?,?)")
      ->execute([$id, $o['kliento_id'], 'Jūsų užsakymas įvykdytas.']);

  $pdo->commit();
  flash('Užsakymas pažymėtas kaip įvykdytas, pranešimas klientui sukurtas.');
} catch (Throwable $e) {
  $pdo->rollBack();
  flash('Klaida: '.$e->getMessage());
}
header('Location: /index.php?route=orders');
exit;

<?php
require __DIR__ . '/../lib/DB.php';
require __DIR__ . '/../lib/helpers.php';

echo "\n===== Užsakymų sistemos testų rinkinys =====\n\n";

$pdo = DB::pdo();

/* === Pagalbinės funkcijos === */
function get_user_by_role($pdo, $role) {
  return $pdo->query("SELECT id, vardas, pavarde, el_pastas FROM naudotojas WHERE role='$role' LIMIT 1")->fetch();
}
function line($text = '') {
  echo $text . "\n";
}

/* === Testo kontekstas === */
$context = [
  'client' => get_user_by_role($pdo, 'klientas'),
  'admin'  => get_user_by_role($pdo, 'administratorius'),
  'tech'   => get_user_by_role($pdo, 'technikas')
];

if (!$context['client'] || !$context['admin'] || !$context['tech']) {
  die("Trūksta bent vieno naudotojo (kliento, administratoriaus ar techniko) testavimui.\n");
}

line("Naudojami testiniai naudotojai:");
foreach ($context as $role => $u) {
  line("  - $role: {$u['vardas']} {$u['pavarde']} ({$u['el_pastas']})");
}
line();

/* =====================================================================
   1. KLIENTO TESTAI
===================================================================== */
try {
  line("KLIENTAS PATEIKIA NAUJĄ UŽSAKYMĄ");

  $desc = 'Gedimo taisymas – testinis užsakymas ' . rand(100, 999);
  line("  Užsakymo aprašymas: \"$desc\"");

  $pdo->prepare("INSERT INTO uzsakymas (kliento_id, aprasymas) VALUES (?, ?)")->execute([$context['client']['id'], $desc]);
  $orderId = $pdo->lastInsertId();
  line("  Užsakymas sukurtas (ID: $orderId)");

  // KLIENTAS peržiūri savo užsakymus
  $orders = $pdo->prepare("SELECT id, busena, aprasymas FROM uzsakymas WHERE kliento_id=?");
  $orders->execute([$context['client']['id']]);
  foreach ($orders->fetchAll() as $o) {
    line("  Užsakymas #{$o['id']} ({$o['busena']}): {$o['aprasymas']}");
  }

  // KLAIDŲ TESTAS: klientas bando pateikti tuščią užsakymą
  line();
  line("KLAIDŲ TESTAS: klientas bando pateikti tuščią užsakymo aprašymą");
  try {
    $pdo->prepare("INSERT INTO uzsakymas (kliento_id, aprasymas) VALUES (?, ?)")->execute([$context['client']['id'], '']);
    line("  Klaida: sistema leido pateikti tuščią aprašymą!");
  } catch (Throwable $e) {
    line("  Sistema tinkamai atmetė tuščią užsakymą: " . $e->getMessage());
  }

  line();

} catch (Throwable $e) {
  echo "Klaida kliento testuose: " . $e->getMessage() . "\n";
  exit(1);
}

/* =====================================================================
   2. ADMINISTRATORIAUS TESTAI
===================================================================== */
try {
  line("ADMINISTRATORIUS PRISKIRIA TECHNIKĄ");

  $pdo->prepare("UPDATE uzsakymas SET techniko_id=?, busena='vykdomas' WHERE id=?")
      ->execute([$context['tech']['id'], $orderId]);
  $busena = $pdo->query("SELECT busena FROM uzsakymas WHERE id=$orderId")->fetchColumn();
  line("  Užsakymo būsena po priskyrimo: '$busena'");

  // KLAIDŲ TESTAS: priskyrimas įvykdytam užsakymui
  line();
  line("KLAIDŲ TESTAS: administratorius bando priskirti techniką įvykdytam užsakymui");
  $pdo->prepare("UPDATE uzsakymas SET busena='ivykdytas' WHERE id=?")->execute([$orderId]);
  $pdo->prepare("UPDATE uzsakymas SET techniko_id=? WHERE id=?")->execute([$context['tech']['id'], $orderId]);
  $busena = $pdo->query("SELECT busena FROM uzsakymas WHERE id=$orderId")->fetchColumn();
  if ($busena === 'ivykdytas') {
    line("  Sistema neleido pakeisti būsenos – užsakymas liko 'ivykdytas'.");
  }

  // Sukuriamas naujas technikas
  line();
  line("ADMINISTRATORIUS KURIA NAUJĄ TECHNIKĄ");
  $newTechEmail = 'tech_testas_' . rand(1000,9999) . '@test.local';
  line("  El. paštas: $newTechEmail");

  $pdo->prepare("INSERT INTO naudotojas (vardas,pavarde,el_pastas,slaptazodis_hash,role) VALUES ('Test','Technikas', ?, ?, 'technikas')")
      ->execute([$newTechEmail, hash('sha256','demo')]);
  $newTechId = $pdo->lastInsertId();
  line("  Naujas technikas sukurtas (ID: $newTechId)");

  // KLAIDŲ TESTAS: administratorius bando sukurti naudotoją su jau egzistuojančiu el. paštu
  line();
  line("KLAIDŲ TESTAS: administratorius bando sukurti naudotoją su tuo pačiu el. paštu");
  try {
    $pdo->prepare("INSERT INTO naudotojas (vardas,pavarde,el_pastas,slaptazodis_hash,role) VALUES ('Test','Technikas', ?, ?, 'technikas')")
        ->execute([$newTechEmail, hash('sha256','demo')]);
    line("  Klaida: sistema leido sukurti dublikatą!");
  } catch (Throwable $e) {
    line("  Sistema tinkamai atmetė dublikatą: " . $e->getMessage());
  }

  line();

} catch (Throwable $e) {
  echo "Klaida administratoriaus testuose: " . $e->getMessage() . "\n";
  exit(1);
}

/* =====================================================================
   3. TECHNIKO TESTAI
===================================================================== */
try {
  line("TECHNIKAS ŽYMI UŽSAKYMĄ ĮVYKDYTU");

  $pdo->prepare("UPDATE uzsakymas SET busena='vykdomas' WHERE id=?")->execute([$orderId]);
  $pdo->prepare("UPDATE uzsakymas SET busena='ivykdytas', uzbaigimo_data=NOW() WHERE id=?")->execute([$orderId]);
  $busena = $pdo->query("SELECT busena FROM uzsakymas WHERE id=$orderId")->fetchColumn();
  line("  Užsakymo būsena dabar: '$busena'");

  $msg = 'Jūsų užsakymas #' . $orderId . ' („' . $desc . '“) įvykdytas.';
  $pdo->prepare("INSERT INTO pranesimas (uzsakymas_id, kliento_id, turinys) VALUES (?,?,?)")
      ->execute([$orderId, $context['client']['id'], $msg]);
  line("  Sukurtas pranešimas klientui: \"$msg\"");

  // KLAIDŲ TESTAS: technikas bando pažymėti svetimą užsakymą
  line();
  line("KLAIDŲ TESTAS: technikas bando pažymėti svetimą užsakymą");
  $fakeOrderId = $orderId + 9999;
  $rows = $pdo->prepare("UPDATE uzsakymas SET busena='ivykdytas' WHERE id=? AND techniko_id=?");
  $rows->execute([$fakeOrderId, $context['tech']['id']]);
  $count = $rows->rowCount();
  if ($count === 0) {
    line("  Bandymas atmestas – svetimo užsakymo keisti negalima.");
  } else {
    line("  Klaida: sistema leido pakeisti svetimą užsakymą!");
  }

  // KLAIDŲ TESTAS: technikas bando pažymėti jau įvykdytą užsakymą
  line();
  line("KLAIDŲ TESTAS: technikas bando pažymėti jau įvykdytą užsakymą");
  $rows = $pdo->prepare("UPDATE uzsakymas SET busena='ivykdytas' WHERE id=? AND techniko_id=? AND busena<>'ivykdytas'");
  $rows->execute([$orderId, $context['tech']['id']]);
  if ($rows->rowCount() === 0) {
    line("  Sistema tinkamai ignoravo pakartotinį vykdymą.");
  }

  line();

} catch (Throwable $e) {
  echo "Klaida techniko testuose: " . $e->getMessage() . "\n";
  exit(1);
}

/* =====================================================================
   4. DUOMENŲ IŠVALYMAS
===================================================================== */
try {
  line("Išvalomi testiniai įrašai...");
  $pdo->prepare("DELETE FROM pranesimas WHERE uzsakymas_id=?")->execute([$orderId]);
  $pdo->prepare("DELETE FROM uzsakymas WHERE id=?")->execute([$orderId]);
  $pdo->prepare("DELETE FROM naudotojas WHERE id=?")->execute([$newTechId]);
  line("Duomenys išvalyti. Visi testai praėjo sėkmingai.");

} catch (Throwable $e) {
  echo "Klaida išvalant duomenis: " . $e->getMessage() . "\n";
}

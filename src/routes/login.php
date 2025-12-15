<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $users=$pdo->query("SELECT id, el_pastas, vardas, pavarde, role FROM naudotojas ORDER BY role, vardas")->fetchAll();
  ob_start(); ?>
  <section class="card small center">
    <h2>Prisijungimas</h2>
    <form method="post" action="/index.php?route=login">
      <label>Pasirinkite naudotoją:</label><br>
      <select name="email" required>
        <?php foreach($users as $u): ?>
          <option value="<?= htmlspecialchars($u['el_pastas']) ?>">[<?= $u['role'] ?>] <?= htmlspecialchars($u['vardas'].' '.$u['pavarde']) ?> - <?= htmlspecialchars($u['el_pastas']) ?></option>
        <?php endforeach; ?>
      </select><br><br>
      <button type="submit" class="btn">Prisijungti</button>
    </form>
  </section>
  <?php
  view(ob_get_clean());
} else {
  $email = $_POST['email'] ?? '';
  $st=$pdo->prepare("SELECT * FROM naudotojas WHERE el_pastas=?");
  $st->execute([$email]);
  $user=$st->fetch();
  if($user){ $_SESSION['user']=$user; flash('Sėkmingai prisijungėte.'); header('Location: /'); }
  else { flash('Vartotojas nerastas.'); header('Location: /index.php?route=login'); }
}

<?php
require_login();
if(!is_role('administratorius')) die('Tik administratoriui');

if($_SERVER['REQUEST_METHOD'] === 'GET'){
  ob_start(); ?>
  <section class="card small">
    <h2>Naujas naudotojas</h2>
    <form method="post">
      <label>Vardas <input name="vardas" required></label><br>
      <label>Pavardė <input name="pavarde" required></label><br>
      <label>El. paštas <input name="el_pastas" type="email" required></label><br>
      <label>Rolė
        <select name="role" required>
          <option value="administratorius">administratorius</option>
          <option value="technikas">technikas</option>
          <option value="klientas">klientas</option>
        </select>
      </label><br><br>
      <button type="submit" class="btn">Sukurti</button>
    </form>
  </section>
  <?php
  view(ob_get_clean());
} else {
  $st = $pdo->prepare("INSERT INTO naudotojas (vardas,pavarde,el_pastas,slaptazodis_hash,role) VALUES (?,?,?,?,?)");
  $st->execute([$_POST['vardas'], $_POST['pavarde'], $_POST['el_pastas'], hash('sha256','demo'), $_POST['role']]);
  flash('Naudotojas sukurtas.');
  header('Location: /');
}

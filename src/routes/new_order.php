<?php
require_login();
if(!is_role('klientas')) die('Tik klientui');

if($_SERVER['REQUEST_METHOD']==='GET'){
  ob_start(); ?>
  <section class="card small">
    <h2>Naujas užsakymas</h2>
    <form method="post" action="/index.php?route=new_order">
      <label>Aprašymas:</label><br>
      <textarea name="aprasymas" rows="4" cols="50" required placeholder="Įveskite užsakymo detales..."></textarea><br><br>
      <button type="submit" class="btn">Pateikti</button>
    </form>
  </section>
  <?php view(ob_get_clean()); }
else {
  $st=$pdo->prepare("INSERT INTO uzsakymas (kliento_id, aprasymas) VALUES (?,?)");
  $st->execute([current_user()['id'], $_POST['aprasymas']]);
  flash('Užsakymas pateiktas.');
  header('Location: /index.php?route=orders');
}

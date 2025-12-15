<?php
require __DIR__.'/lib/DB.php';
require __DIR__.'/lib/helpers.php';

$pdo = DB::pdo();
$route = $_GET['route'] ?? 'home';

$routes = [
  'home' => 'routes/home.php',
  'login' => 'routes/login.php',
  'logout' => 'routes/logout.php',
  'new_order' => 'routes/new_order.php',
  'orders' => 'routes/orders.php',
  'complete' => 'routes/complete.php',
  'admin_create_user' => 'routes/admin_create_user.php',
  'assign' => 'routes/assign.php',
  'notifications' => 'routes/notifications.php',
];

function view(string $html, array $vars = []) {
  extract($vars);
  include __DIR__.'/templates/header.php';
  echo "<main class='container'>{$html}</main>";
  include __DIR__.'/templates/footer.php';
}

if (isset($routes[$route])) {
  require $routes[$route];
  exit;
}

http_response_code(404);
echo '<h2 style="text-align:center;color:#888">Nerastas maršrutas.</h2>';

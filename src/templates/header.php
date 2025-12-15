<?php $u = current_user(); ?>
<!doctype html>
<html lang="lt">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Užsakymų sistema</title>
  <style>
    body {
      font-family: "Inter", system-ui, sans-serif;
      background: #f5f6f8;
      color: #222;
      margin: 0;
    }
    header {
      background: #ffffff;
      border-bottom: 1px solid #e4e4e4;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      padding: 0.8rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .brand {
      font-weight: 700;
      font-size: 1.1rem;
      color: #007aff;
      text-decoration: none;
    }
    nav {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    nav a {
      text-decoration: none;
      color: #333;
      padding: 0.4rem 0.6rem;
      border-radius: 6px;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    nav a:hover {
      background: #f0f3ff;
      color: #0056d1;
    }
    nav a.active {
      background: #007aff;
      color: white;
    }
    .user-info {
      font-size: 0.9rem;
      color: #555;
    }
    main.container { max-width: 800px; margin: 2rem auto; }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .card.small { max-width: 500px; margin: 3rem auto; }
    .card.center { text-align: center; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td { border-bottom: 1px solid #eee; padding: 0.6rem; text-align: left; }
    th { background: #fafafa; font-weight: 600; }
    tr:hover { background: #f9fafb; }
    .btn {
      background: #007aff;
      color: #fff;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
    }
    .btn:hover { background: #005bd1; }
    .btn.small { padding: 0.3rem 0.7rem; font-size: 0.9rem; }
    .badge {
      display: inline-block;
      padding: 0.3rem 0.6rem;
      border-radius: 6px;
      font-size: 0.8rem;
      text-transform: capitalize;
    }
    .badge.pateiktas { background: #f0f7ff; color: #005bd1; }
    .badge.vykdomas { background: #fff8e1; color: #b58900; }
    .badge.ivykdytas { background: #e7f9ef; color: #217a00; }
    label { display: block; margin-top: 0.7rem; }
    textarea, input, select {
      font-family: inherit;
      padding: 0.5rem;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }
    .flash {
      max-width: 800px;
      margin: 1rem auto;
      padding: 0.8rem 1rem;
      background: #e6f9e6;
      border: 1px solid #b8e5b8;
      color: #155724;
      border-radius: 6px;
    }
  </style>
</head>
<body>
  <header>
    <a href="/" class="brand">💼 Užsakymų sistema</a>
    <nav>
      <?php if($u): ?>
        <a href="/index.php?route=orders" class="<?= ($_GET['route']??'')==='orders'?'active':'' ?>">Užsakymai</a>
        <?php if($u['role']==='klientas'): ?>
          <a href="/index.php?route=new_order" class="<?= ($_GET['route']??'')==='new_order'?'active':'' ?>">Naujas</a>
          <a href="/index.php?route=notifications" class="<?= ($_GET['route']??'')==='notifications'?'active':'' ?>">Pranešimai</a>
        <?php endif; ?>
        <?php if($u['role']==='administratorius'): ?>
          <a href="/index.php?route=admin_create_user" class="<?= ($_GET['route']??'')==='admin_create_user'?'active':'' ?>">Sukurti naudotoją</a>
          <a href="/index.php?route=assign" class="<?= ($_GET['route']??'')==='assign'?'active':'' ?>">Priskirti techniką</a>
        <?php endif; ?>
        <a href="/index.php?route=logout">Atsijungti</a>
      <?php else: ?>
        <a href="/index.php?route=login" class="<?= ($_GET['route']??'')==='login'?'active':'' ?>">Prisijungti</a>
      <?php endif; ?>
    </nav>
    <?php if($u): ?>
      <div class="user-info">
        👤 <?= htmlspecialchars($u['vardas'].' '.$u['pavarde']) ?> (<?= htmlspecialchars($u['role']) ?>)
      </div>
    <?php endif; ?>
  </header>

  <?php if($f=flash()): ?>
    <div class="flash"><?= htmlspecialchars($f) ?></div>
  <?php endif; ?>

<?php
session_start();

function current_user() {
  return $_SESSION['user'] ?? null;
}

function require_login() {
  if (!isset($_SESSION['user'])) {
    flash('Prisijunkite, kad galėtumėte tęsti.');
    header('Location: /index.php?route=login');
    exit;
  }
}

function is_role($role) {
  return isset($_SESSION['user']) && $_SESSION['user']['role'] === $role;
}

function flash($msg = null) {
  if ($msg === null) {
    if (isset($_SESSION['_flash'])) {
      $m = $_SESSION['_flash'];
      unset($_SESSION['_flash']);
      return $m;
    }
    return null;
  }
  $_SESSION['_flash'] = $msg;
}

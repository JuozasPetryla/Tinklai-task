<?php
class DB {
  public static function pdo(): PDO {
    static $pdo;
    if ($pdo) return $pdo;

    $host = getenv('DB_HOST') ?: 'db';
    $db   = getenv('DB_NAME') ?: 'uzsakymu_sistema';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: 'root';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);
    return $pdo;
  }
}

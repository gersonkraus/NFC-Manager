<?php
function db() {
  static $pdo = null;
  if ($pdo) return $pdo;

  $configPath = __DIR__ . '/../config/config.php';
  if (!file_exists($configPath)) {
    http_response_code(500);
    die("Config missing. Copy config/config.sample.php to config/config.php and edit DB settings.");
  }
  $cfg = require $configPath;
  $dsn = "mysql:host={$cfg['db']['host']};dbname={$cfg['db']['name']};charset={$cfg['db']['charset']}";
  try {
    $pdo = new PDO($dsn, $cfg['db']['user'], $cfg['db']['pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    die("DB connection failed: ".$e->getMessage());
  }
  return $pdo;
}

function base_url() {
  $cfg = require __DIR__ . '/../config/config.php';
  return rtrim($cfg['app']['base_url'], '/');
}
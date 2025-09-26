<?php
function start_session() {
  $cfg = require __DIR__ . '/../config/config.php';
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($cfg['app']['session_name']);
    session_start();
  }
}
function is_logged_in() {
  start_session();
  return !empty($_SESSION['admin']);
}
function require_login() {
  if (!is_logged_in()) {
    header("Location: ".base_url()."/admin/login");
    exit;
  }
}
function csrf_token() {
  start_session();
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}
function check_csrf() {
  start_session();
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = $_POST['csrf'] ?? '';
    if (empty($t) || $t !== ($_SESSION['csrf'] ?? null)) {
      http_response_code(400);
      die("Invalid CSRF token");
    }
  }
}
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function gen_slug($len=6) {
  $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  $out='';
  for($i=0;$i<$len;$i++) $out .= $chars[random_int(0, strlen($chars)-1)];
  return $out;
}
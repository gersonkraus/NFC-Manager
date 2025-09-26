<?php
// Rename this file to config.php and edit the DB credentials.
return [
  'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => 'nfc_manager',
    'user' => 'admin',
    'pass' => 'sua_senha_forte',
    'charset' => 'utf8mb4',
  ],
  'app' => [
    'base_url' => 'https://gersonkraus.com/nfc_manager/public', // e.g. 'http://localhost/nfc_manager/public'
    'session_name' => 'nfc_app',
  ]
];

<?php
// Establish PDO database connection
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  define('PDO_CONNECTION', $pdo);
} catch (PDOException $e) {

  echo "Connection failed: " . $e->getMessage();
  exit();
}
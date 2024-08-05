<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';

$phone = trim($_GET['phone']);
$user = trim($_GET['user']);
$callId = trim($_GET['callid']);

$sql = "INSERT INTO callcenter.outgoing (phone, user, callid) VALUES (:phone, :user, :callId)";
$stmt = PDO_CONNECTION->prepare($sql);
$stmt->bindValue(':phone', $phone);
$stmt->bindValue(':user', $user);
$stmt->bindValue(':callId', $callId);
$result = $stmt->execute();

if (!$result) {
  echo "Error executing PDO statement: " . $stmt->errorInfo()[2];
  die();
} else {
  echo "done";
}

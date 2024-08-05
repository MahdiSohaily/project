<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';

$phone = $_GET['phone'];
$user = $_GET['user'];
$callId = $user . "-" . $phone . "-" . date("Y-m-d") . "-" . $_GET['callid'];

$sql = "INSERT INTO callcenter.incoming (phone, user, callid) VALUES (:phone, :user, :callId)";

$stmt = PDO_CONNECTION->prepare($sql);
$stmt->bindParam(':phone', $phone);
$stmt->bindParam(':user', $user);
$stmt->bindParam(':callId', $callId);
$result = $stmt->execute();

if (!$result) {
  echo "Error executing PDO statement: " . $stmt->errorInfo()[2];
  die();
} else {
  echo "done";
}

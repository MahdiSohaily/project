<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';

$phone = trim($_GET['phone']);
$user = trim($_GET['user']);
$callId = trim($_GET['callid']);
$date = date("Y-m-d");
$callId = $user . "-" . $phone . "-" . $date . "-" . $callId;

try {
  // Prepare the SQL statement with placeholders
  $sql = "UPDATE callcenter.incoming SET status = 1, starttime = CURRENT_TIMESTAMP WHERE callid LIKE :callId AND user = :user";

  // Prepare the statement
  $stmt = PDO_CONNECTION->prepare($sql);

  // Bind parameters to the prepared statement
  $stmt->bindParam('callId', $callId, PDO::PARAM_STR);
  $stmt->bindParam('user', $user, PDO::PARAM_STR);

  // Execute the statement
  $success = $stmt->execute();

  if ($success) {
    echo "done";
  } else {
    echo "Error executing prepared statement: " . $stmt->errorInfo()[2];
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

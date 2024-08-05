<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';

$phone = trim($_GET['phone']);
$user = trim($_GET['user']);
$callId = $user . "-" . $phone . "-" . date("Y-m-d") . "-" . $_GET['callid'];


try {
  // Prepare the SQL statement
  $sql = "UPDATE callcenter.incoming SET endtime = CURRENT_TIMESTAMP WHERE callid LIKE :callId AND user = :user AND status = 1";
  $stmt = PDO_CONNECTION->prepare($sql);

  // Bind parameters
  $stmt->bindParam(':callId', $callId);
  $stmt->bindParam(':user', $user);

  // Execute the statement
  $stmt->execute();

  // Check if the query was successful
  if ($stmt->rowCount() > 0) {
    echo "done";
  } else {
    echo "No rows were affected";
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

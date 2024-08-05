<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['action']) && $_POST['action'] == 'toggleTV') {
    $status = getTvStatus();
    $newStatus = $status == 'on' ? 'off' : 'on';
    $sql = "UPDATE shop.tv SET status = :status WHERE id='1'";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':status', $newStatus);
    $stmt->execute();
    echo json_encode(['status' => $newStatus]);
    exit();
}

function getTvStatus()
{
    $sql = "SELECT * FROM shop.tv WHERE id='1'";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $tvStatus = $stmt->fetch(PDO::FETCH_ASSOC);
    return $tvStatus['status'];
}

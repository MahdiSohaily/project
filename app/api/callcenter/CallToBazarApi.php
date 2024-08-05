<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['saveAskedPrice'])) {
    echo json_encode(saveAskedPrices());
}


function saveAskedPrices()
{
    $PartNumbers = preg_replace("/[^a-zA-Z0-9]/", "", $_POST['code']);
    $sellerId = $_POST['sellerid'];
    $price = $_POST['price'];
    $user_id = $_SESSION["id"];

    foreach ($PartNumbers as $index => $PartNumber) {
        if (empty($PartNumber) || empty($price[$index])) return false;
        try {
            $stmt = PDO_CONNECTION->prepare("INSERT INTO callcenter.estelam (codename, seller, price, user) VALUES (:partNumber, :sellerId, :price, :userId)");
            $stmt->bindParam(':partNumber', $PartNumber);
            $stmt->bindParam(':sellerId', $sellerId);
            $stmt->bindParam(':price', $price[$index]);
            $stmt->bindParam(':userId', $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    return true;
}

<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['saveInquiredPrices'])) {
    $INQUIRED_CODES = json_decode($_POST['inquiredCodes']);
    $INQUIRED_PRICES = json_decode($_POST['inquiredPrices'], true);
    echo json_encode(saveInquiredPrices($INQUIRED_CODES, $INQUIRED_PRICES));
}

function saveInquiredPrices($INQUIRED_CODES, $INQUIRED_PRICES)
{
    if (empty($INQUIRED_CODES) || empty($INQUIRED_PRICES)) {
        return false;
    }

    foreach ($INQUIRED_PRICES as $index => $specification) {
        $KEY_PARTS = explode('_', $index);
        $SELLER_ID = $KEY_PARTS[1];
        $CODE_INDEX = $KEY_PARTS[2];
        if (empty($INQUIRED_CODES[$CODE_INDEX])) return false;
        try {
            $stmt = PDO_CONNECTION->prepare("INSERT INTO callcenter.estelam (codename, seller, price, user) VALUES (:partNumber, :sellerId, :price, :userId)");
            $stmt->bindParam(':partNumber', $INQUIRED_CODES[$CODE_INDEX]);
            $stmt->bindParam(':sellerId', $SELLER_ID);
            $stmt->bindParam(':price', $specification['price']);
            $stmt->bindParam(':userId', $_SESSION['id']);
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    return true;
}

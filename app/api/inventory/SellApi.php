<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';
require_once '../../../utilities/inventory/ExistingHelper.php';

if (isset($_POST['searchGoods'])) {
    $pattern = $_POST['pattern'];
    $purchasedGoods = getPurchaseReport($pattern);

    echo json_encode($purchasedGoods);
}

if (isset($_POST['saveFactor'])) {
    $billItems = json_decode($_POST['billItems'], true);
    $factorInfo = json_decode($_POST['factorInfo'], true);
    global $stock;

    try {
        PDO_CONNECTION->beginTransaction();

        foreach ($billItems as $index => $item) {
            insertSellsRecord($factorInfo, $item, $stock);
        }
        PDO_CONNECTION->commit();
        echo 'success';
    } catch (PDOException $e) {
        PDO_CONNECTION->rollBack();
        echo 'error';
    }
}

function insertSellsRecord($factorInfo, $item, $stock)
{
    // Extract relevant data from $factorInfo
    $customer = $factorInfo['client'];
    $getter = $factorInfo['receiver'];
    $qty = $item['quantity'];
    $qty_id = $item['quantityId'];
    $id = $factorInfo['user'];
    $invoice_number = $factorInfo['number'];
    $description = $factorInfo['description'];
    $collector = $factorInfo['collector'];
    $invoice_time = $factorInfo['date'];

    // Prepare the SQL statement
    $sql = "INSERT INTO {$stock}.exitrecord (customer, getter, qty, qtyid, user, invoice_number, des, jamkon, invoice_date)
            VALUES (:customer, :getter, :qty, :qty_id, :id, :invoice_number, :description, :collector, :invoice_time)";

    try {
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':customer', $customer);
        $stmt->bindParam(':getter', $getter);
        $stmt->bindParam(':qty', $qty);
        $stmt->bindParam(':qty_id', $qty_id);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':collector', $collector);
        $stmt->bindParam(':invoice_time', $invoice_time);

        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_POST['getClientName'])) {
    $factorNo = $_POST['factorNo'];
    $purchasedGoods = getClientName($factorNo);

    echo json_encode($purchasedGoods);
}

function getClientName($factorNO)
{
    $sql = "SELECT kharidar FROM factor.shomarefaktor WHERE shomare = :shomarefaktor LIMIT 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':shomarefaktor', $factorNO);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return $result['kharidar'];
    } else {
        return false;
    }
}

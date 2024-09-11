<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['saveFactor'])) {

    $billItems = json_decode($_POST['billItems'], true);
    $factorInfo = json_decode($_POST['factorInfo'], true);
    global $stock;

    try {
        PDO_CONNECTION->beginTransaction();

        foreach ($billItems as $index => $item) {
            $quantity = $item['quantity'];
            $prevQty = $item['prevQuantity'];
            $qtyId = $item['quantityId'];
            $stockId = $factorInfo['stock'];
            $description = $factorInfo['description'];

            $exitId = insertSellsRecord($factorInfo, $item, $stock);
            $info = getEnteredInfo($qtyId);
            $bankId = saveNewEntrance($info, $stockId, $quantity, $description, $factorInfo);
            recordTransaction($qtyId, $bankId, $exitId, $prevQty, $quantity, $stockId);
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
    $customer = 'انتقال به انبار';
    $getter = $factorInfo['receiver'];
    $qty = $item['quantity'];
    $qty_id = $item['quantityId'];
    $id = $factorInfo['user'];
    $invoice_number = 'انتقال به انبار';
    $description = $factorInfo['description'];
    $collector = $factorInfo['collector'];
    $invoice_time = $factorInfo['date'];

    // Prepare the SQL statement
    $sql = "INSERT INTO {$stock}.exitrecord (customer, getter, qty, qtyid, user, invoice_number, des, jamkon, invoice_date, is_transfered)
            VALUES (:customer, :getter, :qty, :qty_id, :id, :invoice_number, :description, :collector, :invoice_time,1)";

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
        return PDO_CONNECTION->lastInsertId();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to get entered info
function getEnteredInfo($qtyId)
{
    global $stock;
    $statement = PDO_CONNECTION->prepare("SELECT * FROM $stock.qtybank WHERE id = :qty_id");
    $statement->bindParam(':qty_id', $qtyId);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}

// Function to save new entrance
function saveNewEntrance($info, $stockId, $quantity, $description, $item)
{
    global $stock;
    $userId = $_SESSION['id'];
    $description = $description ?? $info['des'];
    $pos1 = $item['pos1'] ?? '';
    $pos2 = $item['pos2'] ?? '';

    $statement = PDO_CONNECTION->prepare("INSERT INTO $stock.qtybank (codeid, brand, qty, pos1, pos2,
                                            des, seller, deliverer, invoice, anbarenter, user, invoice_number,
                                            stock_id, invoice_date, is_transfered)
                                            VALUES (:codeid, :brand, :quantity, :pos1, :pos2, :description, :seller,
                                            :deliverer, :invoice, :anbarenter, :user, :invoice_number, :stock_id, :invoice_date, 1)");

    $statement->bindParam(':codeid', $info['codeid']);
    $statement->bindParam(':brand', $info['brand']);
    $statement->bindParam(':quantity', $quantity);
    $statement->bindParam(':pos1', $pos1);
    $statement->bindParam(':pos2', $pos2);
    $statement->bindParam(':description', $description);
    $statement->bindParam(':seller', $info['seller']);
    $statement->bindParam(':deliverer', $info['deliverer']);
    $statement->bindParam(':invoice', $info['invoice']);
    $statement->bindParam(':anbarenter', $info['anbarenter']);
    $statement->bindParam(':user', $userId);
    $statement->bindParam(':invoice_number', $info['invoice_number']);
    $statement->bindParam(':stock_id', $stockId);
    $statement->bindParam(':invoice_date', $info['invoice_date']);

    $statement->execute();

    return PDO_CONNECTION->lastInsertId();
}

// Function to record transaction
function recordTransaction($affectedRecord, $bankId, $exitId, $prevQty, $quantity, $stockId)
{
    $userId = $_SESSION['id'];
    global $stock;

    $statement = PDO_CONNECTION->prepare("INSERT INTO $stock.transfer_record 
        (affected_record, qtybanck_id, exit_id, stock, user_id, prev_quantity, quantity)
        VALUES (:affected_record, :qtybanck_id, :exit_id, :stock, :user_id, :prev_quantity, :quantity)");

    $statement->bindParam(':affected_record', $affectedRecord);
    $statement->bindParam(':qtybanck_id', $bankId);
    $statement->bindParam(':exit_id', $exitId);
    $statement->bindParam(':stock', $stockId);
    $statement->bindParam(':user_id', $userId);
    $statement->bindParam(':prev_quantity', $prevQty);
    $statement->bindParam(':quantity', $quantity);

    $statement->execute();

    logAction(PDO_CONNECTION, 'transfer', $statement->queryString, $userId);

    return PDO_CONNECTION->lastInsertId();
}

// Function to log actions
function logAction($pdo, $action, $queryString, $userId)
{
    // Implement your logging logic here
}

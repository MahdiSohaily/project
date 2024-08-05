<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';



if (isset($_POST['action']) && $_POST['action'] == 'save_pre_bill') {
    $billId = $_POST['billId'];
    $billItems = $_POST['billItems'];
    $billItemsDescription = $_POST['billItemsDescription'];

    if (hasPreSellFactor($billId)) {
        update_pre_bill($billId, $billItems, $billItemsDescription);
    } else {
        save_pre_bill($billId, $billItems, $billItemsDescription);
    }
}


function save_pre_bill($billId, $billItems, $billItemsDescription)
{
    try {
        // Prepare the SQL statement with correct placeholders
        $sql = "INSERT INTO factor.pre_sell(bill_id, selected_items, details) 
                VALUES (:billId, :billItems, :billItemsDescription)";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Ensure the data is serialized if necessary
        $billItems = json_encode($billItems);
        $billItemsDescription = json_encode($billItemsDescription);

        // Bind values to the placeholders
        $stmt->bindValue(":billId", $billId);
        $stmt->bindValue(":billItems", $billItems);
        $stmt->bindValue(":billItemsDescription", $billItemsDescription);

        // Execute the statement and handle the result
        if ($stmt->execute()) {
            echo json_encode(array('status' => 'create', 'message' => 'Bill saved successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to save bill'));
        }
    } catch (\Throwable $th) {
        echo json_encode(array('status' => 'error', 'message' => 'An error occurred: ' . $th->getMessage()));
    }
}


function update_pre_bill($billId, $billItems, $billItemsDescription)
{
    try {
        // Prepare the SQL statement with correct placeholders
        $sql = "UPDATE factor.pre_sell SET selected_items = :billItems, details = :billItemsDescription WHERE bill_id = :billId";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Ensure the data is serialized if necessary
        $billItems = json_encode($billItems);
        $billItemsDescription = json_encode($billItemsDescription);

        // Bind values to the placeholders
        $stmt->bindValue(":billId", $billId);
        $stmt->bindValue(":billItems", $billItems);
        $stmt->bindValue(":billItemsDescription", $billItemsDescription);

        // Execute the statement and handle the result
        if ($stmt->execute()) {
            echo json_encode(array('status' => 'update', 'message' => 'Bill updated successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to update bill'));
        }
    } catch (\Throwable $th) {
        echo json_encode(array('status' => 'error', 'message' => 'An error occurred: ' . $th->getMessage()));
    }
}

function hasPreSellFactor($factorId)
{
    $sql = "SELECT * FROM factor.pre_sell WHERE bill_id = :billId";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(":billId", $factorId);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

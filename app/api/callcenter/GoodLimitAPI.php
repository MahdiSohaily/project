<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['operation'])) {
    $operation = $_POST['operation'];

    // Create a new Limit Alert object
    if ($operation == 'create') {
        $type = $_POST['type'];
        $id = $_POST['id'];

        $original = $_POST['original'];
        $fake = $_POST['fake'];

        $original_all = $_POST['original_all'];
        $fake_all = $_POST['fake_all'];
        switch ($type) {
            case 's':
                createStockLimitSingle($id, $original, $fake);
                createOverallLimitSingle($id, $original_all, $fake_all);
                break;
            case 'r':
                createStockLimitRelation($id, $original, $fake,);
                createOverallLimitRelation($id, $original_all, $fake_all);
                break;
        }

        echo true;
    }


    // Updating the existing Limit Alerts
    if ($operation == 'update') {
        $type = $_POST['type'];
        $id = $_POST['id'];

        $original = $_POST['original'];
        $fake = $_POST['fake'];

        $original_all = $_POST['original_all'];
        $fake_all = $_POST['fake_all'];

        switch ($type) {
            case 's':
                updateStockLimitSingle($id, $original, $fake,);
                updateOverallLimitSingle($id, $original_all, $fake_all);
                break;
            case 'r':
                updateStockLimitRelation($id, $original, $fake,);
                updateOverallLimitRelation($id, $original_all, $fake_all);
                break;
        }
        echo true;
    }
}


function createStockLimitSingle($id, $original, $fake)
{
    // INSERT INVENTORY ALERT FOR SPECIFIC INVENTORY
    $stock_id = 9;
    $limit_sql = PDO_CONNECTION->prepare("INSERT INTO shop.good_limit_inventory (nisha_id, original, fake, user_id, stock_id) VALUES (?, ?, ?, ?, ?)");
    $limit_sql->execute([$id, $original, $fake, $_SESSION['user_id'], $stock_id]);
}

function createOverallLimitSingle($id, $original_all, $fake_all)
{
    // INSERT GOODS ALERT WITHIN ALL THE AVAILABLE STOCKS (GENERAL GOODS AMOUNT ALERT)
    $limit_sql = PDO_CONNECTION->prepare("INSERT INTO shop.good_limit_all (nisha_id, original, fake, user_id) VALUES (?, ?, ?, ?)");
    $limit_sql->execute([$id, $original_all, $fake_all, $_SESSION['user_id']]);
}

function createStockLimitRelation($id, $original, $fake)
{
    // INSERT INVENTORY ALERT FOR SPECIFIC INVENTORY
    $stock_id = 9;
    $limit_sql = PDO_CONNECTION->prepare("INSERT INTO shop.good_limit_inventory (pattern_id, original, fake, user_id, stock_id) VALUES (?, ?, ?, ?, ?)");
    $limit_sql->execute([$id, $original, $fake, $_SESSION['user_id'], $stock_id]);
}

function createOverallLimitRelation($id, $original_all, $fake_all)
{
    // INSERT GOODS ALERT WITHIN ALL THE AVAILABLE STOCKS (GENERAL GOODS AMOUNT ALERT)
    $limit_sql = PDO_CONNECTION->prepare("INSERT INTO shop.good_limit_all (pattern_id, original, fake, user_id) VALUES (?, ?, ?, ?)");
    $limit_sql->execute([$id, $original_all, $fake_all, $_SESSION['user_id']]);
}

function updateStockLimitSingle($id, $original, $fake)
{
    // Update the Inventories limit for goods alert for specific pattern
    $updateInventoryLimit = PDO_CONNECTION->prepare("UPDATE shop.good_limit_inventory SET original= ?, fake = ? WHERE nisha_id = ?");
    $updateInventoryLimit->execute([$original, $fake, $id]);
}

function updateOverallLimitSingle($id, $original_all, $fake_all)
{
    // Update the over all alert for goods in specific relation
    $updateAllLimit = PDO_CONNECTION->prepare("UPDATE shop.good_limit_all SET original= ?, fake = ? WHERE nisha_id = ?");
    $updateAllLimit->execute([$original_all, $fake_all, $id]);
}

function updateStockLimitRelation($id, $original, $fake)
{
    // Update the Inventories limit for goods alert for specific pattern
    $updateInventoryLimit = PDO_CONNECTION->prepare("UPDATE shop.good_limit_inventory SET original= ?, fake = ? WHERE pattern_id = ?");
    $updateInventoryLimit->execute([$original, $fake, $id]);
}

function updateOverallLimitRelation($id, $original_all, $fake_all)
{
    // Update the over all alert for goods in specific relation
    $updateAllLimit = PDO_CONNECTION->prepare("UPDATE shop.good_limit_all SET original= ?, fake = ? WHERE pattern_id = ?");
    $updateAllLimit->execute([$original_all, $fake_all, $id]);
}

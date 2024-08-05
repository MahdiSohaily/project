<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

if (isset($_GET['interval'])) {
    $interval = $_GET['interval'];
}

$todays_records = getTodayRecords();
$previous_records = getPreviousRecords();


function getTodayRecords()
{
    global $stock;
    // Create a DateTime object for today
    $today = new DateTime();

    // Subtract one day
    $yesterday = $today;

    // Format and display the result
    $yesterday = $yesterday->format('Y-m-d') . ' 00:00:00';

    $statement = PDO_CONNECTION->prepare("SELECT transfer_record.*, qtybank.qty AS previous_amount,
        nisha.partnumber, brand.name As brand_name, seller.name AS seller_name, getter.name AS getter_name,
        users.name AS user_name, qtybank.stock_id, exitrecord.des
        FROM $stock.transfer_record
        INNER JOIN $stock.qtybank ON qtybank.id =  transfer_record.affected_record
        INNER JOIN nisha ON nisha.id = qtybank.codeid
        INNER JOIN $stock.exitrecord ON exitrecord.id  = transfer_record.exit_id
        LEFT JOIN brand ON brand.id = qtybank.brand
        LEFT JOIN seller ON seller.id = qtybank.seller
        LEFT JOIN getter ON getter.id = exitrecord.getter
        INNER JOIN users ON users.id = transfer_record.user_id
        WHERE transfer_record.transfer_date > :transfer_date
        ORDER BY transfer_record.transfer_date DESC");
    $statement->bindParam(':transfer_date', $yesterday);
    $statement->execute();

    // set the resulting array to associative
    $statement->setFetchMode(PDO::FETCH_ASSOC);
    $today =  $statement->fetchAll();
    return $today;
}

function getPreviousRecords()
{
    global $stock;
    // Create a DateTime object for today
    $today = new DateTime();

    $statement = PDO_CONNECTION->prepare("SELECT transfer_record.*, qtybank.qty AS previous_amount,
        nisha.partnumber, brand.name As brand_name, seller.name AS seller_name, getter.name AS getter_name,
        users.name AS user_name, qtybank.stock_id, exitrecord.des
        FROM $stock.transfer_record
        INNER JOIN $stock.qtybank ON qtybank.id =  transfer_record.affected_record
        INNER JOIN nisha ON nisha.id = qtybank.codeid
        INNER JOIN $stock.exitrecord ON exitrecord.id  = transfer_record.exit_id
        LEFT JOIN brand ON brand.id = qtybank.brand
        LEFT JOIN seller ON seller.id = qtybank.seller
        LEFT JOIN getter ON getter.id = exitrecord.getter
        INNER JOIN users ON users.id = transfer_record.user_id
        WHERE DATE(transfer_record.transfer_date) < NOW()
        ORDER BY transfer_record.transfer_date DESC");

    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_ASSOC);
    $display =  $statement->fetchAll();
    return $display;
}


function getStockName($stock_id)
{
    $statement = PDO_CONNECTION->prepare("SELECT name FROM stock WHERE id = :stock_id");
    $statement->bindParam(":stock_id", $stock_id);
    $statement->execute();

    // set the resulting array to associative
    $statement->setFetchMode(PDO::FETCH_ASSOC);
    $result =  $statement->fetch();
    return $result['name'];
}

function getSanitizedData($quantity, $id)
{
    global $stock;
    $statement = PDO_CONNECTION->prepare("SELECT qty FROM $stock.exitrecord WHERE qtyid = :id");

    $statement->bindParam(":id", $id);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_ASSOC);

    $allExit =  $statement->fetchAll();

    foreach ($allExit as $record) {
        $quantity -= $record["qty"];
    }
    return $quantity;
}

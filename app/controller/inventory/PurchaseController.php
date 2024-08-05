<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

// Check if 'interval' parameter is set and is a valid number
if (isset($_GET['interval']) && is_numeric($_GET['interval'])) {
    $interval = (int) $_GET['interval'];
}

if (isset($interval)) {
    // Get today's date
    $todayDate = date('Y-m-d H:i:s', strtotime('today 23:59:59'));

    // Calculate the date from 'interval' days ago
    $previousDate = date('Y-m-d H:i:s', strtotime('-' . $interval . ' days 00:00:00'));

    $condition = " WHERE qtybank.create_time >= :previousDate AND qtybank.create_time <= :todayDate";
} else {
    $condition = 'WHERE 1=1'; 
}

$purchaseList = getPurchaseReports($condition, $previousDate ?? null, $todayDate ?? null);

function getPurchaseReports($condition, $previousDate = null, $todayDate = null)
{
    global $stock;
    // Prepare the SQL statement
    $sql = "SELECT qtybank.id AS purchase_id,
                   qtybank.des AS purchase_description,
                   qtybank.qty AS purchase_quantity,
                   qtybank.pos1 AS purchase_position1,
                   qtybank.pos2 AS purchase_position2,
                   qtybank.create_time AS purchase_time,
                   qtybank.anbarenter AS purchase_isEntered,
                   qtybank.invoice AS purchase_hasBill,
                   qtybank.invoice_number,
                   qtybank.invoice_date,
                   nisha.partnumber,
                   nisha.price AS good_price,
                   seller.id AS seller_id,
                   seller.name AS seller_name,
                   brand.name AS brand_name,
                   deliverer.name AS deliverer_name,
                   users.username AS username,
                   stock.name AS stock_name
            FROM $stock.qtybank
            INNER JOIN nisha ON qtybank.codeid = nisha.id
            INNER JOIN brand ON qtybank.brand = brand.id
            LEFT JOIN seller ON qtybank.seller = seller.id
            LEFT JOIN deliverer ON qtybank.deliverer = deliverer.id
            LEFT JOIN users ON qtybank.user = users.id
            LEFT JOIN stock ON qtybank.stock_id = stock.id 
            $condition
            AND qtybank.is_transfered = 0
            ORDER BY qtybank.create_time DESC";

    $statement = PDO_CONNECTION->prepare($sql);

    // Bind parameters if interval condition is applied
    if ($previousDate && $todayDate) {
        $statement->bindParam(':previousDate', $previousDate);
        $statement->bindParam(':todayDate', $todayDate);
    }

    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


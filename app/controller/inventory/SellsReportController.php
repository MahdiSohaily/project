<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

// Check if 'interval' parameter is set and is a valid number
if (isset($_GET['interval']) && is_numeric($_GET['interval'])) {
    $interval = (int) $_GET['interval'];
}

if (isset($interval)) {
    date_default_timezone_set('Asia/Tehran');
    // Get today's date
    // Get today's date
    $todayDate = date('Y-m-d H:i:s', strtotime('today 23:59:59'));

    // Calculate the date from 'interval' days ago
    $previousDate = date('Y-m-d H:i:s', strtotime('-' . $interval . ' days 00:00:00'));

    $condition = " WHERE exitrecord.exit_time >= :previousDate AND exitrecord.exit_time <= :todayDate";
} else {
    $condition = 'WHERE 1=1';
}

$soldItemsList = getSoldItemsList($condition, $previousDate ?? null, $todayDate ?? null);

function getSoldItemsList($condition, $previousDate = null, $todayDate = null)
{
    global $stock;
    $sql = "SELECT qtybank.id AS purchase_id,
        qtybank.des AS purchase_description,
        qtybank.qty AS purchase_quantity,
        qtybank.anbarenter AS purchase_isEntered,
        qtybank.invoice_number AS qty_invoice_number,
        qtybank.invoice_date AS qty_invoice_date,
        nisha.id AS partNumber_id,
        nisha.partnumber,
        users.username AS username,
        seller.id AS seller_id,
        seller.name AS seller_name,
        stock.name AS stock_name,
        brand.name AS brand_name,
        exitrecord.qty AS sold_quantity,
        exitrecord.id AS sold_id,
        exitrecord.customer AS sold_customer,
        exitrecord.des AS sold_description,
        exitrecord.exit_time AS sold_time,
        exitrecord.jamkon,
        exitrecord.invoice_number AS sold_invoice_number,
        exitrecord.invoice_date AS sold_invoice_date,
        getter.id AS getter_id,
        getter.name AS getter_name,
        deliverer.id AS deliverer_id,
        deliverer.name AS deliverer_name
        FROM $stock.qtybank
        INNER JOIN nisha ON qtybank.codeid = nisha.id
        INNER JOIN $stock.exitrecord ON qtybank.id = exitrecord.qtyid
        LEFT JOIN seller ON qtybank.seller = seller.id
        LEFT JOIN brand ON qtybank.brand = brand.id
        LEFT JOIN stock ON qtybank.stock_id = stock.id
        LEFT JOIN users ON exitrecord.user = users.id
        LEFT JOIN deliverer ON qtybank.deliverer = deliverer.id
        LEFT JOIN getter ON exitrecord.getter = getter.id
        $condition
        AND exitrecord.is_transfered = 0
        ORDER BY  exitrecord.exit_time DESC";

    $statement = PDO_CONNECTION->prepare($sql);

    // Bind parameters if interval condition is applied
    if ($previousDate && $todayDate) {
        $statement->bindParam(':previousDate', $previousDate);
        $statement->bindParam(':todayDate', $todayDate);
    }

    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

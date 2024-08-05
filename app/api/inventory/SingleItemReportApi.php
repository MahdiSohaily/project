<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

// Fetch data for the given part number from the database
function fetchData($pdo, $partNumber, $query)
{
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':partNumber', $partNumber, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Query to fetch exit records
$exitQuery = "
SELECT
    nisha.partnumber,
    users.username AS username,
    seller.name AS sellerName,
    stock.name AS stockName,
    brand.name AS brandName,
    exitrecord.qty AS quantity,
    exitrecord.customer,
    exitrecord.des AS description,
    exitrecord.exit_time AS create_time,
    exitrecord.invoice_number,
    exitrecord.invoice_date
FROM
    $stock.qtybank
INNER JOIN
    nisha ON qtybank.codeid = nisha.id
INNER JOIN
    $stock.exitrecord ON qtybank.id = exitrecord.qtyid
LEFT JOIN
    seller ON qtybank.seller = seller.id
LEFT JOIN
    brand ON qtybank.brand = brand.id
LEFT JOIN
    stock ON qtybank.stock_id = stock.id
LEFT JOIN
    users ON exitrecord.user = users.id
LEFT JOIN
    deliverer ON qtybank.deliverer = deliverer.id
LEFT JOIN
    getter ON exitrecord.getter = getter.id
WHERE
    nisha.partnumber = :partNumber
ORDER BY
    exitrecord.exit_time DESC,
    exitrecord.invoice_number DESC";

// Query to fetch qty bank records
$qtyBankQuery = "
SELECT
    nisha.partnumber,
    brand.name AS brandName,
    qtybank.des AS description,
    qtybank.qty AS quantity,
    qtybank.create_time,
    seller.name AS sellerName,
    users.username AS username,
    qtybank.invoice_number,
    qtybank.invoice_date,
    stock.name AS stockName
FROM
    $stock.qtybank
LEFT JOIN
    nisha ON qtybank.codeid = nisha.id
LEFT JOIN
    brand ON qtybank.brand = brand.id
LEFT JOIN
    seller ON qtybank.seller = seller.id
LEFT JOIN
    deliverer ON qtybank.deliverer = deliverer.id
LEFT JOIN
    users ON qtybank.user = users.id
LEFT JOIN
    stock ON qtybank.stock_id = stock.id
WHERE
    nisha.partnumber = :partNumber
ORDER BY
    qtybank.create_time DESC";

// Fetch data from both queries
$partNumber = $_POST['partNumber'];
try {
    $exitRecords = fetchData($pdo, $partNumber, $exitQuery);
    $qtyBankRecords = fetchData($pdo, $partNumber, $qtyBankQuery);

    // Mark records as export or import
    foreach ($exitRecords as &$record) {
        $record['export'] = true;
    }
    foreach ($qtyBankRecords as &$record) {
        $record['import'] = true;
    }
    unset($record); // Unset reference to avoid issues

    // Combine results and sort by create_time
    $combinedResults = array_merge($exitRecords, $qtyBankRecords);
    usort($combinedResults, function ($a, $b) {
        return strtotime($b['create_time']) - strtotime($a['create_time']);
    });

    // Output the combined result as JSON
    echo json_encode($combinedResults);
} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}

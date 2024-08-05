<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$status = getStatus();
$totalRegisteredGoods = getRegisteredGoods();
$totalContacts = getContacts();
$totalRequests = getTotalRequests();
$lastHourMostRequested = getLastHourMostRequested(); //
$todayMostRequested = getTodayMostRequested(); //
$allTimeMostRequested = getAllTimeMostRequested();

function getRegisteredGoods()
{
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total
                                    FROM telegram.goods_for_sell");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

function getContacts()
{
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total
                                    FROM telegram.receiver");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

function getTotalRequests()
{
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total
                                    FROM telegram.messages
                                    WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

function getLastHourMostRequested()
{
    $sql = "SELECT request, COUNT(id) AS quantity
            FROM telegram.messages 
            WHERE created_at >= NOW() - INTERVAL 1 HOUR 
            GROUP BY request
            ORDER BY quantity DESC LIMIT 10";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getTodayMostRequested()
{
    // SQL query to count the number of requests for the current date, grouped by the request
    $sql = "SELECT request, COUNT(id) AS quantity 
            FROM telegram.messages 
            WHERE DATE(created_at) = CURDATE() 
            GROUP BY request
            ORDER BY quantity DESC LIMIT 10"; // Order by quantity to get the most requested items at the top

    // Prepare the SQL statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Execute the SQL statement
    $stmt->execute();

    // Fetch all results as associative arrays
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the result
    return $result;
}

function getAllTimeMostRequested()
{
    // SQL query to count the number of requests for all time, grouped by the request
    $sql = "SELECT request, COUNT(id) AS quantity 
            FROM telegram.messages 
            GROUP BY request
            ORDER BY quantity DESC LIMIT 10"; // Order by quantity to get the most requested items at the top

    // Prepare the SQL statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Execute the SQL statement
    $stmt->execute();

    // Fetch all results as associative arrays
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the result
    return $result;
}

function getStatus()
{
    $sql = "SELECT * FROM telegram.receiver_cat WHERE id = 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['status'];
}

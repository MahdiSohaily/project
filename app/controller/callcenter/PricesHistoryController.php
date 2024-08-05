<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$givenPrice = getGivenPrice();
$pinedAskedPrice = getPinedAskedPrice();
$askedPrice = getAskedPrices();

function getGivenPrice()
{
    $sql = "SELECT 
        prices.price, prices.partnumber, users.username,customer.id AS customerID, users.id as userID, prices.created_at, customer.name, customer.family,
        customer.phone
        FROM ((shop.prices 
        INNER JOIN callcenter.customer ON customer.id = prices.customer_id )
        INNER JOIN yadakshop.users ON users.id = prices.user_id)
        ORDER BY prices.created_at DESC LIMIT 50";

    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getPinedAskedPrice()
{
    $sql = "SELECT  customer.name, customer.family, customer.phone, record.id as recordID, 
                    record.time, record.callinfo, record.pin, users.id AS userID
            FROM ((callcenter.record
            INNER JOIN callcenter.customer ON record.phone = customer.phone)
            INNER JOIN yadakshop.users ON record.user = users.id)
            WHERE record.pin = 'pin'
            ORDER BY record.time DESC";

    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAskedPrices()
{
    $sql = "SELECT  customer.name, customer.family, customer.phone, record.id as recordID, record.time, record.callinfo, record.pin, users.id AS userID
            FROM ((callcenter.record
            INNER JOIN callcenter.customer ON record.phone = customer.phone)
            INNER JOIN yadakshop.users ON record.user = users.id)
            WHERE record.pin = 'unpin'
            ORDER BY record.time DESC
            LIMIT 30";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

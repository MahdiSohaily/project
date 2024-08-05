<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$totalUsers = getUsers();
$totalFactors = getFactors();
$totalGoods = getPurchasedGoods();
$totalSold = getSoldGoods();


function getUsers()
{
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total FROM yadakshop.users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

function getFactors()
{
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total FROM factor.bill");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

function getPurchasedGoods()
{
    global $stock;
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total FROM $stock.qtybank");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

function getSoldGoods()
{
    global $stock;
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(id) as total FROM $stock.exitrecord");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
}

<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$cars = getCars();
$goodStatus = getGoodStatus();

function getCars()
{
    $sql = "SELECT * FROM shop.cars";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGoodStatus()
{
    $sql = "SELECT * FROM shop.status";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

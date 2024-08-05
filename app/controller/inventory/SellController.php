<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$receivers = getReceivers();
$stocks = getStocks();

function getReceivers()
{
    $stmt = PDO_CONNECTION->prepare("SELECT * FROM getter ORDER BY sort DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStocks()
{
    $stmt = PDO_CONNECTION->prepare("SELECT * FROM stock ORDER BY sort DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

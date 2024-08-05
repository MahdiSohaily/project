<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$rates = getRates();

function getRates()
{
    $sql = "SELECT * FROM shop.rates WHERE selected = 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

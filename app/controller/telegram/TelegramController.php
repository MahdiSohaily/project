<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$contacts = getContacts();
$selectedGoods = getSelectedGoods();
$newContacts = null;

function getSelectedGoods()
{
    $sql = "SELECT * FROM telegram.goods_for_sell";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getContacts()
{
    $sql = "SELECT * FROM telegram.receiver";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

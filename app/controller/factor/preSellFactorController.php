<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

function formatAsMoney($number)
{
    return number_format($number, 0, '.', ',');
}

$billItems = json_decode($billItems . '', true);

$factorId = $factorInfo['id'];

$preSellFactor = null;
$preSellFactorItems = '{}';
$preSellFactorItemsDescription = '{}';

if (hasPreSellFactor($factorId)) {
    $preSellFactor = getPreSellFactor($factorId);
    $preSellFactorItems = $preSellFactor['selected_items'];
    $preSellFactorItemsDescription = $preSellFactor['details'];
}

function hasPreSellFactor($factorId)
{
    $sql = "SELECT * FROM factor.pre_sell WHERE bill_id = :billId";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(":billId", $factorId);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function getPreSellFactor($factorId)
{
    $sql = "SELECT * FROM factor.pre_sell WHERE bill_id = :billId";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(":billId", $factorId);
    $stmt->execute();
    return $stmt->fetch();
}

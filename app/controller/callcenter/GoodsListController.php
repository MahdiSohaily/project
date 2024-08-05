<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 200;
$amount = $page * $limit;

$goods = getGoods($limit, $amount);
$totalPages = ceil(allGoodsCount() / $limit);

function getGoods($limit, $amount)
{
    $sql = "SELECT * FROM yadakshop.nisha OFFSET LIMIT :limit OFFSET :amount";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function allGoodsCount()
{
    $sql = "SELECT COUNT(*) FROM yadakshop.nisha";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
}

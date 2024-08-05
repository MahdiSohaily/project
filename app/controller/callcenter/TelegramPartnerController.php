<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$current_partners = getExistingTelegramPartners();

$categories = getCategories();

$partners_json = json_encode($current_partners, true);

function getExistingTelegramPartners()
{
    $sql = "SELECT * FROM telegram.telegram_partner";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $partners;
}

function getCategories()
{
    $sql = "SELECT * FROM telegram.partner_categories";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}

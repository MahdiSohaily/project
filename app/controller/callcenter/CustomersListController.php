<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page from URL parameter
$fetchLimit = 200;
$customers = getCustomers($page, $fetchLimit);
$customersCount = getCustomerCount();

function getCustomers($current_page, $fetchLimit)
{
    $offset = ($current_page - 1) * $fetchLimit; // Calculate the offset
    $stmt = PDO_CONNECTION->prepare("SELECT * FROM callcenter.customer LIMIT :fetchLimit OFFSET :offset");
    $stmt->bindParam(':fetchLimit', $fetchLimit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomerCount()
{
    $stmt = PDO_CONNECTION->prepare("SELECT COUNT(*) FROM callcenter.customer");
    $stmt->execute();
    return $stmt->fetchColumn();
}
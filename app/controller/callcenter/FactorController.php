<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

require_once "../../app/partials/factors/helpers.php";
// Set the date to today and adjust the tome period to get todays factors
$date = date('Y-m-d H:i:s');
$startDate = date_create(date('Y-m-d H:i:s'));
$endDate = date_create(date('Y-m-d H:i:s'));

$endDate = $endDate->setTime(23, 59, 59);
$startDate = $startDate->setTime(1, 1, 0);

$end = date_format($endDate, "Y-m-d H:i:s");
$start = date_format($startDate, "Y-m-d H:i:s");

$factors = getFactors($start, $end);

$countFactorByUser = getCountFactorByUser($start, $end);

$users = getUsers();

function getUsers()
{
    $stmt = PDO_CONNECTION->prepare("SELECT id, username, name, family 
    FROM yadakshop.users 
    WHERE name IS NOT NULL AND name != '' AND password IS NOT NULL AND password != ''");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

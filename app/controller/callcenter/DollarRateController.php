<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

if (isset($_POST['status'])) {

    $status = $_POST['status'];
    $sql = "UPDATE shop.dollarrate SET status = :status WHERE id = 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->execute();
    $status = true;
}

if (isset($_POST['rate'])) {
    try {
        $rate = $_POST['rate'];
        $data = $_POST['date'];

        insertNewRate($rate, $data);
    } catch (\Throwable $th) {
        echo $th;
    }
}

$dollarRate  = getDollarRateInfo();
$status = null;

function getDollarRateInfo()
{
    $statement = "SELECT * FROM shop.dollarrate ORDER BY created_at DESC";
    $stmt = PDO_CONNECTION->prepare($statement);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function insertNewRate($rate, $date)
{
    $sql = "INSERT INTO shop.dollarrate (rate, created_at) VALUES (:rate, :date)";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':rate', $rate, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    return $stmt->execute();
}

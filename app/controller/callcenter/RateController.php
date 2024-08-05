<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$form = 'create';

if (isset($_GET['form'])) {
    $form = $_GET['form'];
}

$errors = false;
$success = false;
$selected_rate = null;

if (isset($_GET['form'], $_GET['id'])) {
    $id = $_GET['id'];
    $rate_sql = "SELECT * FROM shop.rates WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($rate_sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $selected_rate = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Create a new good IN THE 
if (isset($_POST['rate_price']) && ($_POST['form'] == 'create')) {
    $rate_price = $_POST['rate_price'];
    $status = $_POST['status'];
    $selected = $_POST['selected'] ?? null;

    $selected = $selected == 'on' ? 1 : 0;

    $sql = "INSERT INTO shop.rates (amount, status, selected)
            VALUES (:amount, :status, :selected)";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':amount', $rate_price);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':selected', $selected);

    if ($stmt->execute()) {
        $success = "اطلاعات موفقانه در پایگاه داده ذخیره شد.";
    } else {
        $errors = "ذخیره سازی اطلاعات ناموفق بود";
    }
}

if (isset($_POST['rate_price']) && ($_POST['form'] == 'update')) {
    $rate_price = $_POST['rate_price'];
    $status = $_POST['status'];

    $id = $_GET['id'];

    $sql = "UPDATE shop.rates SET amount = :amount , status = :status WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':amount', $rate_price);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);


    if ($stmt->execute()) {
        $rate_sql = "SELECT * FROM shop.rates WHERE id = :id";
        $stmt = PDO_CONNECTION->prepare($rate_sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $selected_rate = $stmt->fetch(PDO::FETCH_ASSOC);
        $success = "اطلاعات موفقانه ویرایش گردید.";
    } else {
        $errors = " ویرایش اطلاعات ناموفق بود";
    }
}

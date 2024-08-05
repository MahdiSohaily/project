<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$errors = false;
$success = false;
$selected_good = null;
$delete_success = false;

if (isset($_GET['form'], $_GET['id'])) {
    $id = $_GET['id'];
    $good_sql = "SELECT * FROM yadakshop.nisha WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($good_sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $selected_good = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['partNumber'])) {
    $partNumber = strtoupper(trim($_POST['partNumber']));
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $mobis = $_POST['mobis'];
    $korea = $_POST['korea'];
    $formType = $_POST['form'];

    // Prepare the SQL statement based on form type
    if ($formType == 'create') {
        $sql = "INSERT INTO yadakshop.nisha (partnumber, price, weight, mobis, korea) VALUES (?, ?, ?, ?, ?)";
        $successMessage = "اطلاعات موفقانه در پایگاه داده ذخیره شد.";
        $errorMessage = "ذخیره سازی اطلاعات ناموفق بود";
    } elseif ($formType == 'update' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "UPDATE yadakshop.nisha SET partnumber = ?, price = ?, weight = ?, mobis = ?, korea = ? WHERE id = ?";
        $successMessage = "اطلاعات موفقانه ویرایش گردید.";
        $errorMessage = "ویرایش اطلاعات ناموفق بود";
    } else {
        $errorMessage = "Invalid form type.";
    }

    try {
        // Prepare the SQL statement
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(1, $partNumber);
        $stmt->bindParam(2, $price);
        $stmt->bindParam(3, $weight);
        $stmt->bindParam(4, $mobis);
        $stmt->bindParam(5, $korea);
        if ($formType == 'update') {
            $stmt->bindParam(6, $id);
        }
        
        // Execute the statement
        if ($stmt->execute()) {
            $success = $successMessage;
            if ($formType == 'update') {
                // Retrieve the updated good
                $good_sql = "SELECT * FROM yadakshop.nisha WHERE id = ?";
                $stmt = PDO_CONNECTION->prepare($good_sql);
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $selected_good = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } else {
            $errors = $errorMessage;
        }
    } catch (PDOException $e) {
        $errors = "PDO Error: " . $e->getMessage();
    }
}

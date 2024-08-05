<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['Delete_Good'])) {

    $delete_id = $_POST['delete_id'];
    // sql to delete a record
    $sql = "DELETE FROM yadakshop.nisha WHERE id= :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Update rate

if (isset($_POST['update_selected_rate'])) {
    $id = $_POST['element_id'];
    $element_value = $_POST['element_value'] == 'true' ? 1 : 0;

    $sql = "UPDATE shop.rates SET  selected = :value WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':value', $element_value, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        return true;
    } else {
        $errors = " ویرایش اطلاعات ناموفق بود";
    }
}

if (isset($_POST['Delete_rate'])) {

    $delete_id = $_POST['delete_id'];
    echo $delete_id;
    // sql to delete a record
    $sql = "DELETE FROM shop.rates WHERE id = :id ";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

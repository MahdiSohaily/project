<?php
header('Content-Type: application/json');
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';
require_once '../../../utilities/inventory/ExistingHelper.php';


if (isset($_POST['mode'])) {
    $mode = $_POST['mode'];
    if ($mode === 'create') {
        $sellers = createNewSeller($_POST);
        echo json_encode($sellers);
    } else if ($mode === 'update') {
        $brands = getBrands();
        echo json_encode($brands);
    }
}

function createNewSeller($data)
{
    $name = $data['name'];
    $latinName = $data['latinName'];
    $phone = $data['phone'];
    $address = $data['address'];
    $kind = $data['kind'];
    $view = $data['view'];

    $sql = "INSERT INTO seller (name, latinName, phone, address, kind, view)
            VALUES (:name, :latinName, :phone, :address, :kind, :view)";

    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':latinName', $latinName);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':kind', $kind);
    $stmt->bindParam(':view', $view);

    if ($stmt->execute()) {
        return true;
    }

    return false;
}


if (isset($_POST['updateView'])) {
    $id = $_POST['id'];
    $table = $_POST['table'];

    $sql = "UPDATE $table SET views = CASE WHEN views = 1 THEN 0 ELSE 1 END WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo true;
    } else {
        echo false;
    }
}

if (isset($_POST['updateSeller'])) {
    $id = $_POST['id'];
    $field = $_POST['field'];
    $value = $_POST['value'];
    $table = $_POST['table'];

    if ($field == 'phone') {
        $value = str_replace(' ', PHP_EOL, $value);
    }

    $sql = "UPDATE $table SET $field = :value WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':value', $value);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo true;
    } else {
        echo false;
    }
}

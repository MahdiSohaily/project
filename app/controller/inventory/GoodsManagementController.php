<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$sellers = getSellers();
$brands = getBrands();

function getSellers()
{
    $statement = PDO_CONNECTION->prepare("SELECT id, name, latinName, phone, address, views , kind FROM seller");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function getBrands()
{
    $statement = PDO_CONNECTION->prepare("SELECT * FROM brand");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

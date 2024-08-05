<?php

function getBrands()
{
    $statement = PDO_CONNECTION->prepare("SELECT * FROM brand WHERE views = 1 ORDER BY name ASC");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getSellers()
{
    $statement = PDO_CONNECTION->prepare("SELECT id, name, latinName FROM seller WHERE views = 1 ORDER BY name ASC");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getStocks()
{
    $statement = PDO_CONNECTION->prepare("SELECT id, name FROM stock ORDER BY name ASC");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getDeliverers()
{
    $statement = PDO_CONNECTION->prepare("SELECT id, name FROM deliverer ORDER BY name ASC");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getUsers()
{
    $statement = PDO_CONNECTION->prepare("SELECT id, name, username FROM users 
                                            WHERE password IS NOT NULL AND password !='' 
                                            ORDER BY username ASC");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getBrandBackground($name)
{
    switch ($name) {
        case 'MOB':
            return 'bg-sky-900';
        case 'GEN':
            return 'bg-slate-800';
        default:
            return 'bg-red-600';
    }
}

function getGetters()
{
    $statement = PDO_CONNECTION->prepare("SELECT * FROM yadakshop.getter ORDER BY name ASC");

    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

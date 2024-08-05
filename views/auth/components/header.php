<?php
require_once '../../config/constants.php';
require_once "../../database/db_connect.php";
require_once "../../app/middleware/Authentication.php";
require_once '../../app/controller/auth/LoginController.php'; ?>
<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="icon" href="../../public/img/logo.jpg" sizes="32x32">

    <link href="../../public/css/output.css" rel="stylesheet">
    <link href="./assets/css/login.css" rel="stylesheet">
</head>

<body>
<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';
require_once '../../app/middleware/Authentication.php';
require_once '../../app/middleware/Authorize.php';
require_once '../../app/controller/tv/TvController.php';
require_once '../../utilities/jdf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="icon" type="image/x-icon" href="./assets/icons/<?= $iconUrl ?>">
    <link rel='stylesheet' href='./assets/css/tv.css?v=<?= rand() ?>' type='text/css' media='all' />
    <link rel='stylesheet' href='../../public/css/generalStyles.css' type='text/css' media='all' />
    <script src="../../public/js/assets/axios.js"></script>
    <style>
        .circle-frame {
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            background-color: black;
            color: white;
            font-weight: bold;
            width: 30px;
            height: 30px;
            margin-inline: auto;
        }
    </style>
</head>

<body>
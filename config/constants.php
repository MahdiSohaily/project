<?php
session_name("MyAppSession");
session_start();
date_default_timezone_set("Asia/Tehran");
// Create a PDO connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "yadakshop";

if (isset($_SESSION["financialYear"])) {
    $stock = 'stock_' . $_SESSION["financialYear"];
}

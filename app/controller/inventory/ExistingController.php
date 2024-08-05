<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

require_once '../../utilities/inventory/ExistingHelper.php';
$purchasedGoods = getPurchaseReport();


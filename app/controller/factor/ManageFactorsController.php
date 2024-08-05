<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$users = getUsers();
define('MONTHS', ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند']);
define('DAYS', [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29]);

function getUsers()
{
    $stmt = PDO_CONNECTION->prepare("SELECT DISTINCT(user_id) AS id, name, family FROM factor.bill
            INNER JOIN yadakshop.users ON user_id = yadakshop.users.id ORDER BY bill.created_at DESC");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    if ($result) {
        foreach ($result as $row) {
            array_push($data, $row);
        }
    }
    return $data;
}

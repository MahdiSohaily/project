<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$user = isset($_GET['user']) ? $_GET['user'] : getInternal($_SESSION["id"]);

function getInternal($id)
{
    try {
        // SQL query to fetch internal from users table based on ID
        $sql = "SELECT callcenter.internal FROM users WHERE id = :id";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $internal = $stmt->fetchColumn();

        // Close the statement
        $stmt->closeCursor();

        return $internal;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function getTvStatus()
{
    $sql = "SELECT * FROM shop.tv WHERE id='1'";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $tvStatus = $stmt->fetch(PDO::FETCH_ASSOC);
    return $tvStatus['status'];
}

function getIdByInternal($internal)
{
    try {
        // SQL query to fetch ID from users table based on internal
        $sql = "SELECT id FROM users WHERE internal LIKE :internal";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':internal', $internal);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $id = $stmt->fetchColumn();

        // Close the statement
        $stmt->closeCursor();

        return $id;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function format_calling_time_seconds($seconds)
{
    $result = "";

    $years = floor($seconds / (365 * 24 * 60 * 60));
    $seconds -= $years * 365 * 24 * 60 * 60;

    $months = floor($seconds / (30 * 24 * 60 * 60));
    $seconds -= $months * 30 * 24 * 60 * 60;

    $days = floor($seconds / (24 * 60 * 60));
    $seconds -= $days * 24 * 60 * 60;

    $hours = floor($seconds / (60 * 60));
    $seconds -= $hours * 60 * 60;

    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;

    if ($years) {
        $result .= "$years سال ";
    }
    if ($months) {
        $result .= "$months ماه ";
    }
    if ($days) {
        $result .= "$days روز ";
    }
    if ($hours) {
        $result .= "$hours ساعت ";
    }
    if ($minutes) {
        $result .= "$minutes دقیقه ";
    }
    if ($seconds) {
        $result .= "$seconds ثانیه ";
    }

    return trim($result);
}

function getFirstLetters($string)
{
    // Trim the string and remove special characters
    $string = trim(preg_replace('/[^a-zA-Z0-9\sآ-ی]/u', '', $string));

    $words = preg_split('/\s+/u', $string);
    $firstLetters = '';

    if (count($words) === 1) {
        $firstLetters = mb_substr($words[0], 0, 2);
    } else {
        foreach ($words as $word) {
            $firstLetters .= mb_substr($word, 0, 1) . ' ';
        }
    }

    return trim($firstLetters);
}

function givenPrice()
{
    $sql = "SELECT 
            prices.price, prices.partnumber, users.username,customer.id AS customerID, users.id as userID, prices.created_at, customer.name, customer.family
            FROM ((shop.prices 
            INNER JOIN callcenter.customer ON customer.id = prices.customer_id )
            INNER JOIN yadakshop.users ON users.id = prices.user_id)
            ORDER BY prices.created_at DESC LIMIT 45";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    $givenPrices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $givenPrices;
}

// Fetch distinct users from the database
$sqlDistinctUsers = PDO_CONNECTION->prepare("SELECT DISTINCT user FROM callcenter.incoming");
$sqlDistinctUsers->execute();
$users = $sqlDistinctUsers->fetchAll(PDO::FETCH_ASSOC);
$users = array_column($users, 'user');

// Initialize datetime data array for each user
foreach ($users as $user) {
    $datetimeData[$user] = [
        'total' => 0,
        'currentHour' => 0,
        'receivedCall' => 0,
        'answeredCall' => 0,
        'successRate' => 0,
    ];
}

$date = date('Y-m-d');

$sqlTotal = "SELECT * FROM callcenter.incoming WHERE starttime IS NOT NULL AND DATE(time) >= '$date'";

$stmtTotal = PDO_CONNECTION->prepare($sqlTotal);
$stmtTotal->execute();

while ($row = $stmtTotal->fetch(PDO::FETCH_ASSOC)) {
    $user = $row['user'];
    $starttime = strtotime($row['starttime']);
    $endtime = strtotime($row['endtime']);

    // Ensure valid start and end times
    if ($starttime !== false && $endtime !== false) {
        if (!isset($datetimeData[$user])) {
            $datetimeData[$user] = ['total' => 0, 'answeredCall' => 0];
        }

        // Update the user's total time and answered calls
        $datetimeData[$user]['total'] += ($endtime - $starttime);
        $datetimeData[$user]['answeredCall'] += 1;
    }
}

$sqlReceived = "SELECT * FROM callcenter.incoming WHERE DATE(time) >= '$date'";

$resultReceived = PDO_CONNECTION->prepare($sqlReceived);

if ($resultReceived->execute()) {
    foreach ($resultReceived->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $user = $row['user'];

        if (array_key_exists($user, $datetimeData)) {
            $datetimeData[$user]['receivedCall'] += 1;
        }
    }
}

$sqlCurrentHour = "SELECT * FROM callcenter.incoming WHERE starttime IS NOT NULL AND 
                    DATE(time) >= '$date' AND HOUR(starttime) = :currentHour";

$stmtCurrentHour = PDO_CONNECTION->prepare($sqlCurrentHour);
$currentHour = (int)date('G');
$stmtCurrentHour->bindParam(':currentHour', $currentHour, PDO::PARAM_INT);
$stmtCurrentHour->execute();

while ($row = $stmtCurrentHour->fetch(PDO::FETCH_ASSOC)) {
    $user = $row['user'];
    $starttime = strtotime($row['starttime']);
    $endtime = strtotime($row['endtime']);

    // Ensure valid start and end times
    if ($starttime !== false && $endtime !== false) {
        // Initialize the user's data if not already set
        if (!isset($datetimeData[$user])) {
            $datetimeData[$user] = ['total' => 0, 'answeredCall' => 0, 'currentHour' => 0];
        }

        // Update the user's total time for the current hour
        $datetimeData[$user]['currentHour'] += ($endtime - $starttime);
    }
}

// Sort the users based on total call times
uasort($datetimeData, 'compareTotalCallTimes');
uasort($datetimeData, 'compareTotalCallTimes2');

foreach ($datetimeData as &$data) {
    if ($data['receivedCall'] !== 0)
        $data['successRate'] = floor(($data['answeredCall'] * 100) / $data['receivedCall']);
}

function compareTotalCallTimes($a, $b)
{
    if ($a['total'] == $b['total']) {
        return 0;
    }

    return ($a['total'] > $b['total']) ? -1 : 1;
}

function compareTotalCallTimes2($a, $b)
{
    if ($a['currentHour'] == $b['currentHour']) {
        return 0;
    }

    return ($a['currentHour'] > $b['currentHour']) ? -1 : 1;
}

function formatTimeWithUnits($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    $formattedTime = '';
    if ($hours > 0) {
        $formattedTime .= $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ';
    }
    if ($minutes > 0) {
        $formattedTime .= $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ';
    }
    if ($seconds > 0) {
        $formattedTime .= $seconds . ' second' . ($seconds > 1 ? 's' : '');
    }

    return trim($formattedTime);
}

function getIncomingCalls($user)
{
    $sql = "SELECT * FROM callcenter.incoming WHERE user = :user ORDER BY DATE(time) DESC LIMIT 40";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    $incomingCalls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $incomingCalls;
}

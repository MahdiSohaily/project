<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$phone = $_GET['phone'] ?? '000';
$customer = getCustomer($phone);
$bills = [];
$records = [];

if ($customer) {
    $id = $customer['id'];
    $customer_id = $customer['id'];
    $name = $customer['name'];
    $family = $customer['family'];
    $phone = $customer['phone'];
    $vin = $customer['vin'];
    $des = $customer['des'];
    $address = $customer['address'];
    $car = $customer['car'];
    $kind = $customer['kind'];
    $label = $customer['label'];
    $userSelect = $customer['user'];
    $isOld = 1;
    $bills = getBills($customer['id']);
    $records = getRecords($phone);
} else {
    $isOld = 0;
}

function getBills($customerId)
{
    $sql = "SELECT bill.*, customer.name, customer.family,
    users.username FROM factor.bill 
    INNER JOIN callcenter.customer ON customer.id = bill.customer_id
    LEFT JOIN users ON users.id = bill.user_id
    WHERE customer_id = :customerId AND status = 1 ORDER BY created_at DESC";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomer($phone)
{
    $stmt = PDO_CONNECTION->prepare("SELECT * FROM callcenter.customer WHERE phone = :phone");
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getFamilyById($id)
{
    try {
        // SQL query to fetch family from users table based on ID
        $sql = "SELECT family FROM users WHERE id = :id";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        // Get the family value
        $family = $row['family'];

        return $family;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function tagLabelList()
{
    try {
        // SQL query to fetch label data
        $sql = "SELECT id, name, class FROM callcenter.label";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as an associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        // Output options
        foreach ($rows as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $class = $row['class'];
            echo "<option value='" . $id . "' class='$class'>" . $name . "</option>";
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
    }
}

function userLabelList()
{
    try {
        // SQL query to fetch user label data
        $sql = "SELECT id, name, class FROM callcenter.userlabel";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as an associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        // Output options
        foreach ($rows as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $class = $row['class'];
            echo "<option value='" . $id . "' class='$class'>" . $name . "</option>";
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
    }
}

function getIP($user_id)
{
    try {
        // SQL query to fetch IP from users table based on ID
        $sql = "SELECT ip FROM users WHERE id = :id";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $ip = $stmt->fetchColumn();

        // Close the statement
        $stmt->closeCursor();

        return $ip;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function getRecords($phone)
{
    try {
        // SQL query to fetch records
        $sql = "SELECT * FROM callcenter.record WHERE phone LIKE :phone ORDER BY  time DESC LIMIT 300";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':phone', $phone);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as an associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        return $rows;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function getUser($id)
{
    try {
        // SQL query to fetch user data
        $sql = "SELECT * FROM users WHERE id = :id";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        return $row;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function passedTime($time)
{
    // Assuming $time is a valid datetime string
    $now = new DateTime(); // current date time
    $date_time = new DateTime($time); // date time from string
    $interval = $now->diff($date_time); // difference between two date times
    $days = $interval->format('%a'); // difference in days
    $hours = $interval->format('%h'); // difference in hours
    $minutes = $interval->format('%i'); // difference in minutes
    $seconds = $interval->format('%s'); // difference in seconds

    $text = '';

    if ($days) {
        $text .= "$days روز و ";
    }

    if ($hours) {
        $text .= "$hours ساعت ";
    }

    if (!$days && $minutes) {
        $text .= "$minutes دقیقه ";
    }

    if (!$days && !$hours && $seconds) {
        $text .= "$seconds ثانیه ";
    }

    $text .= " قبل";
    return $text;
}

function getIncomingCallReports($phone)
{
    try {
        // SQL query to fetch incoming call reports
        $sql = "SELECT * FROM callcenter.incoming WHERE phone LIKE :phone ORDER BY time DESC LIMIT 50";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        $phone = $phone . '%';

        // Bind parameter
        $stmt->bindParam(':phone', $phone);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as an associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        return $rows;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function timeDef($timeOne, $timeTwo)
{
    $datetime1 = new DateTime($timeOne);
    $datetime2 = new DateTime($timeTwo);
    $interval = $datetime1->diff($datetime2);
    return $interval;
}

function getNameByInternal($internal)
{
    try {
        // SQL query to fetch name and family from users table based on internal
        $sql = "SELECT name, family FROM users WHERE internal = :internal";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':internal', $internal, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the statement
        $stmt->closeCursor();

        // Concatenate name and family
        $name = $row['name'] ?? '';
        $family = $row['family'] ?? '';

        return $name . " " . $family;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}

function format_calling_time(DateInterval $interval)
{
    $result = "";
    if ($interval->y) {
        $result .= $interval->format("%y سال ");
    }
    if ($interval->m) {
        $result .= $interval->format("%m ماه ");
    }
    if ($interval->d) {
        $result .= $interval->format("%d روز ");
    }
    if ($interval->h) {
        $result .= $interval->format("%h ساعت ");
    }
    if ($interval->i) {
        $result .= $interval->format("%i دقیقه ");
    }
    if ($interval->s) {
        $result .= $interval->format("%s ثانیه ");
    }
    $result .= "قبل";
    return $result;
}

function givenPrice($id)
{
    $sql = "SELECT 
        prices.price, prices.partnumber, users.username, users.id as userID, prices.created_at
        FROM ((shop.prices 
        INNER JOIN callcenter.customer ON customer.id = prices.customer_id )
        INNER JOIN yadakshop.users ON users.id = prices.user_id)
        WHERE customer.id = :id ORDER BY prices.created_at DESC LIMIT 20";

    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $givenPrices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $givenPrices;
}

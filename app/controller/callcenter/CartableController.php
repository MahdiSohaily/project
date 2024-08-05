<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$incomingCalls = getIncomingCalls();

global $repeatKeeper;
$statusKeeper = 0;
$n = 0;
$img = '';

function getFactorsCount($id)
{
    $sql = "SELECT COUNT(id) as total FROM callcenter.bill WHERE customer_id = :id AND status=1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch()['total'];
}

function getLastFactorDate($id)
{
    $sql = "SELECT created_at FROM callcenter.bill WHERE customer_id = :id AND status=1 ORDER BY created_at DESC LIMIT 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) > 0)
        return $result[0]['created_at'];
    return '';
}

function getIncomingCalls()
{
    $sql = "SELECT * FROM callcenter.incoming ORDER BY time DESC LIMIT 200";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getIdByInternal($internal)
{
    try {
        // SQL query to fetch ID from users table based on internal
        $sql = "SELECT id FROM users WHERE internal = :internal";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':internal', $internal, PDO::PARAM_STR);

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

function timeDef($timeOne, $timeTwo)
{
    $datetime1 = new DateTime($timeOne);
    $datetime2 = new DateTime($timeTwo);
    $interval = $datetime1->diff($datetime2);
    return $interval;
}

function displayTimePassed($datetimeString)
{
    if ($datetimeString) {

        $now = new DateTime(); // current date time
        $date_time = new DateTime($datetimeString); // date time from string
        $interval = $now->diff($date_time); // difference between two date times

        $years = $interval->format('%y'); // difference in years
        $months = $interval->format('%m'); // difference in months
        $days = $interval->format('%d'); // difference in days

        $text = '';

        if ($years) {
            $text .= "$years سال ";
        }

        if ($months) {
            $text .= "$months ماه ";
        }

        if ($days) {
            $text .= " $days روز ";
        }

        if (empty($text)) {
            return "امروز"; // If the difference is less than a month
        }

        return $text . "قبل";
    }
    return '';
}

function format_interval(DateInterval $interval)
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

function tagLabelShow($x)
{
    if (empty($x)) {
        return;
    }
    $myString = substr($x, 0, -1);
    $myArray = explode(',', $myString);
    try {
        foreach ($myArray as $id) {
            // SQL query to fetch label data based on ID
            $sql = "SELECT name, class FROM callcenter.label WHERE id = :id";

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

            if ($row) {
                $name = $row['name'];
                $class = $row['class'];
                echo "<span class='labeltag $class rounded text-xs'>" . $name . "</span>";
            }
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
    }
}

function userLabelShow($x)
{
    if (empty($x)) {
        return;
    }
    $myString = substr($x, 0, -1);
    $myArray = explode(',', $myString);
    try {
        foreach ($myArray as $id) {
            // SQL query to fetch user label data based on ID
            $sql = "SELECT name, class FROM callcenter.userlabel WHERE id = :id";

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

            if ($row) {
                $name = $row['name'];
                $class = $row['class'];
                echo "<span class='labeltag $class m-1 rounded text-xs'> " . $name . "</span>";
            }
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
    }
}

function createfile($x)
{

    $myfile = fopen("mirror.html", "w") or die("Unable to open file!");
    $txt = $x;
    fwrite($myfile, $txt);

    fclose($myfile);
}

function jalalitime($x)
{
    $date = $x;
    $array = explode(' ', $date);
    list($year, $month, $day) = explode('-', $array[0]);
    list($hour, $minute, $second) = explode(':', $array[1]);
    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    $jalali_time = jdate("H:i", $timestamp, "", "Asia/Tehran", "en");
    return $jalali_time;
}

function jalalidate($x)
{
    $date = $x;
    $array = explode(' ', $date);
    list($year, $month, $day) = explode('-', $array[0]);
    list($hour, $minute, $second) = explode(':', $array[1]);
    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    $jalali_date = jdate("Y/m/d", $timestamp, "", "Asia/Tehran", "en");
    return $jalali_date;
}

function mahak($x)
{
    global $phone;
    try {
        // Extract phone number without the leading character
        $gphone = substr($x, 1);

        // SQL query to search for phone number in 'mahak' table
        $sql = "SELECT name1, name2 FROM callcenter.mahak WHERE mob1 LIKE :gphone OR mob2 LIKE :gphone";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindValue(':gphone', '%' . $gphone . '%', PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Check if there are any results
        if ($stmt->rowCount() > 0) {
            $n = 1;
            // Fetch and display the results
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mname1 = $row['name1'];
                $mname2 = $row['name2'];

                if (strlen($phone) < 5) {
                    break;
                }

                if ($n > 1) {
                    echo "<br>";
                }

                echo $mname1 . " " . $mname2;

                $n++;
            }
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
        echo $e->getMessage();
    }
}

function mahakContact($x)
{
    try {
        // Extract phone number without the leading character
        $gphone = substr($x, 1);

        // SQL query to search for phone number in 'mahak' table
        $sql = "SELECT name1, name2 FROM callcenter.mahak WHERE mob1 LIKE :gphone OR mob2 LIKE :gphone";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindValue(':gphone', '%' . $gphone . '%', PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Check if there are any results
        if ($stmt->rowCount() > 0) {
            $n = 1;
            // Fetch and display the results
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $mname1 = $row['name1'];
                $mname2 = $row['name2'];

                if (strlen($x) < 5) {
                    break;
                }
                if ($n > 1) {
                    echo ("<br>");
                }
                echo $mname1 . " " . $mname2;

                $n++;
            }
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
        echo $e->getMessage();
    }
}

function googleContact($x)
{
    try {
        // Extract phone number without the leading character
        $gphone = substr($x, 1);

        // SQL query to search for phone number in 'google' table
        $sql = "SELECT name1, name2, name3 FROM callcenter.google WHERE mob1 LIKE :gphone OR mob2 LIKE :gphone OR mob3 LIKE :gphone";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindValue(':gphone', '%' . $gphone . '%', PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Check if there are any results
        if ($stmt->rowCount() > 0) {
            $n = 1;
            // Fetch and display the results
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $gname1 = $row['name1'];
                $gname2 = $row['name2'];
                $gname3 = $row['name3'];

                if (strlen($x) < 5) {
                    break;
                }
                if ($n > 1) {
                    echo ("<br>");
                }
                echo $gname1 . " " . $gname2 . " " . $gname3;

                $n++;
            }
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
        echo $e->getMessage();
    }
}

function outgoingContact($phone)
{
    try {
        // SQL query to select records from 'outgoing' table based on phone number
        $sql = "SELECT user FROM callcenter.outgoing WHERE phone LIKE :phone ORDER BY time DESC";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameter
        $stmt->bindValue(':phone', '%' . $phone . '%', PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch the first row
        $row = $stmt->fetchALL(PDO::FETCH_ASSOC);

        // Check if there are any results
        if (count($row) > 0) {

            foreach ($row as $item) {
                $internal = $item['user'];
                $fileSource = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                if (file_exists( $fileSource)) {
                    echo '<img class="w-5 h-5 rounded-md inline ml-1" src="' . $fileSource . '"/>';
                }
            }
        } else {
            // If no records found, display a message
            echo '<div class="flex justify-center items-center gap-2">
                        <p class="text-xs font-semibold text-rose-500">عدم ارتباط مجدد</p>
                        <img class="inline w-4 h-4" src="../../public/icons/close_red.svg" />
                    </div>';
        }
    } catch (PDOException $e) {
        // Handle exception here, if needed
        echo $e->getMessage();
    }
}

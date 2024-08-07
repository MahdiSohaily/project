<?php
$appliedRate = 0;
$applyDate = null;
$additionRate = null;

$applyDateSmall = null;
$additionRateSmall = null;

$rateSpecification  = getDollarRateInfo();

function getDollarRateInfo()
{
    $statement = "SELECT rate, created_at FROM shop.dollarrate WHERE status = 1";
    $stmt = PDO_CONNECTION->prepare($statement);
    $stmt->execute();
    $rate = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rate;
}

function filterCode($message)
{
    if (empty($message)) {
        return '';
    }

    $codes = explode("\n", $message);

    $filteredCodes = array_map(function ($code) {
        $code = preg_replace('/\[[^\]]*\]/', '', $code);
        $parts = preg_split('/[:,]/', $code, 2);
        $rightSide = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $parts[1] ?? ''));
        return !empty($rightSide) ? $rightSide : trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $code));
    }, array_filter($codes, 'trim'));

    $finalCodes = array_filter($filteredCodes, function ($item) {
        $data = explode(" ", $item);
        if (strlen($data[0]) > 4) {
            return $item;
        }
    });

    $finalCodes = array_map(function ($item) {
        $item = explode(' ', $item);
        if (count($item) >= 2) {
            $partOne = $item[0];
            $partTwo = $item[1];
            if (!preg_match('/[a-zA-Z]{4,}/i', $partOne) && !preg_match('/[a-zA-Z]{4,}/i', $partTwo)) {
                return $partOne . $partTwo;
            }
        }
        return $item[0];
    }, $finalCodes);

    $finalCodes = array_filter($finalCodes, function ($item) {
        $consecutiveChars = preg_match('/[a-zA-Z]{4,}/i', $item);
        return !$consecutiveChars;
    });

    return implode("\n", array_map(function ($item) {
        return explode(' ', $item)[0];
    }, $finalCodes)) . "\n";
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

    return 'تاریخ ورود موجود نیست';
}

function convertToPersian($number)
{
    $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $persianNumber = '';

    while ($number > 0) {
        $digit = $number % 10;
        $persianNumber = $persianDigits[$digit] . $persianNumber;
        $number = (int)($number / 10);
    }

    return $persianNumber;
}

function applyDollarRate($price, $priceDate)
{
    $priceDate = date('Y-m-d', strtotime($priceDate));
    $rate = 0;
    foreach ($GLOBALS['rateSpecification'] as $rate) {
        if ($priceDate <= $rate['created_at']) {
            $rate = $rate['rate'];
            break;
        }
    }

    $GLOBALS['appliedRate'] = $rate;
    // Split the input string into words using space as the delimiter
    $words = explode(' ', $price);

    // Iterate through the words and modify numbers with optional forward slashes
    foreach ($words as &$word) {
        // Define a regular expression pattern to match numbers with optional forward slashes
        $pattern = '/(\d+(?:\/\d+)?)/';

        // Check if the word matches the pattern
        if (preg_match($pattern, $word)) {
            // Extract the matched number, removing any forward slashes
            $number = preg_replace('/\//', '', $word);


            if (ctype_digit($number)) {
                // Increase the matched number by 2%
                $modifiedNumber = $number + (($number * $rate) / 100);

                if ($modifiedNumber >= 10) {
                    // Round the number to the nearest multiple of 10
                    $roundedNumber = ceil($modifiedNumber / 10) * 10;
                } else {
                    $roundedNumber = round($modifiedNumber);
                }

                // Replace the word with the modified number
                $word = str_replace($number, $roundedNumber, $word);
            }
        }
    }
    // Reconstruct the modified string by joining the words with spaces
    $modifiedString = implode(' ', $words);

    return $modifiedString;
}

function checkDateIfOkay($applyDate, $priceDate)
{
    $priceDate = date('Y-m-d', strtotime($priceDate));

    foreach ($GLOBALS['rateSpecification'] as $rate) {
        if ($priceDate <= $rate['created_at']) {
            return true;
        }
    }

    return false;
}

function is_registered($partNumber)
{
    // Prepare the SQL statement
    $sql = "SELECT * FROM telegram.goods_for_sell WHERE partNumber = :partNumber";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindValue(':partNumber', $partNumber);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return count($result) > 0;
}

function timeFormatter($date)
{
    $create = date($date);
    $now = new DateTime(); // current date time
    $date_time = new DateTime($create); // date time from string
    $interval = $now->diff($date_time); // difference between two date times
    $days = $interval->format('%a'); // difference in days
    $hours = $interval->format('%h'); // difference in hours
    $minutes = $interval->format('%i'); // difference in minutes
    $seconds = $interval->format('%s'); // difference in seconds

    $text = '';

    if ($days) {
        $text .= " $days روز و ";
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

    return "<p class='text-xs'>$text قبل</p>";
}

function getExistingBrands($stockInfo)
{
    if ($stockInfo) {
        $stockInfo = array_values($stockInfo);

        $stockInfo = array_filter($stockInfo, function ($item) {
            return count($item) > 0;
        });

        $brands = [];
        foreach ($stockInfo as $stock) {
            foreach ($stock as $item) {
                $brands[] = strtoupper($item['brandName']);
            }
        }

        if (in_array('HI Q', $brands)) {
            $brands[] = 'HIQ';
            $brands[] = 'HI';
        }

        if (in_array('MOB', $brands) || in_array('GEN', $brands)) {
            $brands[] = 'MOB';
            $brands[] = 'GEN';
        }

        if (in_array('OEMAX', $brands) || in_array('JYR', $brands) || in_array('RB2', $brands)) {
            $brands[] = 'CHINA';
        }

        if (in_array('YONG', $brands) || in_array('YONG HOO', $brands)) {
            $brands[] = 'KOREA';
        }

        return array_unique($brands); // Remove duplicate brand names
    }
    return [];
}

function getFinalSanitizedPrice($givenPrices, $existing_brands)
{
    $addedBrands = [];
    $filteredPrices = [];

    foreach ($givenPrices as $price) {
        if (empty($filteredPrices) && $price['price'] == 'موجود نیست') {
            $filteredPrices[] = 'موجود نیست';
            break;
        }

        $finalPriceForm = $price['price'];
        if (checkDateIfOkay(null, $price['created_at']) && $price['price'] !== 'موجود نیست') {
            $finalPriceForm = applyDollarRate($finalPriceForm, $price['created_at']);
        }

        $pricesParts = explode('/', $finalPriceForm);
        $pricesParts = array_map('trim', $pricesParts);
        $pricesParts = array_map('strtoupper', $pricesParts);

        foreach ($pricesParts as $part) {
            $spaceIndex = strpos($part, ' ');
            if ($spaceIndex !== false) {
                $priceSubStr = substr($part, 0, $spaceIndex);
                $brandSubStr = substr($part, $spaceIndex + 1); // Adjusted to skip the space
                $brand = trim(explode('(', $brandSubStr)[0]);

                if (!in_array($brand, $addedBrands)) {
                    $addedBrands[] = $brand;
                    if (in_array($brand, $existing_brands)) {
                        if ($finalPriceForm == 'موجود نیست') {
                            continue;
                        }

                        $filteredPrices[] = strtoupper($priceSubStr . ' ' . $brandSubStr);
                    }
                }
            }
        }
        break;
    }

    return implode(" / ", array_unique($filteredPrices)); // Ensure uniqueness in the final result
}

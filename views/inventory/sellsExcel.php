<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';
require '../../vendor/autoload.php'; // Include the Composer autoloader
require '../../utilities/jdf.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();

// SQL query to retrieve data from the database
$sql = "SELECT 
    nisha.partnumber,
    brand.name AS brn,
    qtybank.des,
    exitrecord.des AS exdes,
    exitrecord.qty AS extqty,
    seller.name ,
    exitrecord.customer,
    getter.name AS gtn,
    exitrecord.jamkon,
    exitrecord.exit_time,
    exitrecord.invoice_number,
    exitrecord.invoice_date,
    qtybank.anbarenter,
    qtybank.invoice_number AS qtybank_invoice_number,
    qtybank.invoice_date AS qtybank_invoice_date,
    stock.name AS stn,
    users.username AS usn
    FROM $stock.qtybank
    INNER JOIN nisha ON qtybank.codeid = nisha.id
    INNER JOIN $stock.exitrecord ON qtybank.id = exitrecord.qtyid
    LEFT JOIN seller ON qtybank.seller = seller.id
    LEFT JOIN brand ON qtybank.brand = brand.id
    LEFT JOIN stock ON qtybank.stock_id = stock.id
    LEFT JOIN users ON exitrecord.user = users.id
    LEFT JOIN deliverer ON qtybank.deliverer = deliverer.id
    LEFT JOIN getter ON exitrecord.getter = getter.id
    WHERE exitrecord.is_transfered = 0
    ORDER BY exitrecord.exit_time DESC";

$result = PDO_CONNECTION->prepare($sql);
$result->execute();
$data = $result->fetchAll(PDO::FETCH_ASSOC);

// Set the active sheet to the first sheet
$sheet = $spreadsheet->getActiveSheet();

// Set custom column headers
$customHeaders = [
    'شماره فنی', 'برند', 'توضیحات ورود', 'توضیحات خروج', 'تعداد', 'فروشنده',
    'خریدار', 'تحویل گیرنده', 'جمع کننده',
    'زمان خروج',
    'تاریخ خروج',
    'شماره فاکتور خروج',
    'تاریخ فاکتور خروج',
    'ورود به انبار',
    'شماره فاکتور ورود',
    'تاریخ فاکتور ورود',
    'انبار',
    'کاربر'
];

$col = 1;
foreach ($customHeaders as $header) {
    $sheet->setCellValueByColumnAndRow($col, 1, $header);
    $col++;
}

// Set data from the database query result
$row = 2;
foreach ($data as $row_data) {
    $date = $row_data["exit_time"];
    $array = explode(' ', $date);
    list($year, $month, $day) = explode('-', $array[0]);
    list($hour, $minute, $second) = explode(':', $array[1]);
    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    $soldTime = jdate("H:i", $timestamp, "", "Asia/Tehran", "en");
    $soldDate = jdate("Y/m/d", $timestamp, "", "Asia/Tehran", "en");

    $record = [
        'partnumber' => $row_data["partnumber"] . "",
        'brand' => $row_data["brn"],
        'des' => $row_data["des"],
        'exdes' => $row_data["exdes"],
        'extqty' => $row_data["extqty"],
        'seller' => $row_data["name"],
        'customer' => $row_data["customer"],
        'gtn' => $row_data["gtn"],
        'jamkon' => $row_data["jamkon"],
        'exit_time' => $soldTime,
        'exit_date' => $soldDate,
        'invoice_number' => $row_data["invoice_number"],
        'invoice_date' => $row_data["invoice_date"],
        'anbarenter' => $row_data["anbarenter"],
        'qtybank_invoice_number' => $row_data["qtybank_invoice_number"],
        'qtybank_invoice_date' => $row_data["qtybank_invoice_date"],
        'stn' => $row_data["stn"],
        'usn' => $row_data["usn"]
    ];
    $col = 1;
    foreach ($record as $value) {
        $sheet->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

// Freeze the header row
$sheet->freezePane('A2');

// Set the header for the Excel file with today's date
$timestamp = date('Y-m-d');
$filename = "Khoroj_kala_report_{$timestamp}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Clean the output buffer
ob_clean();

// Create Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

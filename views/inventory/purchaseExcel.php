<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';
require '../../vendor/autoload.php'; // Include the Composer auto loader
require '../../utilities/jdf.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $stock;
// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
// SQL query to retrieve data from the database
$sql = "SELECT
    nisha.partnumber ,
    brand.name as brandname,
    qtybank.des, 
    qtybank.qty, 
    qtybank.pos1, 
    qtybank.pos2,
    seller.name as slavename,
    qtybank.create_time as time,
    qtybank.create_time AS purchase_time,
    deliverer.name as delivername,
    qtybank.invoice,
    qtybank.invoice_number,
    qtybank.invoice_date,
    qtybank.anbarenter,
    stock.name,
    users.username
    FROM $stock.qtybank
    LEFT JOIN nisha ON qtybank.codeid=nisha.id
    LEFT JOIN brand ON qtybank.brand=brand.id
    LEFT JOIN seller ON qtybank.seller=seller.id
    LEFT JOIN deliverer ON qtybank.deliverer=deliverer.id
    LEFT JOIN users ON qtybank.user=users.id
    LEFT JOIN stock ON qtybank.stock_id=stock.id 
    WHERE qtybank.is_transfered = 0
    ORDER BY qtybank.create_time DESC";
$stmt = PDO_CONNECTION->prepare($sql);
$stmt->execute();

$purchaseList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set the active sheet to the first sheet
$sheet = $spreadsheet->getActiveSheet();

// Set custom column headers
$customHeaders = [
    'شماره فنی', 'برند', 'توضیحات', 'تعداد', 'راهرو', 'قفسه', 'فروشنده',
    'زمان ورود', 'تاریخ ورود', 'تحویل دهنده', 'فاکتور', 'شماره فاکتور',
    'تاریخ فاکتور', 'ورود به انبار', 'انبار', 'کاربر'
];

$col = 1;
foreach ($customHeaders as $header) {
    $sheet->setCellValueByColumnAndRow($col, 1, $header);
    $col++;
}

// Set data from the database query result
$row = 2;
foreach ($purchaseList as $row_data) {
    $row_data["partnumber"] =  $row_data["partnumber"] . "";
    $date = $row_data["purchase_time"];
    $array = explode(' ', $date);
    list($year, $month, $day) = explode('-', $array[0]);
    list($hour, $minute, $second) = explode(':', $array[1]);
    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    $jalali_time = jdate("H:i", $timestamp, "", "Asia/Tehran", "en");
    $jalali_date = jdate("Y/m/d", $timestamp, "", "Asia/Tehran", "en");
    $row_data['time'] = $jalali_time; // Corrected field name
    $row_data['purchase_time'] = $jalali_date; // Corrected field name

    $col = 1;
    foreach ($row_data as $value) {
        $sheet->setCellValueExplicitByColumnAndRow($col, $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $col++;
    }
    $row++;
}

// Freeze the header row
$sheet->freezePane('A2');

// Set the column width to auto-size
foreach (range('A', 'P') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Set the header for the Excel file with today's date and time
$timestamp = date('Y-m-d');
$filename = "vorod_kala_report_{$timestamp}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Create Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

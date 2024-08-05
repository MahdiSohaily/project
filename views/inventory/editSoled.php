<?php
$pageTitle = "ویرایش فروش";
$iconUrl = 'purchase.svg';
require_once './components/header.php';
require_once '../../utilities/inventory/InventoryHelpers.php';
require_once '../../utilities/inventory/ExistingHelper.php';
$successfulOperation = false;

if (isset($_GET['record'])) {
    $record_id = $_GET['record'];
    $selected_record = getRecord($record_id);
    $getters = getGetters();
}

if (isset($_POST['selected_record_id'])) {
    $record_id = $_POST['selected_record_id'];
    $selected_record = getRecord($record_id);
    $report = getPurchaseReportById($selected_record['purchase_id']);

    if ($report) {
        $allowedAmount = $report[0]['remaining_qty'] + $selected_record['sold_quantity'];
    } else {
        $allowedAmount = $selected_record['sold_quantity'];
    }

    $successfulOperation = saveChanges($_POST, $allowedAmount);
    $selected_record = getRecord($record_id);
    $getters = getGetters();
}

if (isset($_POST['delete_sold'])) {
    $record_id = $_POST['delete_sold'];

    $successfulOperation = deleteRecord($record_id);
    die("<div class='bg-rose-600 text-sm text-white p-5 rounded text-center font-semibold'>ریکارد مدنظر با موفقیت حذف شد</div>");
}
?>

<body style="padding: 0 !important;">
    <table id="report-table" class="w-full">
        <thead>
            <tr class="bg-gray-800 text-white border-2 border-gray-800">
                <th class="text-xs p-3 font-bold" title="شماره فنی">شماره فنی</th>
                <th class="text-xs p-3 font-bold" title="برند">برند</th>
                <th class="text-xs p-3 font-bold" title="توضیحات ورود">توضیحات و</th>
                <th class="text-xs p-3 font-bold" title="توضیحات خروج">توضیحات خ</th>
                <th class="text-xs p-3 font-bold" title="تعداد">تعداد</th>
                <th class="text-xs p-3 font-bold" title="فروشنده">فروشنده</th>
                <th class="text-xs p-3 font-bold" title="خریدار">خریدار</th>
                <th class="text-xs p-3 font-bold" title="تحویل گیرنده">تحویل گیرنده</th>
                <th class="text-xs p-3 font-bold" title="جمع کننده">جمع کننده</th>
                <th class="text-xs p-3 font-bold" title="زمان خروج">زمان خ</th>
                <th class="text-xs p-3 font-bold" title="تاریخ خروج">تاریخ خ</th>
                <th class="text-xs p-3 font-bold" title="شماره فاکتور خروج">ش ف خروج</th>
                <th class="text-xs p-3 font-bold" title="تاریخ فاکتور خروج">تاریخ ف خ</th>
                <th class="text-xs p-3 font-bold" title="ورود به انبار">ورود به انبار</th>
                <th class="text-xs p-3 font-bold" title="شماره فاکتور ورود">ش ف و</th>
                <th class="text-xs p-3 font-bold" title="تاریخ فاکتور ورود">تاریخ ف و</th>
                <th class="text-xs p-3 font-bold" title="انبار">انبار</th>
                <th class="text-xs p-3 font-bold" title="کاربر">کاربر</th>
            </tr>
        </thead>
        <tbody id="resultBox">
            <?php
            if ($selected_record) :
                $date = $selected_record["sold_time"];
                $array = explode(' ', $date);
                list($year, $month, $day) = explode('-', $array[0]);
                list($hour, $minute, $second) = explode(':', $array[1]);
                $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
                $purchaseTime = jdate("H:i", $timestamp, "", "Asia/Tehran", "en");
                $purchaseDate = jdate("Y/m/d", $timestamp, "", "Asia/Tehran", "en");

            ?>
                <tr class="border-2 border-t-0 border-gray-800 even:bg-sky-100">
                    <td class="p-2 text-center text-lg text-white font-semibold uppercase bg-sky-500"><?= $selected_record["partnumber"] ?></td>
                    <td class="p-2 text-center text-sm text-white <?= getBrandBackground(strtoupper($selected_record["brand_name"])) ?> font-semibold uppercase"><?= $selected_record["brand_name"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record["purchase_description"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record["sold_description"] ?></td>
                    <td class="p-2 text-center text-sm font-semibold"><?= $selected_record["sold_quantity"] ?></td>
                    <td class="p-2 text-sm uppercase text-center font-semibold <?= $selected_record["seller_id"] == 61 ? 'text-white bg-red-600' : 'bg-yellow-500' ?>"><?= $selected_record["seller_name"] ?></td>
                    <td class="p-2 text-center text-sm font-semibold"><?= $selected_record["sold_customer"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record["getter_name"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record["jamkon"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $purchaseTime ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $purchaseDate ?></td>
                    <td class="p-2 text-sm text-red-500 text-center font-semibold">
                        <?= $selected_record["sold_invoice_number"] ?>
                    </td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= strtoupper($selected_record["sold_invoice_date"]) ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold">
                        <?php if ($selected_record["purchase_isEntered"] == 1) : ?>
                            <img class="block mx-auto" src="./assets/icons/tick.svg" alt="TICK ICON">
                        <?php else : ?>
                            <img class="block mx-auto" src="./assets/icons/cross.svg" alt="CROSS ICON">
                        <?php endif; ?>
                    </td>
                    <td class="p-2 text-sm text-red-500 text-center font-semibold"><?= $selected_record['qty_invoice_number'] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record['qty_invoice_date'] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record["stock_name"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $selected_record["username"] ?></td>
                </tr>
            <?php else : ?>
                <tr class="">
                    <td colspan="18" class="text-center bg-rose-400 py-3">
                        <p class="text-white">ریکارد مد نظر شما در سیستم موجود نمی باشد</p>
                    </td>
                </tr>
            <?php die();
            endif; ?>
        </tbody>
    </table>
    <form id="soldForm" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="bg-gray-200 rounded-lg mt-3 p-5 w-full grid grid-cols-4 gap-3">
        <div>
            <input value="<?= $selected_record["sold_id"] ?>" type="hidden" name="selected_record_id">
            <label for="sold_quantity">تعداد</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["sold_quantity"] ?>" min="0" type="number" name="sold_quantity" id="purchase_quantity">

        </div>
        <div>
            <label for="invoice_number_edit">شماره فاکتور</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["sold_invoice_number"] ?>" type="text" name="invoice_number_edit" id="invoice_number_edit">
        </div>
        <div>
            <label for="customer">خریدار</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["customer"] ?>" type="text" name="customer" id="customer">
        </div>
        <div>
            <label for="getter_name">تحویل گیرنده</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" name="getter_name" id="getter_name">
                <?php
                foreach ($getters as $getter) : ?>
                    <option value="<?= $getter['id'] ?>" <?= $selected_record["getter_id"] == $getter['id'] ? 'selected' : '' ?>>
                        <?= $getter['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="invoice_time">زمان فاکتور</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["sold_invoice_date"] ?>" type="text" name="invoice_time_edit" id="invoice_time">
        </div>
        <div>
            <label for="sold_description" class="block mb-2 text-sm font-medium text-gray-900">توضیحات</label>
            <textarea id="sold_description" name="sold_description" rows="4" class="block p-2 mb-2 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"><?= $selected_record["sold_description"] ?></textarea>
        </div>
    </form>
    <div class="flex justify-between py-2">
        <div class="flex gap-3">
            <input form="soldForm" id="submit_form" class="cursor-pointer text-white bg-green-800 rounded text-sm px-5 py-2" type="submit" value="ویرایش">
            <form id="deleteForm" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <input type="hidden" name="delete_sold" value="<?= $selected_record["sold_id"] ?>">
                <input type="button" onclick="askPermission()" class="cursor-pointer text-white bg-rose-800 rounded px-5 py-2 text-sm" value="حذف">
            </form>
        </div>
        <?php if ($successfulOperation) : ?>
            <div class="text-white bg-green-900 rounded px-5 py-2 text-sm">عملیات موفقانه صورت گرفت</div>
        <?php endif; ?>
    </div>
    <script>
        $(function() {
            $("#invoice_time").persianDatepicker({
                formatDate: "YYYY/0M/0D",
            });
        });

        function askPermission() {
            if (confirm("آیا از حذف این ریکارد اطمینان دارید؟")) {
                const deleteForm = document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>

</html>

<?php
function getRecord($record_id)
{
    global $stock;
    try {
        $statement = PDO_CONNECTION->prepare("SELECT 
        qtybank.id AS purchase_id,
        qtybank.des AS purchase_description,
        qtybank.qty AS purchase_quantity,
        qtybank.anbarenter AS purchase_isEntered,
        qtybank.invoice_number AS qty_invoice_number,
        qtybank.invoice_date AS qty_invoice_date,
        nisha.id AS partNumber_id,
        nisha.partnumber,
        users.username AS username,
        seller.name AS seller_name,
        seller.id AS seller_id,
        stock.name AS stock_name,
        brand.name AS brand_name,
        exitrecord.qty AS sold_quantity,
        exitrecord.id AS sold_id,
        exitrecord.customer AS sold_customer,
        exitrecord.des AS sold_description,
        exitrecord.exit_time AS sold_time,
        exitrecord.jamkon,
        exitrecord.invoice_number AS sold_invoice_number,
        exitrecord.invoice_date AS sold_invoice_date,
        getter.id AS getter_id,
        getter.name AS getter_name,
        deliverer.id AS deliverer_id,
        deliverer.name AS deliverer_name,
        exitrecord.customer
        FROM $stock.qtybank
        INNER JOIN nisha ON qtybank.codeid = nisha.id
        INNER JOIN $stock.exitrecord ON qtybank.id = exitrecord.qtyid
        LEFT JOIN seller ON qtybank.seller = seller.id
        LEFT JOIN brand ON qtybank.brand = brand.id
        LEFT JOIN stock ON qtybank.stock_id = stock.id
        LEFT JOIN users ON exitrecord.user = users.id
        LEFT JOIN deliverer ON qtybank.deliverer = deliverer.id
        LEFT JOIN getter ON exitrecord.getter = getter.id
        LEFT JOIN factor.shomarefaktor ON exitrecord.invoice_number = shomarefaktor.shomare
        WHERE exitrecord.id = :record_id");

        $statement->bindParam(':record_id', $record_id);
        $statement->execute();

        $purchaseList = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $purchaseList ? $purchaseList[0] : [];
    } catch (\Throwable $th) {
        throw $th;
    }
}

function saveChanges($data, $allowedAmount)
{
    global $stock;
    $record_id = $data['selected_record_id'];
    $sold_quantity = $data['sold_quantity'];
    $invoice_number_edit = $data['invoice_number_edit'];
    $invoice_time_edit = $data['invoice_time_edit'];
    $sold_description = $data['sold_description'];
    $getter_name = $data['getter_name'];
    $customer = $data['customer'];

    if (intval($sold_quantity) > $allowedAmount) {
        die('<p class="bg-red-500 text-center text-white p-3 rounded ">مقدار وارد شده بیشتر از موجودی مجاز است</p>');
    }
    // Assuming PDO_CONNECTION is a PDO instance
    $statement = PDO_CONNECTION->prepare("UPDATE $stock.exitrecord
        SET qty = :sold_quantity,
        customer = :customer,
        getter = :getter_name,
        invoice_number = :invoice_number_edit,
        des = :sold_description,
        invoice_date = :invoice_time_edit
        WHERE id = :record_id
    ");

    // Bind parameters
    $statement->bindParam(':sold_quantity', $sold_quantity);
    $statement->bindParam(':customer', $customer);
    $statement->bindParam(':getter_name', $getter_name);
    $statement->bindParam(':invoice_number_edit', $invoice_number_edit);
    $statement->bindParam(':sold_description', $sold_description);
    $statement->bindParam(':invoice_time_edit', $invoice_time_edit);
    $statement->bindParam(':record_id', $record_id);

    // Execute the statement
    $statement->execute();

    // Check for success
    if ($statement->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

function deleteRecord($record)
{
    global $stock;
    $statement = PDO_CONNECTION->prepare("DELETE FROM $stock.exitrecord WHERE id = :record_id");
    $statement->bindParam(':record_id', $record);
    $statement->execute();

    if ($statement->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

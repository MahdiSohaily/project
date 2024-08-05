<?php
$pageTitle = "ویرایش خرید";
$iconUrl = 'purchase.svg';
require_once './components/header.php';
require_once '../../utilities/inventory/InventoryHelpers.php';
$successfulOperation = false;

if (isset($_GET['record'])) {
    $record_id = $_GET['record'];
    $selected_record = getRecord($record_id);
    $brands = getBrands();
    $sellers = getSellers();
    $stocks = getStocks();
    $deliverers = getDeliverers();
}

if (isset($_POST['selected_record_id'])) {
    $record_id = $_POST['selected_record_id'];

    $successfulOperation = saveChanges($_POST);
    $selected_record = getRecord($record_id);
    $brands = getBrands();
    $sellers = getSellers();
    $stocks = getStocks();
    $deliverers = getDeliverers();
}

if (isset($_POST['delete_purchase'])) {
    $record_id = $_POST['delete_purchase'];

    $successfulOperation = deleteRecord($record_id);
    die("<div class='bg-rose-600 text-sm text-white p-5 rounded text-center font-semibold'>ریکارد مدنظر با موفقیت حذف شد</div>");
}
?>
<style>
    /* Step 2: Add CSS for the Loading Element */
    #loading {
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 2em;
        color: #000;
    }
</style>

<body>
    <!-- Step 1: Add Loading Element -->
    <div id="loading">لطفاً منتظر بمانید...</div>
    
    <table id="report-table" class="max-w-7xl mx-auto">
        <thead>
            <tr class="bg-gray-800">
                <th class="text-white py-2 px-3 text-sm text-right">شماره فنی</th>
                <th class="text-white py-2 px-3 text-sm text-right">برند</th>
                <th class="text-white py-2 px-3 text-sm text-right">توضیحات</th>
                <th class="text-white py-2 px-3 text-sm text-right">تعداد</th>
                <th class="text-white py-2 px-3 text-sm text-right">راهرو</th>
                <th class="text-white py-2 px-3 text-sm text-right">قفسه</th>
                <th class="text-white py-2 px-3 text-sm text-right">فروشنده</th>
                <th class="text-white py-2 px-3 text-sm text-right">زمان ورود</th>
                <th class="text-white py-2 px-3 text-sm text-right">تاریخ ورود</th>
                <th class="text-white py-2 px-3 text-sm text-right">تحویل دهنده</th>
                <th class="text-white py-2 px-3 text-sm text-right">فاکتور</th>
                <th class="text-white py-2 px-3 text-sm text-right">شماره فاکتور</th>
                <th class="text-white py-2 px-3 text-sm text-right">تاریخ فاکتور</th>
                <th class="text-white py-2 px-3 text-sm text-right">ورود به انبار</th>
                <th class="text-white py-2 px-3 text-sm text-right">انبار</th>
                <th class="text-white py-2 px-3 text-sm text-right">کاربر</th>
            </tr>
        </thead>
        <tbody id="resultBox">
            <?php
            $billItemsCount = 0;
            if ($selected_record) :
                $invoice_number = $selected_record['invoice_number'] ?? 'x';
                $date = $selected_record["purchase_time"];
                $array = explode(' ', $date);
                list($year, $month, $day) = explode('-', $array[0]);
                list($hour, $minute, $second) = explode(':', $array[1]);
                $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
                $jalali_time = jdate("H:i", $timestamp, "", "Asia/Tehran", "en");
                $jalali_date = jdate("Y/m/d", $timestamp, "", "Asia/Tehran", "en");
                $billItemsCount += $selected_record["purchase_quantity"];
            ?>
                <tr class="bg-gray-100">
                    <td class="py-2 px-3 bg-blue-500 text-white uppercase font-semibold"><?= $selected_record["partnumber"] ?></td>
                    <td class="py-2 px-3"><?= $selected_record["brand_name"] ?></td>
                    <td class="py-2 px-3"><?= $selected_record["purchase_description"] ?></td>
                    <td class="py-2 px-3"><?= $selected_record["purchase_quantity"] ?></td>
                    <td class="py-2 px-3"><?= $selected_record["purchase_position1"] ?></td>
                    <td class="py-2 px-3"><?= $selected_record["purchase_position2"] ?></td>
                    <td class="py-2 px-3 bg-yellow-500"><?= $selected_record["seller_name"] ?></td>
                    <td class="py-2 px-3 text-xs"><?= $jalali_time ?></td>
                    <td class="py-2 px-3 text-xs"><?= $jalali_date ?></td>
                    <td class="py-2 px-3 text-xs"><?= $selected_record["deliverer_name"] ?></td>
                    <td class="py-2 px-3">
                        <?php if ($selected_record["purchase_hasBill"] == 1) : ?>
                            <img class="block mx-auto" src="./assets/icons/tick.svg" alt="TICK ICON">
                        <?php else : ?>
                            <img class="block mx-auto" src="./assets/icons/cross.svg" alt="CROSS ICON">
                        <?php endif; ?>
                    </td>
                    <td class="py-2 px-3 text-rose-500 text-sm font-semibold"><?= $selected_record["invoice_number"] ?></td>
                    <td class="py-2 px-3 text-xs"><?= substr($selected_record["invoice_date"], 5) ?></td>
                    <td class="py-2 px-3">
                        <?php if ($selected_record["purchase_isEntered"] == 1) : ?>
                            <img class="block mx-auto" src="./assets/icons/tick.svg" alt="TICK ICON">
                        <?php else : ?>
                            <img class="block mx-auto" src="./assets/icons/cross.svg" alt="CROSS ICON">
                        <?php endif; ?>
                    </td>
                    <td class="py-2 px-3 text-xs"><?= $selected_record["stock_name"] ?></td>
                    <td class="py-2 px-3 text-xs"><?= $selected_record["username"] ?></td>
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

    <form id="edit" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="bg-gray-200 rounded-lg mt-3 p-5 max-w-7xl mx-auto grid grid-cols-4 gap-3">
        <div>
            <input value="<?= $selected_record["purchase_id"] ?>" type="hidden" name="selected_record_id">
            <label for="purchase_quantity">تعداد</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["purchase_quantity"] ?>" min="0" type="number" name="purchase_quantity" id="purchase_quantity">

        </div>
        <div>
            <label for="purchase_position1">راهرو</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["purchase_position1"] ?>" onkeydown="upperCaseF(this)" type="text" name="purchase_position1" id="purchase_position1">

        </div>
        <div>
            <label for="purchase_position2">قفسه</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["purchase_position2"] ?>" onkeydown="upperCaseF(this)" type="text" name="purchase_position2" id="purchase_position2">
        </div>
        <div>
            <label for="invoice_number_edit">شماره فاکتور</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["invoice_number"] ?>" type="text" name="invoice_number_edit" id="invoice_number_edit">
        </div>
        <div>
            <label for="invoice_time">زمان فاکتور</label>
            <input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" value="<?= $selected_record["invoice_date"] ?>" type="text" name="invoice_time_edit" id="invoice_time">
        </div>
        <fieldset class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2">
            <legend>آیا فاکتور دارد ؟</legend>
            <label for="purchase_hasBill_false">خیر</label>
            <input class="cursor-pointer" type="radio" name="purchase_hasBill" id="purchase_hasBill_false" value="0" <?= $selected_record["purchase_hasBill"] == 0 ? 'checked' : '' ?>>
            <label class="mr-5" for="purchase_hasBill_false">بله</label>
            <input class="cursor-pointer" type="radio" name="purchase_hasBill" id="purchase_hasBill_true" value="1" <?= $selected_record["purchase_hasBill"] == 1 ? 'checked' : '' ?>>
        </fieldset>
        <fieldset class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2">
            <legend>آیا وارد انبار شده ؟</legend>
            <label for="purchase_isEntered_false">خیر</label>
            <input class="cursor-pointer" type="radio" name="purchase_isEntered" id="purchase_isEntered_false" value="0" <?= $selected_record["purchase_isEntered"] == 0 ? 'checked' : '' ?>>

            <label class="mr-5" for="purchase_isEntered_true">بله</label>
            <input class="cursor-pointer" type="radio" name="purchase_isEntered" id="purchase_isEntered_true" value="1" <?= $selected_record["purchase_isEntered"] == 1 ? 'checked' : '' ?>>
        </fieldset>
        <div>
            <label for="brand_edit">اصالت</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" name="brand_edit" id="brand_edit">
                <?php foreach ($brands as $brand) : ?>
                    <option <?= $selected_record["brand_id"] == $brand['id'] ? 'selected' : '' ?> value="<?= $brand['id'] ?>"> <?= $brand['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="seller_edit">فروشنده</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" name="seller_edit" id="seller_edit">
                <?php
                foreach ($sellers as $seller) : ?>
                    <option title="<?= $seller['latinName'] ?>" value="<?= $seller['id'] ?>" <?= $selected_record["seller_id"] == $seller['id'] ? 'selected' : '' ?>>
                        <?= $seller['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="stock_edit">انبار</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" name="stock_edit" id="stock_edit">
                <?php
                foreach ($stocks as $stock) : ?>
                    <option value="<?= $stock['id'] ?>" <?= $selected_record["stock_id"] == $stock['id'] ? 'selected' : '' ?>>
                        <?= $stock['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="deliverer_edit">تحویل دهنده</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm block w-full p-2 mb-2" name="deliverer_edit" id="deliverer_edit">
                <?php
                foreach ($deliverers as $deliverer) : ?>
                    <option value="<?= $deliverer['id'] ?>" <?= $selected_record["delivery_id"] == $deliverer['id'] ? 'selected' : '' ?>>
                        <?= $deliverer['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="purchase_description" class="block mb-2 text-sm font-medium text-gray-900">توضیحات</label>
            <textarea id="purchase_description" name="purchase_description" rows="4" class="block p-2 mb-2 w-full text-sm text-gray-900 bg-gray-50 border border-gray-300"><?= $selected_record["purchase_description"] ?></textarea>
        </div>
    </form>
    <div class="py-2 max-w-7xl mx-auto">
        <div class="flex justify-between">
            <div class="flex gap-3">
                <input form="edit" id="submit_form" class="cursor-pointer text-white bg-green-800 rounded px-5 py-2 text-sm" type="submit" value="ویرایش">
                <form id="deleteForm" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="delete_purchase" value="<?= $selected_record["purchase_id"] ?>">
                    <input type="button" onclick="askPermission()" class="cursor-pointer text-white bg-rose-800 rounded px-5 py-2 text-sm" value="حذف">
                </form>
            </div>
            <?php if ($successfulOperation) : ?>
                <div class="text-white bg-green-900 rounded px-5 py-2 text-sm" class="error">عملیات موفقانه صورت گرفت</div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        $(function() {
            $("#invoice_time").persianDatepicker({
                formatDate: "YYYY/0M/0D",
            });
            $("#exit_time").persianDatepicker({
                formatDate: "YYYY/0M/0D",
            });
        });

        function askPermission() {
            if (confirm("آیا از حذف این ریکارد اطمینان دارید ؟")) {
                document.getElementById('deleteForm').submit();
            }
        }
        // Step 3: Use JavaScript to Show/Hide the Loading Element
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('loading').style.display = 'none';
        });
    </script>
</body>

</html>

<?php
function getRecord($record_id)
{
    global $stock;
    try {
        $statement = PDO_CONNECTION->prepare("SELECT qtybank.id AS purchase_id,
            qtybank.des As purchase_description,
            qtybank.qty AS purchase_quantity,
            qtybank.pos1 AS purchase_position1,
            qtybank.pos2 AS purchase_position2,
            qtybank.create_time AS purchase_time,
            qtybank.anbarenter AS purchase_isEntered,
            qtybank.invoice AS purchase_hasBill,
            qtybank.invoice_number,
            qtybank.invoice_date,
            nisha.partnumber,
            nisha.price AS good_price,
            seller.id AS seller_id,
            seller.name AS seller_name,
            brand.id AS brand_id,
            brand.name AS brand_name,
            deliverer.id AS delivery_id,
            deliverer.name AS deliverer_name,
            users.username AS username,
            stock.id AS stock_id,
            stock.name AS stock_name
            FROM $stock.qtybank
            INNER JOIN nisha ON qtybank.codeid = nisha.id
            INNER JOIN brand ON qtybank.brand = brand.id
            LEFT JOIN seller ON qtybank.seller = seller.id
            LEFT JOIN deliverer ON qtybank.deliverer = deliverer.id
            LEFT JOIN users ON qtybank.user = users.id
            LEFT JOIN stock ON qtybank.stock_id = stock.id
            WHERE qtybank.id = :record_id
            ORDER BY qtybank.create_time DESC");

        $statement->bindParam(':record_id', $record_id);
        $statement->execute();

        $purchaseList = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $purchaseList ? $purchaseList[0] : [];
    } catch (\Throwable $th) {
        echo "Error: " . $th->getMessage;
        die('ریکارد مد نظر شما در سیستم موجود نمی باشد.');
    }
}

function saveChanges($data)
{
    global $stock;
    $record_id = $data['selected_record_id'];
    $purchase_quantity = $data['purchase_quantity'];
    $purchase_position1 = $data['purchase_position1'];
    $purchase_position2 = $data['purchase_position2'];
    $invoice_number_edit = $data['invoice_number_edit'];
    $invoice_time_edit = $data['invoice_time_edit'];
    $purchase_hasBill = $data['purchase_hasBill'];
    $purchase_isEntered = $data['purchase_isEntered'];
    $brand_edit = $data['brand_edit'];
    $seller_edit = $data['seller_edit'];
    $stock_edit = $data['stock_edit'];
    $deliverer_edit = $data['deliverer_edit'];
    $purchase_description = $data['purchase_description'];

    // Assuming PDO_CONNECTION is a PDO instance
    $statement = PDO_CONNECTION->prepare("UPDATE $stock.qtybank
        SET brand = :brand,
        des = :purchase_description,
        qty = :purchase_quantity,
        pos1 = :purchase_position1,
        pos2 = :purchase_position2,
        seller = :seller_edit,
        deliverer = :deliverer_edit,
        invoice = :purchase_isEntered,
        anbarenter = :purchase_isEntered,
        invoice_number = :invoice_number_edit,
        stock_id = :stock_edit,
        invoice_date = :invoice_time_edit
        WHERE id = :record_id
    ");

    // Bind parameters
    $statement->bindParam(':brand', $brand_edit);
    $statement->bindParam(':purchase_description', $purchase_description);
    $statement->bindParam(':purchase_quantity', $purchase_quantity);
    $statement->bindParam(':purchase_position1', $purchase_position1);
    $statement->bindParam(':purchase_position2', $purchase_position2);
    $statement->bindParam(':seller_edit', $seller_edit);
    $statement->bindParam(':deliverer_edit', $deliverer_edit);
    $statement->bindParam(':purchase_isEntered', $purchase_isEntered);
    $statement->bindParam(':invoice_number_edit', $invoice_number_edit);
    $statement->bindParam(':stock_edit', $stock_edit);
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
    $statement = PDO_CONNECTION->prepare("DELETE FROM $stock.qtybank WHERE id = :record_id");
    $statement->bindParam(':record_id', $record);
    $statement->execute();

    if ($statement->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

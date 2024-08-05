<style>
    #editModal {
        display: none;
    }

    @media print {
        * {
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12px !important;
            background-color: transparent !important;
            color: black !important;
            font-weight: normal !important;
        }

        #main_nav,
        #parent,
        .operation {
            display: none !important;
        }

        table {
            width: 100% !important;
        }

        body {
            padding: 0 !important;
            margin: 0 !important;
        }

        th {
            background-color: lightgray !important;
        }

        td,
        th {
            padding: 5px !important;
            font-size: 12px !important;
        }

        .total {
            padding-block: 10px !important;
        }

    }
</style>
<div class="bg-gray-200 rounded-lg p-3 shadow mb-5">
    <form class="grid grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-3" id="parent" method="post" onsubmit="event.preventDefault();" autocomplete="off">
        <input class="border-2 text-sm p-2 uppercase" type="text" onkeyup="convertToEnglish(this)" name="partNumber" id="partNumber" placeholder="کد فنی">

        <select class="border-2 text-sm p-2" name="seller" id="seller">
            <option selected="true" disabled="disabled">انتخاب فروشنده</option>
            <?php
            foreach (getSellers() as $seller) : ?>
                <option value='<?= $seller["id"] ?>'><?= $seller["name"] ?></option>
            <?php endforeach; ?>
        </select>
        <select class="border-2 text-sm p-2" name="brand" id="brand">
            <option selected="true" disabled="disabled">انتخاب برند جنس</option>
            <?php foreach (getBrands() as $brand) : ?>
                <option value='<?= $brand["id"] ?>'><?= $brand["name"] ?></option>
            <?php endforeach; ?>
        </select>
        <input class="border-2 text-sm p-2" type="text" name="pos2" id="pos2" placeholder="قفسه">

        <input class="border-2 text-sm p-2" onkeydown="upperCaseF(this)" type="text" name="pos1" id="pos1" placeholder="راهرو">
        <select class="border-2 text-sm p-2 placeholder:text-gray-600" name="stock" id="stock">
            <option selected="true" disabled="disabled" class="text-gray-100">انتخاب انبار</option>
            <?php foreach (getStocks() as $stock) : ?>
                <option value='<?= $stock["id"] ?>'><?= $stock["name"] ?></option>
            <?php endforeach; ?>
        </select>
        <select class="border-2 text-sm p-2" name="user" id="user">
            <option selected="true" disabled="disabled">انتخاب کاربر</option>
            <?php foreach (getUsers() as $user) : ?>
                <option value='<?= $user["id"] ?>'><?= $user["username"] ?></option>
            <?php endforeach; ?>
        </select>
        <input class="border-2 text-sm p-2" type="text" onkeyup="convertToEnglish(this)" name="invoice_number" id="invoice_number" placeholder="شماره فاکتور">
        <input class="w-full border-2 text-sm p-2" type="text" name="invoice_time" id="invoice_time" placeholder="شروع">
        <input class="border-2 text-sm p-2" type="text" name="exit_time" id="exit_time" placeholder="ختم">
        <!-- <div></div>
        <div></div>
        <div></div>
        <div>
            <ul class="flex gap-5">
                <li>
                    <label class="text-sm text-neutral-500 font-semibold" for="purchase">
                        <input type="radio" class="checked:bg-rose-500" value="purchase" name="dateType" id="purchase">
                        ورود
                    </label>
                </li>
                <li>
                    <label class="text-sm text-neutral-500 font-semibold" for="sells">
                        <input type="radio" class="checked:bg-rose-500" value="sells" name="dateType" id="sells">
                        خروج
                    </label>
                </li>
                <li>
                    <label class="text-sm text-neutral-500 font-semibold" for="factor">
                        <input checked type="radio" class="checked:bg-rose-500" value="factor" name="dateType" id="factor">
                        فاکتور
                    </label>
                </li>
            </ul>
        </div> -->
    </form>
    <div class="flex flex-wrap gap-2 my-2">
        <button form="parent" class="rounded text-white text-xs px-3 py-2 w-24 bg-sky-500" onclick="filterReport()" type="submit">
            فیلتر
        </button>
        <button class="rounded text-white text-xs px-3 py-2 w-24 bg-rose-500" onclick="clearFilter()">
            <i style="padding-inline: 5px;" class="fa fa-trash" aria-hidden="true"></i>
            حذف فیلتر
        </button>
        <button onclick="exportToExcel('purchase')" class="rounded text-white text-xs px-3 py-2 w-24 bg-green-500">
            <i style="padding-inline: 5px;" class="fas fa-file-excel"></i>
            اکسل
        </button>
        <a href="./purchaseExcel.php" class="rounded text-white text-xs px-3 py-2 w-24 bg-green-500">
            <i style="padding-inline: 5px;" class="fas fa-file-excel"></i>
            اکسل جدید
        </a>
        <button class="rounded text-white text-xs px-3 py-2 w-24 bg-blue-500" onclick="window.print()">
            <i style="padding-inline: 5px;" class="fas fa-print"></i>
            پرینت
        </button>
    </div>
</div>
<script>
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.keyCode === 13) {
            filterReport();
        }
    });
</script>
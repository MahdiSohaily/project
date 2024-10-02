<?php
$pageTitle = "قیمت دستوری";
$iconUrl = 'ordered.png';
require_once './components/header.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
?>
<div class="max-w-2xl mx-auto py-14 sm:px-6 lg:px-8 bg-gray-200 rounded-lg shadow-s mt-32">
    <form target="_blank" action="./orderedPrice.php" method="post">
        <input type="text" name="givenPrice" value="givenPrice" id="form" hidden>
        <input type="text" name="user" value="<?= $_SESSION["id"] ?>" hidden>
        <input type="text" name="customer" value="1" id="target_customer" hidden>
        <div class="">
            <!-- Korea section -->
            <div class="col-span-6 sm:col-span-4">
                <label for="code" class="block text-lg font-semibold text-gray-900">
                    کدهای مدنظر
                </label>
                <textarea onchange="filterCode(this)" rows="9" id="code" name="code" required class="border-2 border-gray-300 focus:border-gray-500 p-3 outline-none  text-sm mt-1 shadow-sm block w-full uppercase" style="direction: ltr !important;" placeholder="لطفا کد های مود نظر خود را در خط های مجزا قرار دهید"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-between py-3 text-right sm:rounded-bl-md sm:rounded-br-md">
            <button type="submit" formaction="../factor/createPreCompleteBill.php" class="inline-flex items-center px-5 py-3 bg-gray-200 shadow font-semibold text-xs text-black hover:bg-gray-700 hover:text-white text-md focus:bg-gray-700 active:bg-gray-900 focus:outline-none"> ایجاد فاکتور
            </button>
            <button type="submit" class="inline-flex items-center px-5 py-3 bg-gray-800 font-semibold text-xs text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none"> جستجو
            </button>
        </div>
    </form>
</div>
<?php
require_once './components/footer.php';

<?php
$pageTitle = "داشبورد";
$iconUrl = 'logo.jpg';
require_once './components/header.php';
require_once '../../app/controller/inventory/InventoryDashboardController.php';
require_once '../../app/controller/components/DashboardCardsController.php';
require_once '../../layouts/inventory/nav.php';
require_once '../../layouts/inventory/sidebar.php';
$dateTime = jdate('Y-m-d'); ?>

<!-- ------------------------------------------------ Dashboard card section ------------------------------------------------------>
<section class="mx-auto px-5 pb-5 bg-gray-100">
    <?php include_once '../components/dashboardCards.php'; ?>
</section>

<!-- ---------------------------------------------- Download Stock Report Section ------------------------------------------------->

<!-- <section class="mx-auto px-5 pb-5 bg-gray-100">
    <div class="bg-white rounded-lg hover:shadow-lg p-5">
        <h2 class="text-xl font-semibold">دانلود راپور سال مالی</h2>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
            <select class="p-2 border-2 border-gray-300 outline-none focus:border-gray-600" name="year" id="year">
                <?php
                foreach (getStocks() as $stock) : ?>
                    <option value="<?= $stock['database_name']; ?>"><?= str_replace('stock_', '', $stock['database_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select class="p-2 border-2 border-gray-300 outline-none focus:border-gray-600" name="type" id="type">
                <option value="existing">گزارش موجودی</option>
                <option value="purchase">گزارش ورود</option>
                <option value="sell">گزارش خروج</option>
            </select>
            <button type="submit" class="bg-sky-600 text-white border-none">دانلود</button>
        </form>
    </div>
</section> -->

<!-- ---------------------------------------------- Dashboard users and calender -------------------------------------------------->
<section class="mx-auto rtl bg-gray-100 mb-5">
    <div class="grid grid-cols-1 lg:grid-cols-2 px-5 gap-5">
        <?php
        include_once '../components/latestFactors.php';
        include_once '../components/calender.php'; ?>
    </div>
</section>

<?php
require_once './components/footer.php';
?>
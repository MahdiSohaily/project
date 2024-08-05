<div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">مجموع کاربران</span>
                    <span class="text-lg font-semibold"><?= $totalUsers ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('../../public/icons/user.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="<?= ($_SESSION['username'] == 'niayesh') ? '../callcenter/usersManagement.php' : '#' ?>" class="text-blue-500 underline">مدیریت کاربران</a>
            </div>
        </div>
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">مجموع فاکتور های ثبت شده</span>
                    <span class="text-lg font-semibold"><?= $totalFactors ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('../../public/icons/invoice.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="../callcenter/factor.php" class="text-blue-500 underline">ثبت فاکتور جدید</a>
            </div>
        </div>
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">مجموع اقلام وارد شده</span>
                    <span class="text-lg font-semibold"><?= $totalGoods ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('../../public/icons/receive.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="./purchaseReport.php?interval=3" class="text-blue-500 underline">گزارش اقلام وارده</a>
            </div>
        </div>
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">مجموع اقلام به فروش رسیده</span>
                    <span class="text-lg font-semibold"><?= $totalSold ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('../../public/icons/deliver.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="./sellsReport.php?interval=3" class="text-blue-500 underline">گزارش اقلام خارجه</a>
            </div>
        </div>
    </div>
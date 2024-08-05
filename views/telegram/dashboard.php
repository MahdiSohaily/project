<?php
$pageTitle = "مدیریت تلگرام";
$iconUrl = 'telegram.svg';
require_once './components/header.php';
require_once '../../app/controller/telegram/DashboardController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
?>
<style>
    body {
        background-color: #F3F4F6 !important;
    }

    .bg-gradient::after {
        background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), rgba(0, 0, 0, 1), transparent 20%);
    }
</style>

<!-- ------------------------------------------------ Dashboard card section ---------------------------------------------------- -->
<section class="mx-auto px-5 pb-5 bg-gray-100">
    <div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">ارسال پیام خودکار</span>
                    <?php if ($status) : ?>
                        <span class="text-xs text-gray-700 font-semibold">
                            درحال ارسال پیام
                        </span>
                    <?php else : ?>
                        <span class="text-xs text-gray-700 font-semibold">
                            ارسال پیام متوقف شده
                        </span>
                    <?php endif; ?>

                </div>
                <?php if ($status) : ?>
                    <img onclick="toggleStatus(0)" title="توقف ارسال پیام خودکار" class="cursor-pointer" src="./assets/img/powerOff.svg" alt="power off icon">
                <?php else : ?>
                    <img onclick="toggleStatus(1)" title="شروع ارسال پیام خودکار" class="cursor-pointer" src="./assets/img/powerOn.svg" alt="power On icon">
                <?php endif; ?>
            </div>
            <div>
                <span class="text-xs text-gray-600">برای توقف و از سر گیری ارسال پیام خودکار
                    روی آیکن کلیک کنید.</span>
            </div>
        </div>
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">مجموع کد های ثبت شده</span>
                    <span class="text-lg font-semibold"><?= $totalRegisteredGoods ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('./assets/icons/qrCode.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="./registeredGoods.php" class="text-blue-500 underline">مشاهده همه</a>
            </div>
        </div>
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">مخاطبین</span>
                    <span class="text-lg font-semibold"><?= $totalContacts ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('./assets/icons/contact.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="./contacts.php" class="text-blue-500 underline">مشاهده همه</a>
            </div>
        </div>
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-800 font-semibold">اقلام درخواستی امروز</span>
                    <span class="text-lg font-semibold"><?= $totalRequests ?></span>
                </div>
                <img class="rounded-md w-16 h-16" src="<?= ('./assets/icons/alert.svg') ?>" alt="">
            </div>
            <div>
                <span class="inline-block px-2 text-sm text-white bg-green-500 ml-1 rounded"><?= rand(1, 100) ?>%</span>
                <a href="./requests.php?type=all" class="text-blue-500 underline">مشاهده همه</a>
            </div>
        </div>
    </div>
</section>

<!-- ------------------- DASHBOARD MESSAGES REPORTS SECTION ----------------------------- -->
<section class="mx-auto rtl bg-gray-100 mb-5">
    <div class="grid grid-cols-1 lg:grid-cols-3 px-5 gap-5">
        <div class="bg-white rounded-lg overflow-hidden border border-gray-800 shadow-md hover:shadow-xl">
            <div class="flex items-center justify-between bg-gray-800 p-5">
                <h1 class="text-lg font-semibold text-white">
                    آمار درخواست های یک ساعت اخیر</h1>
                <a href="./requests.php?type=hour" class="text-sm text-blue-500">مشاهده همه</a>
            </div>
            <div class="shadow-md sm:rounded-lg w-full h-full">
                <table class="w-full text-sm text-left rtl:text-center text-gray-800">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                        <tr>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                شماره
                            </th>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                کد درخواستی
                            </th>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                دفعات درخواست
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($lastHourMostRequested as $index => $request) : ?>
                            <tr class="border-b/10 hover:bg-gray-50 even:bg-gray-100">
                                <th class="px-6 py-3  font-semibold text-gray-800 text-center">
                                    <?= ++$index; ?>
                                </th>
                                <th class="px-6 py-3  font-semibold text-gray-800 text-center">
                                    <?= $request['request'] ?>
                                </th>
                                <td class="px-6 py-3  font-semibold text-center text-gray-800">
                                    <?= $request['quantity'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden border border-gray-800 shadow-md hover:shadow-xl">
            <div class="flex items-center justify-between bg-gray-800 p-5">
                <h1 class="text-lg font-semibold text-white">آمار درخواست های امروز
                </h1>
                <a href="./requests.php?type=today" class="text-sm text-blue-500">مشاهده همه</a>
            </div>
            <div class="shadow-md sm:rounded-lg w-full h-full">
                <table class="w-full text-sm text-left rtl:text-center text-gray-800">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                        <tr>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                شماره
                            </th>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                کد درخواستی
                            </th>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                دفعات درخواست
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($todayMostRequested as $index => $request) : ?>
                            <tr class="border-b/10 hover:bg-gray-50 even:bg-gray-100">
                                <th class="px-6 py-3  font-semibold text-gray-800 text-center">
                                    <?= ++$index; ?>
                                </th>
                                <th class="px-6 py-3  font-semibold text-gray-800 text-center">
                                    <?= $request['request'] ?>
                                </th>
                                <td class="px-6 py-3  font-semibold text-center text-gray-800">
                                    <?= $request['quantity'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden border border-gray-800 shadow-md hover:shadow-xl">
            <div class="flex items-center justify-between bg-gray-800 p-5">
                <h1 class="text-lg font-semibold text-white">آمار درخواست های کلی</h1>
                <a href="./requests.php?type=all" class="text-sm text-blue-500">مشاهده همه</a>
            </div>
            <div class="shadow-md sm:rounded-lg w-full h-full">
                <table class="w-full text-sm text-left rtl:text-center text-gray-800">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                        <tr>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                شماره
                            </th>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                کد درخواستی
                            </th>
                            <th scope="col" class="font-semibold text-sm text-center text-gray-800 px-6 py-3">
                                دفعات درخواست
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allTimeMostRequested as $index => $request) : ?>
                            <tr class="border-b/10 hover:bg-gray-50 even:bg-gray-100">
                                <th class="px-6 py-3  font-semibold text-gray-800 text-center">
                                    <?= ++$index; ?>
                                </th>
                                <th class="px-6 py-3  font-semibold text-gray-800 text-center">
                                    <?= $request['request'] ?>
                                </th>
                                <td class="px-6 py-3  font-semibold text-center text-gray-800">
                                    <?= $request['quantity'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script>
    const contactApi = "../../app/api/telegram/ContactsApi.php";

    function toggleStatus(status) {
        var params = new URLSearchParams();
        params.append('toggleStatus', 'toggleStatus');
        params.append('status', status);

        axios
            .post(contactApi, params)
            .then(function(response) {
                const data = response.data;
                window.location.reload();
            })
            .catch(function(error) {
                console.log(error);
            });
    }
</script>
<?php
require_once './components/footer.php';
?>
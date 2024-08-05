<?php
$pageTitle = "تاریخچه";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../app/controller/callcenter/PricesHistoryController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
require_once '../../utilities/callcenter/DollarRateHelper.php';
?>
<!-- START NEWLY ADDED SECTION BY MAHDI REZAEI -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-2" id="resultBox">
    <div class="mb-5">
        <h2 class="text-xl font-semibold py-2">آخرین قیمت های داده شده</h2>
        <table class="min-w-full text-left text-sm bg-white custom-table mb-2 p-3">
            <thead class="font-semibold bg-gray-800">
                <tr>
                    <th scope="col" class="px-3 py-2 text-white text-right ">
                        مشتری
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-center ">
                        قیمت
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-center ">
                        کد فنی
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-right">
                        کاربر
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-right ">
                        زمان
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($givenPrice) > 0) :
                    foreach ($givenPrice as $price) : ?>
                        <tr class=" min-w-full mb-1 ?> odd:bg-gray-200">
                            <td class=" px-1">
                                <p class="text-indigo-600 px-1 py-1">
                                    <a class="flex items-center" href="./main.php?phone=<?= $price['phone'] ?>">
                                        <i class="small material-icons px-2">attachment</i>
                                        <?= $price['name'] . ' ' . $price['family'] ?>
                                    </a>
                                </p>
                            </td>
                            <td style="direction: ltr !important;" class=" px-1">
                                <p  style="direction: ltr !important;" class="text-gray-700 px-2 py-1 text-left">
                                    <?= $price['price'] === null ? 'ندارد' : $price['price']  ?>
                                </p>
                            </td>
                            <td class=" px-1 cursor-pointer">
                                <form target="_blank" action="orderedPrice.php" method="post">
                                    <input type="text" name="givenPrice" value="givenPrice" id="form" hidden>
                                    <input type="text" name="user" value="<?= $_SESSION["id"] ?>" hidden>
                                    <input type="text" name="customer" value="<?= $price['customerID'] ?>" id="target_customer" hidden>
                                    <input type="text" name="code" value=" <?= $price['partnumber']; ?>" hidden>
                                    <input class="text-indigo-600 cursor-pointer" type="submit" value=" <?= $price['partnumber']; ?>">
                                </form>
                            </td>
                            <td class="">
                                <img title="<?= $price['username'] ?>" class="w-8 h-8 rounded-full mx-auto mt-1" src="../../public/userimg/<?= $price['userID'] ?>.jpg" alt="userimage" />
                            </td>
                            <td>
                                <p class="text-right text-gray-700 px-2 py-1 text-xs">
                                    <?= displayTimePassed($price['created_at']) ?>
                                </p>
                            </td>
                        </tr>
                    <?php endforeach;
                else : ?>
                    <tr class="">
                        <td colspan="5" scope="col" class="not-exist">
                            موردی برای نمایش وجود ندارد !!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-5">
        <h2 class="text-xl font-semibold py-2">آخرین استعلام ها</h2>
        <table class="min-w-full text-sm bg-white custom-table mb-2 p-3">
            <thead class="bg-gray-800">
                <tr>
                    <th scope="col" class="px-3 py-2 text-white text-right">
                        مشتری
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-right">
                        تلفن
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-right">
                        اطلاعات استعلام
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-right">
                        پین
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-center">
                        کاربر
                    </th>
                    <th scope="col" class="px-3 py-2 text-white text-right">
                        زمان
                    </th>
                </tr>
            </thead>
            <?php
            if ($pinedAskedPrice) {
                foreach ($pinedAskedPrice as $record) {
                    $recordID = $record['recordID'];
                    $time = $record['time'];
                    $callInfo = $record['callinfo'];
                    $user = $record['userID'];
                    $phone = $record['phone'];
                    $name = $record['name'];
                    $family = $record['family'];
            ?>
                    <tr class=" min-w-full mb-1 odd:bg-orange-300 even:bg-orange-100">
                        <td class="px-2 py-2"><a target="_blank" href="./main.php?phone=<?= $phone ?>"><?= ($name . " " . $family) ?></a></td>
                        <td>
                            <a class="text-indigo-600" target="_blank" href="./main.php?phone=<?= $phone ?>">
                                <?= $phone ?></a>
                        </td>
                        <td class="px-2 py-2"><?= nl2br($callInfo) ?></td>
                        <td class="px-2 py-2">
                            <input onclick="togglePin(this)" type="checkbox" name="pin" data-id="<?= $recordID ?>" checked>
                        </td>
                        <td class="px-2 py-2"><img class="w-8 h-8 rounded-full mt-1" src="../../public/userimg/<?= $user ?>.jpg" /> </td>
                        <td>
                            <p class="text-right text-gray-700 px-2 py-1 text-xs">
                                <?= displayTimePassed($record['time']) ?>
                            </p>
                        </td>
                    </tr>
                <?php

                }
            }

            if ($askedPrice) {
                foreach ($askedPrice as $record) {
                    $recordID = $record['recordID'];
                    $time = $record['time'];
                    $callInfo = $record['callinfo'];
                    $user = $record['userID'];
                    $phone = $record['phone'];
                    $name = $record['name'];
                    $family = $record['family'];
                ?>
                    <tr class=" min-w-full mb-1 ?> odd:bg-gray-200">
                        <td class="px-2 py-2"><a target="_blank" href="./main.php?phone=<?= $phone ?>"><?= ($name . " " . $family) ?></a></td>
                        <td>
                            <a class="text-indigo-600" target="_blank" href="./main.php?phone=<?= $phone ?>">
                                <?= $phone ?></a>
                        </td>
                        <td class="px-2 py-2"><?= nl2br($callInfo) ?></td>
                        <td class="px-2 py-2">
                            <input onclick="togglePin(this)" type="checkbox" name="pin" data-id="<?= $recordID ?>">
                        </td>
                        <td class="px-2 py-2"><img class="w-8 h-8 rounded-full mt-1" src="../../public/userimg/<?= $user ?>.jpg" /> </td>

                        <td>
                            <p class="text-right text-gray-700 px-2 py-1 text-xs">
                                <?= displayTimePassed($record['time']) ?>
                            </p>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<td colspan="4">هیچ اطلاعاتی موجود نیست</td>';
            }
            ?>
        </table>
    </div>
</div>
<div class=""></div>
<script>
    const resultBox = document.getElementById('resultBox');
    setInterval(() => {
        var params = new URLSearchParams();
        params.append('historyAjax', 'historyAjax');

        axios.post("../../app/api/callcenter/pricesHistoryApi.php", params)
            .then(function(response) {
                resultBox.innerHTML = response.data;
            })
            .catch(function(error) {
                console.log(error);
            });
    }, 20000);

    function togglePin(element) {
        const id = element.getAttribute('data-id');
        let pin = element.checked ? 'pin' : 'unpin';
        var params = new URLSearchParams();
        params.append('togglePin', 'togglePin');
        params.append('pin', pin);
        params.append('id', id);

        axios.post("../../app/api/callcenter/pricesHistoryApi.php", params)
            .then(function(response) {
                resultBox.innerHTML = response.data;
            })
            .catch(function(error) {
                console.log(error);
            });
    }
</script>
<?php
require_once './components/footer.php';

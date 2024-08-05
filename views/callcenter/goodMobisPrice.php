<?php
$pageTitle = "جستجوی اجناس ";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../app/controller/callcenter/SearchGoodsController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
$pattern = $_GET['partNumber'];
$good = getMatchedGoods($pattern);
$result = checkMobis($pattern, $good);
?>
<link href="./assets/css/report.css" rel="stylesheet" />
<div class="py-14">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-100 bg-opacity-25">
            <div class="max-w-7xl overflow-x-auto mx-auto">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="font-medium dark:border-neutral-500">
                        <tr class="bg-green-700">
                            <th scope="col" class="px-3 py-3 bg-black text-white w-52 text-center">
                                شماره فنی
                            </th>
                            <th scope="col" class="px-3 py-3 text-white">
                                دلار پایین
                            </th>
                            <th scope="col" class="px-3 py-3 text-white">
                                دلار میانگین
                            </th>
                            <th scope="col" class="px-3 py-3 text-white border-black border-r-2">
                                دلار بالا
                            </th>

                            <?php
                            if (count($rates) > 0) {
                                // output data of each row
                                foreach ($rates as $rate) {
                                    echo "<th class='" . $rate['status'] . " px-3 py-3 text-white text-center ' scope='col'>" . $rate['amount'] . "</th>";
                                }
                            }
                            ?>
                            <th scope="col" class="px-3 py-3 text-white w-32 text-center">
                                عملیات
                            </th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        <?php
                        if (count($result)) {
                        ?>
                            <tr v-if="result.length > 0" class="transition duration-300 ease-in-out bg-neutral-300">
                                <td class="whitespace-nowrap px-3 py-3 text-center">
                                    <?php echo $result["partNumber"] ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center">
                                    <?php echo round($result["avgPrice"] / 1.1)
                                    ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?php echo round($result["avgPrice"]) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center">
                                    <?php echo round($result["avgPrice"] * 1.1) ?>
                                </td>
                                <?php
                                $sql = "SELECT * FROM rates ORDER BY amount ASC";
                                $rates = $conn->query($sql);
                                if ($rates->num_rows > 0) {
                                    // output data of each row
                                    while ($rate = $rates->fetch_assoc()) {
                                        echo "<th class='b-" . $rate['status'] . " px-3 py-3 text-center ' scope='col'>" . round(
                                            $result["avgPrice"] *
                                                $rate['amount'] *
                                                1.25 *
                                                1.3
                                        ) . "</th>";
                                    }
                                }
                                ?>
                                <td class="whitespace-nowrap w-24">
                                    <div class="flex justify-center gap-1 items-center px-2">
                                        <a target="_blank" :href="
                                                    'https://www.google.com/search?tbm=isch&q=<?php echo $item['partNumber'] ?>">
                                            <img class="w-5 h-auto" src="../../public/img/google.png" alt="google" />
                                        </a>
                                        <a msg="partNumber">
                                            <img class="w-5 h-auto" src="../../public/img/tel.png" alt="part" />
                                        </a>
                                        <a target="_blank" :href="
                                                    'https://www.google.com/search?tbm=isch&q=<?php echo $item['partNumber'] ?>">
                                            <img class="w-5 h-auto" src="../../public/img/part.png" alt="part" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr v-else class="transition duration-300 ease-in-out bg-neutral-200">
                                <td colspan="14" class="whitespace-nowrap px-3 py-3 text-center text-red-500 font-bold">
                                    <i class="material-icons text-red-500">mood_bad</i>
                                    <br />
                                    !این قطعه فاقد موبیز می باشد
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
require_once './components/footer.php';

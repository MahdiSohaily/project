<?php
$pageTitle = "جستجوی اجناس ";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../app/controller/callcenter/SearchGoodsController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php'; ?>
<link href="./assets/css/report.css" rel="stylesheet" />
<div class="py-12">
    <div class="w-11/12 mx-auto bg-gray-100 min-h-screen rounded shadow p-4">
        <div class="flex justify-center pb-3">
            <input style="direction: ltr !important;" type="text" name="serial" id="serial" class="p-3 w-96 border-2 border-gray-300 outline-none uppercase" min="0" max="30" onkeyup="convertToEnglish(this); search(this.value)" placeholder="... کد فنی قطعه را وارد کنید" />
        </div>
        <div class="flex justify-center items-center pb-6">
            <input type="checkbox" name="super" id="mode" class="rounded-md " />
            <label for="mode" class="px-2 text-sm">جستجوی پیشرفته</label>
        </div>
        <div class="bg-gray-100 bg-opacity-25">
            <div class="w-full overflow-x-auto mx-auto">
                <table class="w-full">
                    <thead class="font-medium dark:border-neutral-500">
                        <tr class="bg-green-700">
                            <th scope="col" class="text-sm text-white font-semibold p-3 bg-black w-52 text-center">
                                شماره فنی
                            </th>
                            <th scope="col" class="text-right text-sm text-white font-semibold p-3 w-20">
                                دلار پایه
                            </th>
                            <th scope="col" class="text-right text-sm text-white font-semibold p-3 border-black border-l-2">
                                +10%
                            </th>
                            <?php if (count($rates) > 0) :
                                // output data of each row
                                foreach ($rates as $rate) : ?>
                                    <th scope="col" class="<?= $rate['status'] ?> text-center text-sm text-white font-semibold p-3">
                                        <?= $rate['amount'] ?>
                                    </th>
                            <?php
                                endforeach;
                            endif;
                            ?>
                            <th scope="col" class="text-sm font-semibold p-3 text-white w-32 text-center">
                                عملیات
                            </th>
                            <th scope="col" class="text-right text-sm font-semibold p-3 text-white">
                                وزن
                            </th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        <!-- search Results will be appended here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    let result = null;

    const search = (val) => {
        let pattern = val;
        let superMode = 0;
        const resultBox = document.getElementById("results");

        if (document.getElementById("mode").checked) {
            superMode = 1;
        }

        if (
            (pattern.length > 4 && superMode == 1) ||
            (pattern.length > 6 && superMode == 0)
        ) {
            pattern = pattern.replace(/\s/g, "");
            pattern = pattern.replace(/-/g, "");
            pattern = pattern.replace(/_/g, "");

            resultBox.innerHTML = `<tr class=''>
                                        <td colspan='14' class='py-10 text-center'> 
                                            <img class=' block w-10 mx-auto h-auto' src='../../public/img/loading.png' alt='loading'>
                                        </td>
                                    </tr>`;
            var params = new URLSearchParams();
            params.append('pattern', pattern);
            params.append('superMode', superMode);

            axios.post("../../app/api/callcenter/SearchGoodsApi.php", params)
                .then(function(response) {
                    resultBox.innerHTML = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        } else {
            resultBox.innerHTML = "";
        }
    };
</script>
<?php
require_once './components/footer.php';

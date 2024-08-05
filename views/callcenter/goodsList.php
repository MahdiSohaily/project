<?php
$pageTitle = "لیست اجناس";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../app/controller/callcenter/GoodsListController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
?>
<div>
    <div class="max-w-6xl overflow-x-auto mx-auto pt-11 pb-3 flex justify-between">
        <a href="./registerGoods.php" class="flex justify-center items-center bg-blue-500 hover:bg-blue-400 text-white px-3 text-sm">
            ثبت جنس جدید
        </a>
        <input onkeyup="convertToEnglish(this)" type="text" name="serial" id="serial" class="border-2 border-gray-800 placeholder:text-gray-800 px-3 py-2 w-96 text-sm" min="0" max="30" placeholder="جستجو به اساس شماره فنی ..." />
    </div>
    <div class="bg-gray-100 bg-opacity-25">
        <div class="max-w-6xl overflow-x-auto mx-auto">
            <table class="min-w-full text-left text-sm font-light">
                <thead class="border border-gray-800">
                    <tr class="bg-gray-800">
                        <th scope="col" class="p-3 text-white font-semibold text-center">
                            #
                        </th>
                        <th scope="col" class="p-3 text-white font-semibold text-center">
                            شماره فنی
                        </th>
                        <th scope="col" class="p-3 text-white font-semibold text-center">
                            قیمت
                        </th>
                        <th scope="col" class="p-3 text-white font-semibold text-center">
                            وزن
                        </th>
                        <th scope="col" class="p-3 text-white font-semibold text-center">
                            موبیز
                        </th>
                        <th scope="col" class="p-3 text-white font-semibold text-center">
                            کورآ
                        </th>
                        <th scope="col" class="p-3 text-white font-semibold text-center w-24">
                            عملیات
                        </th>
                    </tr>
                </thead>
                <tbody class="border border-dashed border-gray-600" id="results">
                    <?php
                    $counter = $page > 1 ? $limit * ($page - 1) + 1 : 1;
                    if (count($goods) > 0) :
                        foreach ($goods as $good) : ?>
                            <tr class="even:bg-gray-100">
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?= $counter ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?= $good['partnumber'] ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?= $good['price'] ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?= $good['weight'] ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?= $good['mobis'] ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                    <?= $good['korea'] ?>
                                </td>
                                <td class="whitespace-nowrap w-24">
                                    <div class="flex justify-center gap-1 items-center px-2">
                                        <a title="ویرایش قطعه" class="cursor-pointer" href="./registerGoods.php?form=update&id=<?= $good['id'] ?>">
                                            <i class="material-icons text-blue-500 hover:text-blue-700">create</i>
                                        </a>
                                        <a title="حذف کد فنی" class="cursor-pointer" type="submit" onclick="confirmDeletion(this)" data-id="<?= $good['id'] ?>">
                                            <i :data-id="item.id" class="material-icons text-red-600 hover:text-red-800">delete_forever</i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                            $counter += 1;
                        endforeach;
                    else : ?>
                        <tr v-else class="transition duration-300 ease-in-out bg-neutral-200">
                            <td colspan="6" class="whitespace-nowrap px-3 py-3 text-center text-red-500 font-bold">
                                <i class="material-icons text-red-500">mood_bad</i>
                                <br />
                                !متاسفانه چیزی برای نمایش در پایگاه داده
                                موجود نیست
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
// Calculate the offset for the SQL query
$offset = ($page - 1) * $limit;

// Display pagination links
echo '<div class="flex justify-center items-center p-5">';
echo '<span class="bg-gray-600 rounded-md flex justify-center items-center text-white px-3 h-8 m-1 ">صحفه  ' . $page . ' از ' . $totalPages . '</span>';

// Previous page link
if ($page > 1) {
    echo '<a class="prev bg-rose-600 text-white rounded-md px-3 h-8 flex justify-center items-center" href="?page=' . ($page - 1) . '">قبلی</a>';
}

// Page links with limited visibility
$startPage = max(1, $page - 2);
$endPage = min($totalPages, $startPage + 4);

for ($i = $startPage; $i <= $endPage; $i++) {
    echo '<a class="' . ($i == $page ? 'bg-gray-900' : 'bg-gray-600') . ' rounded-md flex justify-center items-center text-white w-8 h-8 m-1" href="?page=' . $i . '">' . $i . '</a>';
}

// Next page link
if ($page < $totalPages) {
    echo '<a class="next bg-rose-600 text-white rounded-md px-3 h-8 flex justify-center items-center" href="?page=' . ($page + 1) . '">بعدی</a>';
}

echo '</div>';
?>
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
                    <img class=' block w-10 mx-auto h-auto' src='./public/img/loading.png' alt='loading'>
                    </td>
            </tr>`;
            var params = new URLSearchParams();
            params.append('pattern', pattern);
            params.append('superMode', superMode);

            axios.post("./app/Controllers/SearchController.php", params)
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

    function confirmDeletion(e) {
        const element = e;
        const deleteItem = element.getAttribute('data-id');

        var params = new URLSearchParams();

        params.append('delete_id', deleteItem);
        params.append('Delete_Good', 'Delete_Good');

        let text = "آیا مطمئن هستید که میخواهید عملیات حذف را انجام دهید؟";
        if (confirm(text) == true) {
            axios.post("../../app/api/callcenter/GoodsListApi.php", params)
                .then(function(response) {
                    if (response.data) {
                        location.reload();
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        }

    }
</script>
<?php
require_once './components/footer.php';

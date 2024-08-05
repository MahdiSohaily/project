<div class="bg-white rounded-lg p-5 shadow-md hover:shadow-lg">
    <div class="border border-dashed border-gray-800 flex flex-col items-center h-full rounded-lg">
        <div class="overflow-x-auto shadow-md sm:rounded-lg w-full h-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-800 h-full">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="font-semibold text-sm text-right text-gray-800 px-6 py-3">
                            شهرت
                        </th>
                        <th scope="col" class="font-semibold text-sm text-right text-gray-800 px-6 py-3">
                            آي پی آدرس
                        </th>
                        <th scope="col" class="font-semibold text-sm text-right text-gray-800 px-6 py-3">
                            داخلی
                        </th>
                        <th scope="col" class="font-semibold text-sm text-right text-gray-800 px-6 py-3">
                            مدت زمان مکالمه
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach (getCallCenterUsers() as $user) :
                        $profile = '../../public/userimg/default.png';
                        if (file_exists("../../public/userimg/" . $user['id'] . ".jpg")) {
                            $profile = "../../public/userimg/" . $user['id'] . ".jpg";
                        } ?>
                        <tr class="border-b/10 hover:bg-gray-50 even:bg-gray-100">
                            <th scope="row" class="flex items-center px-6 py-2 text-gray-800 whitespace-nowrap">
                                <img class="w-10 h-10 rounded-full" src="<?= $profile ?>" alt="Jese image">
                                <div class="ps-3">
                                    <div class="text-base font-semibold text-right"><?= $user['name'] . ' ' . $user['family'] ?></div>
                                    <div class="text-sm font-normal text-gray-500 py-1 text-right"><?= $user['username'] ?></div>
                                </div>
                            </th>
                            <td class="px-6 py-4 text-right text-sm">
                                <?= $user['ip'] ?>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div>
                                    <?= $user['internal'] ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <?=
                                isset($datetimeData[$user['internal']]['total']) ? format_calling_time_seconds($datetimeData[$user['internal']]['total']) : ' ۰ ثانیه'
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?php
$pageTitle = "ثبت جنس جدید در سیستم";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../app/controller/callcenter/RegisterGoodsController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
$form = 'create';

if (isset($_GET['form'])) {
    $form = $_GET['form'];
}
?>
<div>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <?php if ($form === 'create') { ?>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gray-800 flex justify-between p-5">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-white">
                            ثبت جنس جدید در سیستم
                        </h3>

                        <p class=" mt-1 text-sm text-white">
                            برای ثبت یک جنس جدید در سیستم فورم ذیل را به صورت دقیق خانه پری نمایید.
                        </p>
                    </div>
                </div>
                <form action="" method="post">
                    <div class="p-5">
                        <input type="text" name="form" value="create" hidden>
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700">
                                شماره فنی
                            </label>
                            <input onkeyup="convertToEnglish(this)" name="partNumber" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" required id="serial" type="text" />
                            <p class="mt-2"></p>
                        </div>

                        <!-- Price -->
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700">
                                قیمت جنس
                            </label>
                            <input name="price" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="price" type="text" />
                            <p class="mt-2"> </p>
                        </div>
                        <!-- Weight -->
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700">
                                وزن جنس
                            </label>
                            <input name="weight" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="weight" type="text" />
                            <p class="mt-2"> </p>
                        </div>
                        <!-- Mobis -->
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700">
                                موبیز
                            </label>
                            <input name="mobis" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="mobis" type="text" />
                            <p class="mt-2"> </p>
                        </div>
                        <!-- Korea section -->
                        <div class="col-span-6 sm:col-span-4">
                            <label class="block font-medium text-sm text-gray-700">
                                کوریا
                            </label>
                            <input name="korea" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="korea" v-model="form.korea" type="text" />
                            <p class="mt-2"> </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-start px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        <button type="type" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="px-2 material-icons hover:cursor-pointer">save</i>
                            ذخیره سازی
                        </button>
                        <?php
                        if ($success) { ?>
                            </br>
                            <p class="text-green-400"><?= $success ?></p>
                        <?php  } elseif ($errors) { ?>
                            <br>
                            <p class="text-orange-400"><?= $errors ?></p>
                        <?php
                        }
                        ?>
                    </div>
                </form>
            </div>
        <?php
        } else { ?>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-gray-800 flex justify-between p-5">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-white">
                            ویرایش اطلاعات جنس در سیستم
                        </h3>

                        <p class="mt-1 text-sm text-white">
                            برای ویرایش اطلاعات جنس در سیستم فورم ذیل را به صورت دقیق خانه پری نمایید.
                        </p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="" method="post">
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                            <div class="">
                                <input type="text" name="form" value="update" hidden>
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700">
                                        شماره فنی
                                    </label>
                                    <input name="partNumber" value="<?= $selected_good['partnumber'] ?>" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" required id="serial" type="text" />
                                    <p class="mt-2"></p>
                                </div>

                                <!-- Price -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700">
                                        قیمت جنس
                                    </label>
                                    <input name="price" value="<?= $selected_good['price'] ?>" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="price" type="text" />
                                    <p class="mt-2"> </p>
                                </div>
                                <!-- Weight -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700">
                                        وزن جنس
                                    </label>
                                    <input name="weight" value="<?= $selected_good['weight'] ?>" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="weight" type="text" />
                                    <p class="mt-2"> </p>
                                </div>
                                <!-- Mobis -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700">
                                        موبیز
                                    </label>
                                    <input name="mobis" value="<?= $selected_good['mobis'] ?>" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="mobis" type="text" />
                                    <p class="mt-2"> </p>
                                </div>
                                <!-- Korea section -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block font-medium text-sm text-gray-700">
                                        کوریا
                                    </label>
                                    <input name="korea" value="<?= $selected_good['korea'] ?>" class="border mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm px-3 py-2" id="korea" v-model="form.korea" type="text" />
                                    <p class="mt-2"> </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                            <button type="type" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="px-2 material-icons hover:cursor-pointer">save</i>
                                ویرایش اطلاعات
                            </button>
                            <?php
                            if ($success) {
                            ?>
                                <br>
                                <p class="bg-green-700 px-5 py-2 text-white rounded-md text-xs"><?= $success ?></p>
                            <?php
                            }
                            ?>

                            <?php
                            if ($errors) {
                            ?>
                                <br>
                                <p class="bg-rose-700 px-5 py-2 text-white rounded-md text-xs"><?= $errors ?></p>
                            <?php
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        <?php
        } ?>
    </div>
</div>
<?php
require_once './components/footer.php';

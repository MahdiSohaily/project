<?php
$pageTitle = "پیام خودکار";
$iconUrl = 'telegram.svg';
require_once './components/header.php';
require_once '../../app/controller/telegram/MessageController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
$messages = getMessages();
?>
<div class="rtl flex px-5 gap-5 h-screen flex justify-center">
    <section class=" border container lg:w-1/2 border-dotted border-2 rounded-md">
        <h2 class="text-xl font-bold bg-gray-300 p-5 rounded-t-md">پیام های ارسال شده</h2>
        <div id="messages_container" class="p-5">
            <?php foreach ($messages as $message) : ?>
                <div class="flex justify-end">
                    <div class="request rounded-md bg-green-100 inline-block  w-80">
                        <p class="ltr p-2 text-sm font-bold border-b border-dashed border-green-300"><?= $message['name'] ?></p>
                        <p class="p-2 ltr text-sm"><?= $message['request'] ?></p>
                    </div>
                </div>
                <div>
                    <div class="response rounded-md bg-blue-100 inline-block w-80">
                        <p class="ltr p-2 text-sm font-bold border-b border-dashed border-blue-300">Yadak Shop</p>
                        <p class="p-2 ltr text-sm"><?= $message['response'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php
require_once './components/footer.php';

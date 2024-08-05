<div class="min-w-full bg-white <?= $messagesSize; ?> overflow-auto shadow-md p-2">
    <table class="min-w-full text-xs font-light">
        <thead>
            <tr class="min-w-full bg-gray-700">
                <td class="text-white bold text-center py-2 px-2 ">پیام دریافتی
                </td>
            </tr>
        </thead>
        <tbody>
            <tr class="min-w-full mb-4 border-b-2 border-white">
                <td class="text-gray-800 py-2 text-center bg-indigo-300">
                    <?= nl2br($messages); ?>
                </td>
            </tr>
            <tr class="min-w-full mb-1 border-b-2 bg-red-400">
                <td>
                    <i class="px-1 material-icons tiny-text text-white">access_time</i>
                    <span class="text-white px-1 text-xs py-2"><?= date('Y-m-d H:m:i', $message_date) ?></span>
                </td>

            </tr>
        </tbody>
    </table>
</div>
<?php
$pageTitle = "تماس عمومی";
$iconUrl = 'callcenter.svg';
require_once './components/header.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';

function getIP($user_id)
{
    try {
        // SQL query to fetch IP from users table based on ID
        $sql = "SELECT ip FROM users WHERE id = :id";
        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);
        // Bind parameter
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        // Execute the query
        $stmt->execute();
        // Fetch the result
        $ip = $stmt->fetchColumn();
        // Close the statement
        $stmt->closeCursor();
        return $ip;
    } catch (PDOException $e) {
        // Handle exception here, if needed
        return false;
    }
}
?>
<section class="box px-5">
    <table class="w-full">
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">
                نیایش

            </td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09123612779
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">بابک</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09127204134
                </p>
            </td>
        </tr>

        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">آقای امیردوست</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09100493873
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">آقای عباسی</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09195597992
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">ساسان</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09903870946
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">خانم رحیمی</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09125805827
                </p>
            </td>
        </tr>


        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">حسابداری</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    36870452
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">موبایل و تلگرام حسابداری</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09930703612
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">حامد انبار</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09385141911
                </p>
            </td>
        </tr>

        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">نیایش 2</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09357884727
                </p>
            </td>
        </tr>
        <tr class="even:bg-gray-200">
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="w-8"><span class="w-6 h-6 p-2 border-2 border-gray-500 flex rounded-full"></span></td>
            <td class="p-4">نیایش 3</td>
            <td class="p-4">
                <p class="text-white text-xs font-semibold px-3 py-2 bg-red-500 rounded inline-block cursor-pointer">
                    09120465969
                </p>
            </td>
        </tr>
    </table>
    <div class="bazar-click-to-cancel fixed bottom-4 right-4 p-2 cursor-pointer rounded bg-red-500 hover:bg-red-700 text-white font-sm" href="">قطع تماس جاری</div>
</section>

<div class="space"></div>

<script>
    $(document).ready(function() {
        $("p").click(function() {
            $(this).addClass("called-tel")
            if (confirm($(this).parent().children().eq(6).text() + "\n" + "شماره تماس : " + $(this).text())) {
                window.open('http://admin:1028400NRa@<?= getIp($_SESSION["id"]) ?>/servlet?key=number=' + $(this).text() + '&outgoing_uri=@192.168.9.10', 'برقراری تماس', 'width=200,height=200')
            }
        });

        $(".bazar-click-to-cancel").click(function() {
            window.open('http://admin:1028400NRa@<?= getIp($_SESSION["id"]) ?>/servlet?key=CALLEND', 'برقراری تماس', 'width=200,height=200')
        });
    });
</script>
<?php
require_once './components/footer.php';

<?php
$pageTitle = "کارتابل شخصی";
$iconUrl = 'callcenter.svg';
require_once './components/header.php';
require_once '../../app/controller/callcenter/CartableController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
global $repeatKeeper;
$statusKeeper = 0;
$n = 0;
$img = '';
?>
<style>
    .overlay {
        position: absolute;
        background-color: rgba(85, 85, 85, 0.826);
        color: white;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        top: 100%;
        overflow: hidden;
        transition: all 0.2s ease-in-out;
    }

    .parent:hover .overlay {
        top: 0;
    }
</style>
<div class="grid lg:grid-cols-5 md:grid-cols-3  gap-6 px-4">
    <?php
    if (count($incomingCalls) > 0) :
        foreach ($incomingCalls as $row) :
            $n = $n + 1;
            $interval = timeDef($repeatKeeper, $row["time"]);
            $TimeSecond =   $interval->s;
            $TimeMinute =   $interval->i;

            if ($TimeSecond > 1 || $TimeMinute > 0) :
                $repeatKeeper =  $row["time"];

                if ($n == 1) {
                    $phone = $row['phone'];
                    $status = $row['status'];
                    $statusKeeper = $statusKeeper . $status;
                    $callId = $row["callid"];
                    $internal = $row["user"];

                    $answer = '';

                    if ($status == 1) {
                        $answer = 'class="this-user-answer"';
                    }

                    $file = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                    if (!file_exists($file)) {
                        $file = "../../public/userimg/default.png";
                    }

                    $img = $img . "<img class='w-10 h-10 rounded-full' $answer   src='$file' />";
                    $tagLabel = '';
                    $userLabel = '';
                    $jalali_time = '';
                    continue;
                }
                $stmt = $pdo->prepare("SELECT * FROM callcenter.customer WHERE phone LIKE :phone");
                $pattern = $phone . '%';
                $stmt->bindParam(':phone', $pattern, PDO::PARAM_STR);
                $stmt->execute();

                if ($statusKeeper == 0) {
                    $additionalClasses = 'this-capsol-answer';
                }

                if ($internal > 150) {
                    $additionalClasses .= ' capsol-bazar';
                }

                if ($stmt->rowCount() > 0) {
                    while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $name = $row2['name'];
                        $family = $row2['family'];
                        $userLabel = $row2['user'];
                        $tagLabel = $row2['label'];
                        $additionalClasses = ''; ?>
                        <a href="main.php?phone=<?= $phone ?>" class="parent bg-gray-200 p-2 rounded-lg relative <?= $additionalClasses ?>">
                            <div class="call-capsol-phone"><?= $phone ?></div>
                            <div class="call-capsol-name"><?= $name ?> <?= $family ?></div>
                            <div class="call-capsol-extra-info"><?php mahakContact($phone); ?></div>
                            <div class="call-capsol-extra-info"><?php googleContact($phone); ?></div>
                            <div class="call-capsol-user-img"><?= $img ?></div>
                            <div class="call-capsol-taglabel"> <?php tagLabelShow($tagLabel)  ?></div>
                            <div class="call-capsol-userlabel"> <?php userLabelShow($userLabel)  ?></div>
                            <div class="call-capsol-if-reconnect"><?php outgoingContact($phone) ?></div>
                            <div class="call-capsol-time-info"><?= $jalali_time ?></div>
                            <div class="call-capsol-time-ago"><?= $jalali_time_ago ?></div>
                            <div class="overlay rounded-lg text-sm">آخرین استعلام نمایش داده می شود</div>
                        </a>
                    <?php
                    }
                } else {
                    ?>
                    <a href="main.php?phone=<?= $phone ?>" class="parent bg-gray-200 p-2 rounded-lg relative <?= $additionalClasses ?>">
                        <div class="call-capsol-phone"><?= $phone ?></div>
                        <div class="call-capsol-name no-save">این شماره ذخیره نشده است</div>
                        <div class="call-capsol-extra-info"><?php mahakContact($phone); ?></div>
                        <div class="call-capsol-extra-info"><?php googleContact($phone); ?></div>
                        <div class="call-capsol-user-img"><?= $img ?></div>
                        <div class="call-capsol-taglabel"> <?php tagLabelShow($tagLabel)  ?></div>
                        <div class="call-capsol-userlabel"> <?php userLabelShow($userLabel)  ?></div>
                        <div class="call-capsol-if-reconnect"><?php outgoingContact($phone) ?></div>
                        <div class="call-capsol-time-info"><?= $jalali_time ?></div>
                        <div class="call-capsol-time-ago"><?= $jalali_time_ago ?></div>
                        <div class="overlay rounded-lg text-sm">آخرین استعلام نمایش داده می شود</div>
                    </a>
                <?php
                }

                $img = '';
                $tagLabel = '';
                $userLabel = '';
                $statusKeeper = '';
                // get value 
                $phone = $row['phone'];
                $status = $row['status'];
                $statusKeeper = $statusKeeper . $status;
                $callId = $row["callid"];
                $internal = $row["user"];
                $start = $row['starttime'];
                $end = $row['endtime'];
                $answerTime = timeDef($start, $end);
                $answerTime = '<div class="capsol-answer-time">' . format_calling_time($answerTime) . '</div>';


                if ($status == 1) {
                    $answer = 'class="this-user-answer"';
                } else {
                    $answer = '';
                    $answerTime = '';
                }

                $file = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                if (!file_exists($file)) {
                    $file = "../../public/userimg/default.png";
                }

                $img = $img . "<div><img class='w-10 h-10 rounded-full' $answer   src='$file' /> $answerTime </div>";

                $jalali_time = jalalitime($row["time"]);
                $jalali_time_ago =  format_interval(timeDef(date('Y/m/d H:i:s'), $row["time"]));
            else :
                ?>
    <?php
                // get value 

                $phone = $row['phone'];
                $status = $row['status'];
                $statusKeeper = $statusKeeper . $status;

                $callId = $row["callid"];
                $internal = $row["user"];

                $start = $row['starttime'];
                $end = $row['endtime'];
                $answerTime = timeDef($start, $end);
                $answerTime = '<div class="capsol-answer-time">' . format_calling_time($answerTime) . '</div>';

                if ($status == 1) {
                    $answer = 'class="this-user-answer"';
                } else {
                    $answer = '';
                    $answerTime = '';
                }

                $file = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                if (!file_exists($file)) {
                    $file = "../../public/userimg/default.png";
                }

                $img = $img . "<div><img class='w-10 h-10 rounded-full' $answer src='$file' /> $answerTime </div>";

                $jalali_time = jalalitime($row["time"]);
                $jalali_time_ago =  format_interval(timeDef(date('Y/m/d H:i:s'), $row["time"]));
            endif;
        endforeach;
    endif;
    ?>
</div>
<?php
require_once './components/footer.php';

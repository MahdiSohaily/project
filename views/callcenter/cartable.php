<?php
$pageTitle = "کارتابل";
$iconUrl = 'callcenter.svg';
require_once './components/header.php';
require_once '../../app/controller/callcenter/CartableController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
$result = getIncomingCalls();
?>
<link rel="stylesheet" href="./assets/css/style.css">
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

    body {
        padding-top: 10px !important;
    }
</style>
<div style="z-index: 100;" class="flex items-center gap-2 w-14 hover:w-64 transition bg-gray-300 p-2 rounded fixed bottom-5 right-0 overflow-hidden">
    <a class="text-xs bg-rose-600 p-1 text-white" href="#">کارتابل</a>
    <div class="w-full bg-white outline-none h-8 overflow-hidden border-2 border-gray-400 p-1" style="direction: ltr !important;" contenteditable="true"></div>
</div>
<div class="grid lg:grid-cols-5 md:grid-cols-3 gap-6 px-4">
    <?php if (count($result) > 0) {
        foreach ($result as $row) {
            $n = $n + 1;
            $interval = timeDef($repeatKeeper, $row["time"]);
            $capsoltimesecond =   $interval->s;
            $capsoltimeminute =   $interval->i;

            if ($capsoltimesecond > 1 or $capsoltimeminute > 0) {
                $repeatKeeper =  $row["time"];

                if ($n == 1) {
                    $phone = $row['phone'];
                    $status = $row['status'];
                    $statusKeeper = $statusKeeper . $status;
                    $callid = $row["callid"];
                    $internal = $row["user"];

                    if ($status == 1) {
                        $answer = 'class="this-user-answer"';
                    } else {
                        $answer = '';
                    }

                    $file = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                    if (!file_exists($file)) {
                        $file = "../../public/userimg/default.png";
                    }

                    $img = $img . "<img $answer   src='$file' />";
                    $taglabel = '';
                    $userlabel = '';
                    $jalali_time = '';

                    continue;
                }

                $sql2 = "SELECT * FROM callcenter.customer WHERE phone LIKE '" . $phone . "%'";
                $stmt = PDO_CONNECTION->prepare($sql2);
                $stmt->execute();
                $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result2) > 0) {
                    foreach ($result2 as $row2) {
                        $name = $row2['name'];
                        $family = $row2['family'];
                        $userlabel = $row2['user'];
                        $taglabel = $row2['label']; ?>

                        <a href="main.php?phone=<?= $phone ?>" class="parent bg-gray-200 p-2 rounded-lg relative <?php if ($statusKeeper == 0) {
                                                                                                                        echo 'this-capsol-answer';
                                                                                                                    } ?> <?php if ($internal > 150) {
                                                                                                                                echo 'capsol-bazar';
                                                                                                                            } ?>">
                            <div class="call-capsol-phone"><?= $phone ?></div>
                            <div class="call-capsol-name"><?= $name ?> <?= $family ?></div>
                            <div class="call-capsol-extra-info"><?php mahakcontact($phone); ?></div>
                            <div class="call-capsol-extra-info"><?php googlecontact($phone); ?></div>
                            <div class="call-capsol-user-img"><?= $img ?></div>
                            <div class="call-capsol-taglabel"> <?php taglabelshow($taglabel)  ?></div>
                            <div class="call-capsol-userlabel"> <?php userlabelshow($userlabel)  ?></div>
                            <div class="call-capsol-if-reconnect"><?php outgoingContact($phone) ?></div>
                            <div class="call-capsol-time-info"><?= $jalali_time ?></div>
                            <div class="call-capsol-time-ago"><?= $jalali_time_ago ?></div>
                            <div class="overlay rounded-lg text-sm">آخرین استعلام نمایش داده می شود</div>
                        </a>
                    <?php
                    }
                } else {
                    ?>
                    <a href="main.php?phone=<?= $phone ?>" class="parent bg-gray-200 p-2 rounded-lg relative <?php if ($statusKeeper == 0) {
                                                                                                                    echo 'this-capsol-answer';
                                                                                                                }
                                                                                                                if ($internal > 150) {
                                                                                                                    echo 'capsol-bazar';
                                                                                                                } ?>">
                        <div class="call-capsol-phone"><?= $phone ?></div>
                        <div class="call-capsol-name no-save">این شماره ذخیره نشده است</div>
                        <div class="call-capsol-extra-info"><?php mahakcontact($phone); ?></div>
                        <div class="call-capsol-extra-info"><?php googlecontact($phone); ?></div>
                        <div class="call-capsol-user-img"><?= $img ?></div>
                        <div class="call-capsol-taglabel"> <?php taglabelshow($taglabel)  ?></div>
                        <div class="call-capsol-userlabel"> <?php userlabelshow($userlabel)  ?></div>
                        <div class="call-capsol-if-reconnect"><?php outgoingContact($phone) ?></div>
                        <div class="call-capsol-time-info"><?= $jalali_time ?></div>
                        <div class="call-capsol-time-ago"><?= $jalali_time_ago ?? '' ?></div>
                        <div class="overlay rounded-lg text-sm">آخرین استعلام نمایش داده می شود</div>
                    </a>
    <?php
                }
                $img = '';
                $taglabel = '';
                $userlabel = '';
                $statusKeeper = '';
                // get value 

                $phone = $row['phone'];
                $status = $row['status'];
                $statusKeeper = $statusKeeper . $status;
                $callid = $row["callid"];
                $internal = $row["user"];
                $start = $row['starttime'];
                $end = $row['endtime'];
                $answertime = timeDef($start, $end);
                $answertime = '<div class="capsol-answer-time">' . format_calling_time($answertime) . '</div>';


                if ($status == 1) {
                    $answer = 'class="this-user-answer"';
                } else {
                    $answer = '';
                    $answertime = '';
                }

                $file = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                if (!file_exists($file)) {
                    $file = "../../public/userimg/default.png";
                }

                $img = $img . "<div><img $answer   src='$file' /> $answertime </div>";

                $jalali_time = jalalitime($row["time"]);
                $jalali_time_ago =  format_interval(timeDef(date('Y/m/d H:i:s'), $row["time"]));
            } else {

                // get value 

                $phone = $row['phone'];
                $status = $row['status'];
                $statusKeeper = $statusKeeper . $status;

                $callid = $row["callid"];
                $internal = $row["user"];

                $start = $row['starttime'];
                $end = $row['endtime'];
                $answertime = timeDef($start, $end);
                $answertime = '<div class="capsol-answer-time">' . format_calling_time($answertime) . '</div>';

                if ($status == 1) {
                    $answer = 'class="this-user-answer"';
                } else {
                    $answer = '';
                    $answertime = '';
                }

                $file = "../../public/userimg/" . getIdByInternal($internal) . ".jpg";

                if (!file_exists($file)) {
                    $file = "../../public/userimg/default.png";
                }

                $img = $img . "<div><img $answer   src='$file' /> $answertime </div>";

                $jalali_time = jalalitime($row["time"]);
                $jalali_time_ago =  format_interval(timeDef(date('Y/m/d H:i:s'), $row["time"]));
            }
        }
    }
    ?>
</div>
<?php
require_once './components/footer.php';

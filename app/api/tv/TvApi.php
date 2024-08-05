<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
// Check if session ID is set
if (!isset($_SESSION['id'])) {
    header("Refresh:1");
    exit;
}
require_once '../../../database/db_connect.php';
require_once '../../../app/controller/tv/TvController.php';
require_once '../../../utilities/jdf.php';

if (isset($_POST['action']) && $_POST['action'] == 'toggleTV') {
    $status = getTvStatus();
    $newStatus = $status == 'on' ? 'off' : 'on';
    $sql = "UPDATE shop.tv SET status = '$newStatus' WHERE id='1'";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    echo json_encode(['status' => $newStatus]);
    exit();
}

if (getTvStatus() == 'on') : ?>
    <div class="bg-white">
        <table>
            <tr>
                <td>کاربر</td>
                <?php
                foreach ($datetimeData as $key => $value) :
                    $file = "../../../public/userimg/" . getIdByInternal($key) . ".jpg";
                    if (file_exists($file)) : ?>
                        <td> <img class="user-imgs" src="../../public/userimg/<?= getIdByInternal($key) ?>.jpg" /></td>
                    <?php else : ?>
                        <td>
                            <p class="circle-frame">
                                <?= $key ?>
                            </p>
                        </td>
                <?php
                    endif;
                endforeach;
                ?>
            </tr>
            <tr>
                <td>فعلا</td>
                <?php foreach ($datetimeData as $key => $value) : ?>
                    <td style='text-align: center; font-weight:bold;'><?= format_calling_time_seconds($value['currentHour']) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td> زمان کلی </td>
                <?php foreach ($datetimeData as $key => $value) : ?>
                    <td style='text-align: center; font-weight:bold;'><?= format_calling_time_seconds($value['total']) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td>
                    <img src="./assets/icons/received.svg" alt="received calls icon">
                </td>
                <?php foreach ($datetimeData as $key => $value) : ?>
                    <td style='text-align: center; font-weight:bold;'><?= ($value['receivedCall']) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td>
                    <img src="./assets/icons/answered.svg" alt="" srcset="">
                </td>
                <?php foreach ($datetimeData as $key => $value) : ?>
                    <td style='text-align: center; font-weight:bold;'>
                        <?= ($value['answeredCall']) ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td>
                    <img class="w-8 h-8" src="./assets/icons/success_rate.svg" alt="">
                </td>
                <?php foreach ($datetimeData as $key => $value) : ?>
                    <td style='text-align: center; font-weight:bold;'>
                        <?= $value['successRate'] . "%" ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        </table>
        <div style="display: flex; gap:5px; padding-block:5px">
            <img onclick="openFullscreen()" class="handler" src="./assets/icons/fullScreen.svg" alt="full screen" style="cursor: pointer;">
            <img onclick="closeFullscreen()" class="handler" src="./assets/icons/minimize.svg" alt="full screen" style="cursor: pointer;">
        </div>
        <div class="d-grid">
            <div class="div1">
                <h2 class="section_heading">تماس های ورودی</h2>
                <table>
                    <thead>
                        <tr>
                            <th class="bg-violet-800 text-white tiny-text px-2 py-2">مشخصات</th>
                            <th class="bg-violet-800 text-white tiny-text px-2 py-2">شماره تماس</th>
                            <th class="bg-violet-800 text-white tiny-text px-2 py-2">نیایش</th>
                            <th class="bg-violet-800 text-white tiny-text px-2 py-2">محک</th>
                            <th class="bg-violet-800 text-white tiny-text px-2 py-2">زمان</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $incomingCalls = getIncomingCalls($user);
                        if (count($incomingCalls) > 0) {
                            foreach ($incomingCalls as $call) {
                                $phone = $call['phone'];
                                $user = $call['user'];
                                $status = $call['status'];
                                $date = $call["time"];
                                $array = explode(' ', $date);
                                list($year, $month, $day) = explode('-', $array[0]);
                                list($hour, $minute, $second) = explode(':', $array[1]);
                                $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
                                $jalali_time = jdate("H:i", $timestamp, "", "Asia/Tehran", "en");
                                $jalali_date = jdate("Y/m/d", $timestamp, "", "Asia/Tehran", "en");

                                $sql2 = "SELECT * FROM callcenter.customer WHERE phone LIKE :phone";
                                $sql2 = PDO_CONNECTION->prepare($sql2);
                                $sql2->bindParam(':phone', $phone, PDO::PARAM_STR);
                                $sql2->execute();
                                $result2 = $sql2->fetchAll(PDO::FETCH_ASSOC);
                                if (count($result2)) {
                                    foreach ($result2 as $row2) {
                                        $name = $row2['name'];
                                        $family = $row2['family']; ?>
                                        <tr>
                                            <td><?= $name . " " . $family ?></td>
                                            <td> <?= $phone ?></td>
                                            <td>
                                                <?php
                                                $gphone = substr($phone, 1);
                                                $sql3 = "SELECT * FROM callcenter.google WHERE mob1 LIKE :gPhone OR mob2 LIKE :gPhone OR mob3 LIKE :gPhone  ";
                                                $sql3 = PDO_CONNECTION->prepare($sql3);
                                                $gphone = "%" . $gphone . "%";
                                                $sql3->bindParam(':gPhone', $gphone, PDO::PARAM_STR);
                                                if ($sql3->execute()) {
                                                    $result3 = $sql3->fetchAll(PDO::FETCH_ASSOC);
                                                    $n = 1;
                                                    foreach ($result3 as $row3) {
                                                        $gname1 = $row3['name1'];
                                                        $gname2 = $row3['name2'];
                                                        $gname3 = $row3['name3'];
                                                        if (strlen($phone) < 5) {
                                                            break;
                                                        }
                                                        if ($n > 1) {
                                                            echo ("<br>");
                                                        }
                                                        echo $gname1 . " " . $gname2 . " " . $gname3;
                                                        $n++;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $gphone = substr($phone, 1);
                                                $sql4 = "SELECT * FROM callcenter.mahak WHERE mob1 LIKE :gPhone OR mob2 LIKE :gPhone";
                                                $sql4 = PDO_CONNECTION->prepare($sql4);
                                                $gphone = "%" . $gphone . "%";
                                                $sql4->bindParam(':gPhone', $gphone, PDO::PARAM_STR);
                                                if ($sql4->execute()) {
                                                    $result4 = $sql4->fetchAll(PDO::FETCH_ASSOC);
                                                    $n = 1;
                                                    foreach ($result4 as $row4) {
                                                        $mname1 = $row4['name1'];
                                                        $mname2 = $row4['name2'];

                                                        if (strlen($phone) < 5) {
                                                            break;
                                                        }

                                                        if ($n > 1) {
                                                            echo ("<br>");
                                                        }
                                                        echo $mname1 . " " . $mname2;
                                                        $n++;
                                                    }
                                                }
                                                ?></td>
                                            <td><?= $jalali_time ?></td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="./assets/icons/cancel.svg" alt="cancel icon">
                                        </td>
                                        <td><?= $phone ?></td>
                                        <td>
                                            <?php
                                            $gphone = substr($phone, 1);
                                            $sql3 = "SELECT * FROM callcenter.google WHERE mob1 LIKE :gPhone OR mob2 LIKE :gPhone OR mob3 LIKE :gPhone";
                                            $sql3 = PDO_CONNECTION->prepare($sql3);
                                            $gphone = "%" . $gphone . "%";
                                            $sql3->bindParam(':gPhone', $gphone, PDO::PARAM_STR);
                                            if ($sql3->execute()) {
                                                $result3 = $sql3->fetchAll(PDO::FETCH_ASSOC);
                                                $n = 1;
                                                foreach ($result3 as $row3) {
                                                    $gname1 = $row3['name1'];
                                                    $gname2 = $row3['name2'];
                                                    $gname3 = $row3['name3'];
                                                    if (strlen($phone) < 5) {
                                                        break;
                                                    }
                                                    if ($n > 1) {
                                                        echo ("<br>");
                                                    }
                                                    echo $gname1 . " " . $gname2 . " " . $gname3;
                                                    $n++;
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $gphone = substr($phone, 1);
                                            $sql4 = "SELECT * FROM callcenter.mahak WHERE mob1 LIKE :gPhone OR mob2 LIKE :gPhone";
                                            $sql4 = PDO_CONNECTION->prepare($sql4);
                                            $gphone = "%" . $gphone . "%";
                                            $sql4->bindParam(':gPhone', $gphone, PDO::PARAM_STR);
                                            if ($sql4->execute()) {
                                                $result4 = $sql4->fetchAll(PDO::FETCH_ASSOC);
                                                $n = 1;
                                                foreach ($result4 as $row4) {
                                                    $mname1 = $row4['name1'];
                                                    $mname2 = $row4['name2'];

                                                    if (strlen($phone) < 5) {
                                                        break;
                                                    }

                                                    if ($n > 1) {
                                                        echo ("<br>");
                                                    }
                                                    echo $mname1 . " " . $mname2;
                                                    $n++;
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><?= $jalali_time ?></td>
                                    </tr>
                        <?php
                                }
                            }
                        } // end while
                        else {
                            echo 'هیچ اطلاعاتی موجود نیست';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="div2">
                <h2 class="section_heading">آخرین قیمت های داده شده</h2>
                <table>
                    <thead>
                        <tr>
                            <th> کد فنی </th>
                            <th> قیمت</th>
                            <th> مشتری</th>
                            <th> کاربر </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $givenPrice = givenPrice();
                        if (count($givenPrice) > 0) : ?>
                            <?php foreach ($givenPrice as $price) : ?>
                                <?php if ($price['price'] !== null) : ?>
                                    <tr>
                                        <td class="strong_content"> <?= $price['partnumber']; ?></td>
                                        <td style="direction: ltr !important;"> <?= $price['price'] === null ? 'ندارد' : $price['price'] ?></td>
                                        <td><?= $price['name'] . ' ' . $price['family'] ?></td>
                                        <td class="pic">
                                            <img title="<?= $price['username'] ?>" class="user-img" src="../../public/userimg/<?= $price['userID'] ?>.jpg" alt="user-img">
                                        </td>
                                    </tr>
                            <?php endif;
                            endforeach;
                        else :  ?>
                            <tr>
                                <td colspan="4" scope="col">
                                    موردی برای نمایش وجود ندارد !!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="div3">
                <h2 class="section_heading">آخرین استعلام ها</h2>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>مشتری</th>
                                <th>اطلاعات استعلام</th>
                                <th>کاربر</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql2 = "SELECT  customer.name, customer.family, customer.phone, record.id as recordID, record.time, record.callinfo, record.pin, users.id AS userID
                            FROM ((callcenter.record
                            INNER JOIN callcenter.customer ON record.phone = customer.phone)
                            INNER JOIN yadakshop.users ON record.user = users.id)
                            WHERE record.pin = 'pin'
                            ORDER BY record.time DESC
                            LIMIT 40";
                            $stmt = PDO_CONNECTION->prepare($sql2);
                            $stmt->execute();
                            $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result2)) :
                                foreach ($result2 as $row2) :
                                    $recordID = $row2['recordID'];
                                    $time = $row2['time'];
                                    $callinfo = $row2['callinfo'];
                                    $user = $row2['userID'];
                                    $phone = $row2['phone'];
                                    $name = $row2['name'];
                                    $family = $row2['family']; ?>
                                    <tr class="pin">
                                        <td><?= ($name . " " . $family) ?></td>
                                        <td><?= nl2br($callinfo) ?></td>
                                        <td class="pic"><img class="user-img" src="../../public/userimg/<?= $user ?>.jpg" /></td>
                                    </tr>
                                <?php
                                endforeach;
                            endif;
                            $sql2 = "SELECT customer.name, customer.family, customer.phone, record.id as recordID, record.time, record.callinfo, record.pin, users.id AS userID
                                        FROM ((callcenter.record
                                        INNER JOIN callcenter.customer ON record.phone = customer.phone)
                                        INNER JOIN yadakshop.users ON record.user = users.id)
                                        WHERE record.pin = 'unpin'
                                        ORDER BY record.time DESC
                                        LIMIT 40";
                            $stmt = PDO_CONNECTION->prepare($sql2);
                            $stmt->execute();
                            $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (count($result2)) :
                                foreach ($result2 as $row2) :
                                    $recordID = $row2['recordID'];
                                    $time = $row2['time'];
                                    $callinfo = $row2['callinfo'];
                                    $user = $row2['userID'];
                                    $phone = $row2['phone'];
                                    $name = $row2['name'];
                                    $family = $row2['family']; ?>
                                    <tr>
                                        <td><?= ($name . " " . $family) ?></td>
                                        <td><?= nl2br($callinfo) ?></td>
                                        <td> <img class="user-img" src="../../public/userimg/<?= $user ?>.jpg" /></td>
                                    </tr>
                            <?php
                                endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
else : ?>
    <style>
        body,
        html {
            height: 100vh !important;
            padding: 0 !important;
            margin: 0 !important;
            background: url('./assets/img/tv-wallpaper-2.jpg')no-repeat center;
            background-size: cover !important;
        }
    </style>
<?php
endif;

<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';
require_once '../../../utilities/callcenter/DollarRateHelper.php';
require_once '../../../utilities/jdf.php';

if (filter_has_var(INPUT_POST, 'operation')) {
    $toBeDelete = filter_input(INPUT_POST, 'toBeDelete', FILTER_SANITIZE_NUMBER_INT);

    $sql = "DELETE FROM callcenter.estelam WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindValue(':id', $toBeDelete);
    if ($stmt->execute()) {
        echo true;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

if (filter_has_var(INPUT_POST, 'editOperation')) {
    $toBeEdited = filter_input(INPUT_POST, 'toBeEdited', FILTER_SANITIZE_NUMBER_INT);
    $price = htmlspecialchars($_POST['price']);

    $sql = "UPDATE callcenter.estelam SET price = :price WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':id', $toBeEdited);
    if ($stmt->execute()) {
        echo true;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

if (filter_has_var(INPUT_POST, 'pattern')) :
    $pattern = $_POST['pattern'];

    $sql = "SELECT 
            estelam.*, 
            users.id AS user_id, 
            users.name,
            users.family,
            seller.name AS seller_name
        FROM 
            callcenter.estelam
        JOIN 
            users ON estelam.user = users.id
        JOIN 
            seller ON estelam.seller = seller.id
        WHERE 
            LOWER(REPLACE(estelam.codename, ' ', '')) LIKE CONCAT('', LOWER(REPLACE(:pattern, ' ', '')), '%')
            OR LOWER(REPLACE(seller.name, ' ', '')) LIKE CONCAT('%', LOWER(REPLACE(:pattern, ' ', '')), '%')
        ORDER BY 
            estelam.time DESC
        LIMIT 
            300";

    // Prepare the statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Bind the value to the :pattern placeholder
    $stmt->bindValue(':pattern', $pattern);

    // Execute the query
    $stmt->execute();

    // Fetch the results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentGroup = null;
    $bgColors = ['rgb(254 243 199)', 'rgb(220 252 231)']; // Array of background colors for date groups
    $bgColorIndex = 0;

    foreach ($results as $row) :
        $id = $row['id'];
        $time = $row['time'];
        $partNumber = $row['codename'];
        $sellerName = $row['seller_name'];
        $price = $row['price'];
        $userId = $row['user_id'];

        // Explode the time value to separate date and time
        $dateTime = explode(' ', $time);
        $date = $dateTime[0];

        // Check if the group has changed
        if ($date !== $currentGroup) :
            // Update the current group
            $currentGroup = $date;

            // Get the background color for the current group
            $bgColor = $bgColors[$bgColorIndex % count($bgColors)];
            $bgColorIndex++; ?>
            <tr class="bg-sky-800">
                <td class="text-white font-semibold p-3" colspan="6"><?= displayTimePassed($time) . ' - ' . jdate('Y/m/d', strtotime($time)) ?></td>
            </tr>
        <?php
        endif; ?>
        <tr id="row-<?= $id ?>" style="background-color:<?= $bgColor ?>">
            <td class="text-md font-semibold p-3 hover:cursor-pointer text-blue-400 uppercase" onclick="searchByCustomer(this)" data-customer='<?= $partNumber ?>'><?= $partNumber ?></td>
            <td class="text-md font-semibold p-3 hover:cursor-pointer text-blue-400" onclick="searchByCustomer(this)" data-customer='<?= $sellerName ?>'><?= $sellerName ?></td>
            <td class="text-md font-semibold p-3" id="price-<?= $id ?>"><?= $price ?></td>
            <td>
                <?php
                $profile = "../../public/userimg/" . $row['user_id'] . ".jpg";
                if (!file_exists("../" . $profile)) {
                    $profile = "../../public/userimg/default.png";
                }
                ?>
                <img title="<?= $row['name'] . ' ' . $row['family'] ?>" class="w-8 h-8 rounded-full" src="<?= $profile ?>" alt="user profile" />

            </td>
            <td class="text-md font-semibold p-3">
                <p class="text-sm text-center font-semibold p-3" style="direction: ltr !important;">
                    <?= jdate('Y/m/d H:i', strtotime($time)); ?>
                </p>
            </td>
            <td>
                <i onclick="editItem(this)" data-price="<?= $price ?>" data-item='<?= $id ?>' class="material-icons hover:cursor-pointer text-indigo-600">edit</i>
                <i onclick="deleteItem(this)" data-item='<?= $id ?>' class="material-icons hover:cursor-pointer text-red-600">delete</i>
            </td>
        </tr>
<?php
    endforeach;
endif;

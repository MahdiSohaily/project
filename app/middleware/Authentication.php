<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}
function isLogin()
{
    if (isset($_SESSION["isLogin"]) && $_SESSION["isLogin"] != true) {
        return false; // retune false if the successful login is not set
    }

    if (isLoginSessionExpired()) {
        return false;
    }

    if(isset($_SESSION["isLogin"]) && authModified($_SESSION["id"])) {
        return false;
    }

    if (isAccessLevelSet()) {
        return false;
    }

    return true;
}

function isLoginSessionExpired()
{
    // Check if the session has expired (current time > expiration time)
    if (isset($_SESSION["expiration_time"]) && time() > $_SESSION["expiration_time"]) {
        return true;
    }
    return false;
}

function authModified($id)
{
    $stmt = PDO_CONNECTION->prepare("SELECT modified FROM yadakshop.authorities WHERE user_id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if result is not empty to avoid errors
    if ($result) {
        return $result['modified'];
    } else {
        return false; // or handle the case where no data is found for the given user ID
    }
}

function isAccessLevelSet()
{
    if (!isset($_SESSION["not_allowed"])) {
        return true;
    }
    return false;
}

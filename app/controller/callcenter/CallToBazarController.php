<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$allSellers = getSellers();

function getSellers()
{
    $sql = "SELECT id , name FROM seller WHERE view IS NULL AND phone IS NOT NULL AND phone !='' ORDER  BY sort";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

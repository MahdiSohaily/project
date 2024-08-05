<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['deleteContact'])) {
    $id = $_POST['id'];
    echo deleteContact($id);
}

function deleteContact($id)
{
    $sql = "DELETE FROM telegram.receiver WHERE id = :id";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':id', $id);
    $result = $statement->execute();
    if ($result) {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST['addContact'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $profile = $_POST['profile'];
    $chat_id = $_POST['chat_id'];
    addContact($name, $username, $chat_id, $profile);
}

function addContact($name, $username, $chat_id, $profile)
{
    $sql = "SELECT COUNT(chat_id) AS total FROM telegram.receiver WHERE chat_id = :chat_id";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':chat_id', $chat_id);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC)['total'];

    if (!$result) {
        $addSql = "INSERT INTO telegram.receiver (cat_id, chat_id, name, username, profile) VALUES 
                    ('1', :chat_id , :name , :username , :profile)";
        $statement = PDO_CONNECTION->prepare($addSql);
        $statement->bindParam(':chat_id', $chat_id);
        $statement->bindParam(':name', $name);
        $statement->bindParam(':username', $username);
        $statement->bindParam(':profile', $profile);
        $status = $statement->execute();
        if ($status) {
            echo 'true';
        } else {
            echo 'false';
        }
    } else {
        echo 'exist';
    }
}

if (isset($_POST['addAllContact'])) {

    $contacts = json_decode($_POST['contacts']);

    foreach ($contacts as $contact) {
        addAllContacts($contact);
    }

    echo true;
}

function addAllContacts($contact)
{
    $chat_id = $contact->id;
    $name = $contact->first_name ?? '';
    $lastName = $contact->last_name ?? '';

    $clientName = trim($name . ' ' . $lastName);
    $username = $contact->username ?? '';
    $profile = '$contact->profile';

    $sql = "SELECT COUNT(chat_id) AS total FROM telegram.receiver WHERE chat_id = :chat_id";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':chat_id', $chat_id);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC)['total'];

    if (!$result) {
        $addSql = "INSERT INTO telegram.receiver (cat_id, chat_id, name, username, profile) VALUES 
                    ('1', :chat_id , :name, :username, :profile)";
        $statement = PDO_CONNECTION->prepare($addSql);
        $statement->bindParam(':chat_id', $chat_id);
        $statement->bindParam(':name', $clientName);
        $statement->bindParam(':username', $username);
        $statement->bindParam(':profile', $profile);
        $status = $statement->execute();
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
}

if (isset($_POST['getPartialContacts'])) {
    $page = $_POST['page'];

    header('Content-Type: application/json');
    echo getPartialContacts($page);
}

function getPartialContacts($page)
{
    $offset = ($page - 1) * 50;
    $sql = "SELECT * FROM telegram.receiver LIMIT 50 OFFSET :offset";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    $contacts = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($contacts);
}

if (isset($_POST['getContactsCount'])) {

    header('Content-Type: application/json');
    echo getContactsCount();
}

function getContactsCount()
{
    $sql = "SELECT COUNT(id) AS total FROM telegram.receiver";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->execute();
    $contacts = $statement->fetch(PDO::FETCH_ASSOC);
    return json_encode($contacts['total']);
}


if (isset($_POST['saveConversation'])) {
    $receiver = $_POST['receiver'];
    $request = $_POST['request'];
    $response = $_POST['response'];

    header('Content-Type: application/json');
    echo saveConversation($receiver, $request, $response);
}


function saveConversation($receiver, $request, $response)
{
    // Prepare the SQL statement
    $sql = "INSERT INTO telegram.messages (receiver, request, response) VALUES (:receiver, :request , :response)";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':receiver', $receiver);
    $statement->bindParam(':request', $request);
    $statement->bindParam(':response', $response);
    // Check if the insertion was successful
    if ($statement->execute()) {
        return true; // Conversation saved successfully
    } else {
        return false; // Failed to save conversation
    }
}


if (isset($_POST['searchContact'])) {
    $pattern = $_POST['pattern'];

    header('Content-Type: application/json');
    echo searchContact($pattern);
}

function searchContact($pattern)
{
    $sql = "SELECT * FROM telegram.receiver WHERE name LIKE :pattern OR username LIKE :pattern";
    $statement = PDO_CONNECTION->prepare($sql);
    $pattern = '%' . $pattern . '%';
    $statement->bindParam(':pattern', $pattern);
    $statement->execute();
    $contacts = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($contacts);
}

if (isset($_POST['toggleStatus'])) {
    $status = $_POST['status'];

    header('Content-Type: application/json');
    echo toggleStatus();
}

function toggleStatus()
{
    $sql = "SELECT * FROM telegram.receiver_cat WHERE id = 1";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->execute();
    $status = $statement->fetch(PDO::FETCH_ASSOC)['status'];

    if ($status == 1) {
        $sql = "UPDATE telegram.receiver_cat SET status = 0 WHERE id = 1";
        $statement = PDO_CONNECTION->prepare($sql);
        $result = $statement->execute();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    } else {
        $sql = "UPDATE telegram.receiver_cat SET status = 1 WHERE id = 1";
        $statement = PDO_CONNECTION->prepare($sql);
        $result = $statement->execute();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
}

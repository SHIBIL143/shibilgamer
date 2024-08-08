<?php

$botToken = "7242978741:AAESuACxrwKFDZiAB5kFm0tNt5U4aIO-slM"; // Replace with your bot token
$apiUrl = "https://api.telegram.org/bot$botToken/";

$update = file_get_contents("php://input");
$update = json_decode($update, TRUE);

$chatId = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];
$fileId = isset($update["message"]["document"]["file_id"]) ? $update["message"]["document"]["file_id"] : null;

if (isset($text)) {
    if (strtolower($text) == "/start") {
        sendMessage($chatId, "Send me a file and I'll convert it for you!");
    } else {
        sendMessage($chatId, "Please send a file to convert.");
    }
}

if ($fileId) {
    $fileUrl = getFileUrl($fileId);
    $fileContent = file_get_contents($fileUrl);
    
    // Convert the file (this is just an example, replace with your own conversion logic)
    $convertedFile = convertFile($fileContent);

    // Send converted file back to user
    sendDocument($chatId, $convertedFile);
}

function sendMessage($chatId, $text) {
    global $apiUrl;
    $url = $apiUrl . "sendMessage?chat_id=$chatId&text=" . urlencode($text);
    file_get_contents($url);
}

function sendDocument($chatId, $fileContent) {
    global $apiUrl;
    $url = $apiUrl . "sendDocument";
    $postFields = [
        'chat_id' => $chatId,
        'document' => new CURLFile($fileContent)
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}

function getFileUrl($fileId) {
    global $apiUrl;
    $url = $apiUrl . "getFile?file_id=$fileId";
    $response = file_get_contents($url);
    $response = json_decode($response, TRUE);
    return "https://api.telegram.org/file/bot" . $GLOBALS['botToken'] . "/" . $response["result"]["file_path"];
}

function convertFile($fileContent) {
    // Example conversion: just save the file with a new name
    $convertedFile = 'converted_file.ext'; // Replace with your logic
    file_put_contents($convertedFile, $fileContent);
    return $convertedFile;
}

?>

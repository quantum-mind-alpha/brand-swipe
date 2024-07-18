<?php

$apiToken = "7328905715:AAFU-FLZfoTz5To-pwr0QRybmSBIApS1jgo";
$website = "https://api.telegram.org/bot".$apiToken;

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

switch($message) {
    case "/start":
        sendMessage($chatId, "Hi! Welcome to our bot.");
        break;
    case "/help":
        sendMessage($chatId, "How can I help you?");
        break;
    default:
        sendMessage($chatId, "You said: " . $message);
        break;
}

function sendMessage($chatId, $message) {
    global $website;
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

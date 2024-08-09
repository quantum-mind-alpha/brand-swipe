<?php

$botToken = "7328905715:AAFU-FLZfoTz5To-pwr0QRybmSBIApS1jgo";
$website = "https://api.telegram.org/bot" . $botToken;

// Get the incoming update
$update = file_get_contents("php://input");
$updateArray = json_decode($update, TRUE);

$chatId = $updateArray["message"]["chat"]["id"];
$message = $updateArray["message"]["text"];

// Define the keyboard with a button linking to the mini app
$keyboard = [
    "inline_keyboard" => [
        [
            ["text" => "Open Mini App", "url" => "https://dolphin-app-wkr7w.ondigitalocean.app/index.html"]
        ]
    ]
];

switch ($message) {
    case "/start":
        sendMessage($chatId, "Click the button below to open the mini app:", $keyboard);
        break;
    default:
        sendMessage($chatId, "I don't understand that command.");
        break;
}

function sendMessage($chatId, $message, $keyboard = null)
{
    global $website;

    $encodedKeyboard = json_encode($keyboard);
    $url = $website . "/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($message) . "&reply_markup=" . $encodedKeyboard;

    file_get_contents($url);
}
?>

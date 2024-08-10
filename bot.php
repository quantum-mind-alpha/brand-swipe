<?php

$botToken = "7328905715:AAEDW2xDVDasPJ5-Kdti9ND_pQBK77VHlf8";
$website = "https://api.telegram.org/bot" . $botToken;

// Get the incoming update
$update = file_get_contents("php://input");
$updateArray = json_decode($update, TRUE);

$chatId = $updateArray["message"]["chat"]["id"];
$message = $updateArray["message"]["text"];

// Define the keyboard with a button that launches the Telegram Web App
$keyboard = [
    "inline_keyboard" => [
        [
            [
                "text" => "Start",
                "web_app" => ["url" => "https://dolphin-app-wkr7w.ondigitalocean.app/index.html"]
            ]
        ]
    ]
];

switch ($message) {
    case "/start":
        sendMessage($chatId, "Click the button below to start:", $keyboard);
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

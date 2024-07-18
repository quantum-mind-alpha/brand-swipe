<?php

$apiToken = "YOUR_TELEGRAM_BOT_TOKEN";
$website = "https://api.telegram.org/bot".$apiToken;

// Sample card data
$cards = [
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/dominos.jpeg", "name" => "Domino's"],
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/pizzahut.jpeg", "name" => "Pizza Hut"],
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/lapinoz.jpeg", "name" => "La Pinoz"],
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/ovenstory.jpeg", "name" => "Oven Story"]
];

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];

    if ($message == "/start") {
        clearMessages($chatId);
        sendCard($chatId, 0);
    }
}

if (isset($update["callback_query"])) {
    $callbackQuery = $update["callback_query"];
    $chatId = $callbackQuery["message"]["chat"]["id"];
    $messageId = $callbackQuery["message"]["message_id"];
    $data = $callbackQuery["data"];

    $parts = explode("_", $data);
    $action = $parts[0];
    $currentIndex = (int)$parts[1];

    deleteMessage($chatId, $messageId);

    $nextIndex = $currentIndex + 1;
        if ($nextIndex < count($cards)) {
            sendCard($chatId, $nextIndex);
        } else {
            sendEndMessage($chatId);
        }
}

function clearMessages($chatId) {
    global $website;
    $updates = file_get_contents($website."/getUpdates");
    $updates = json_decode($updates, TRUE);

    if ($updates["ok"]) {
        foreach ($updates["result"] as $update) {
            if (isset($update["message"]["chat"]["id"]) && $update["message"]["chat"]["id"] == $chatId) {
                $messageId = $update["message"]["message_id"];
                deleteMessage($chatId, $messageId);
            }
            if (isset($update["callback_query"]["message"]["chat"]["id"]) && $update["callback_query"]["message"]["chat"]["id"] == $chatId) {
                $messageId = $update["callback_query"]["message"]["message_id"];
                deleteMessage($chatId, $messageId);
            }
        }
    }
}

function sendCard($chatId, $index) {
    global $website, $cards;

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => "❤️", 'callback_data' => "like_$index"],
                ['text' => "👎", 'callback_data' => "dislike_$index"]
            ]
        ]
    ];

    $encodedKeyboard = json_encode($keyboard);

    $image = $cards[$index]["image"];
    $name = $cards[$index]["name"];
    $message = "<a href=\"$image\">&#8205;</a>$name";  // The zero-width space is needed to display the image

    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message)."&parse_mode=HTML&reply_markup=".$encodedKeyboard;
    file_get_contents($url);
}

function sendConfetti($chatId) {
    global $website;
    $message = "🎉 Congratulations! 🎉";
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

function deleteMessage($chatId, $messageId) {
    global $website;
    $url = $website."/deleteMessage?chat_id=".$chatId."&message_id=".$messageId;
    file_get_contents($url);
}

function sendEndMessage($chatId) {
    global $website;
    $message = "No more businesses to show.";
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

?>

<?php

$apiToken = "7328905715:AAFU-FLZfoTz5To-pwr0QRybmSBIApS1jgo";
$website = "https://api.telegram.org/bot".$apiToken;

// Sample card data
$cards = [
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/dominos.jpeg?size=200x200", "name" => "Domino's"],
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/pizzahut.jpeg?size=200x200", "name" => "Pizza Hut"],
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/lapinoz.jpeg?size=200x200", "name" => "La Pinoz"],
    ["image" => "https://seal-app-asu6p.ondigitalocean.app/images/ovenstory.jpeg?size=200x200", "name" => "Oven Story"]
];

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];

    if ($message == "/start") {
        sendWelcomeMessage($chatId);
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

    if ($action == "start") {
        sendCard($chatId, 0);
    } elseif ($action == "like" || $action == "dislike") {
        $nextIndex = $currentIndex + 1;
        if ($nextIndex < count($cards)) {
            sendCard($chatId, $nextIndex);
        } else {
            sendEndMessage($chatId);
        }
    }
}

function sendWelcomeMessage($chatId) {
    global $website;

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => "Start", 'callback_data' => "start_0"]
            ]
        ]
    ];

    $encodedKeyboard = json_encode($keyboard);
    $message = "Press Start to see today's Businesses";

    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message)."&reply_markup=".$encodedKeyboard;
    file_get_contents($url);
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

function deleteMessage($chatId, $messageId) {
    global $website;
    $url = $website."/deleteMessage?chat_id=".$chatId."&message_id=".$messageId;
    file_get_contents($url);
}

function sendEndMessage($chatId) {
    global $website;
    $message = "That's all for today! For new businesses, check back tomorrow.";
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

?>

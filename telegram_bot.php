<?php

$apiToken = "7328905715:AAFU-FLZfoTz5To-pwr0QRybmSBIApS1jgo";
$website = "https://api.telegram.org/bot".$apiToken;

// Sample card data
$cards = [
    "Card 1: This is the first card.",
    "Card 2: This is the second card.",
    "Card 3: This is the third card.",
    "Card 4: This is the fourth card."
];

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];

    if ($message == "/start") {
        sendCard($chatId, 0);
    }
}

if (isset($update["callback_query"])) {
    $callbackQuery = $update["callback_query"];
    $chatId = $callbackQuery["message"]["chat"]["id"];
    $data = $callbackQuery["data"];

    $parts = explode("_", $data);
    $action = $parts[0];
    $currentIndex = (int)$parts[1];

    if ($action == "next") {
        $nextIndex = $currentIndex + 1;
        if ($nextIndex < count($cards)) {
            sendCard($chatId, $nextIndex);
        }
    } elseif ($action == "prev") {
        $prevIndex = $currentIndex - 1;
        if ($prevIndex >= 0) {
            sendCard($chatId, $prevIndex);
        }
    }
}

function sendCard($chatId, $index) {
    global $website, $cards;

    $keyboard = [
        'inline_keyboard' => []
    ];

    if ($index > 0) {
        $keyboard['inline_keyboard'][] = [['text' => "Previous", 'callback_data' => "prev_$index"]];
    }

    if ($index < count($cards) - 1) {
        $keyboard['inline_keyboard'][] = [['text' => "Next", 'callback_data' => "next_$index"]];
    }

    $encodedKeyboard = json_encode($keyboard);

    $message = $cards[$index];
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message)."&reply_markup=".$encodedKeyboard;
    file_get_contents($url);
}

?>

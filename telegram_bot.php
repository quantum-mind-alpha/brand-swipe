<?php

$apiToken = "7328905715:AAFU-FLZfoTz5To-pwr0QRybmSBIApS1jgo";
$website = "https://api.telegram.org/bot".$apiToken;

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];

    if ($message == "/start") {
        sendWelcomeMessage($chatId);
    } elseif ($message == "/cards") {
        sendCards($chatId);
    }
}

function sendWelcomeMessage($chatId) {
    global $website;
    $message = "Welcome! Use /cards to see the available cards.";
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

function sendCards($chatId) {
    global $website;
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => "Card 1", 'callback_data' => 'CARD_1'],
                ['text' => "Card 2", 'callback_data' => 'CARD_2']
            ],
            [
                ['text' => "Card 3", 'callback_data' => 'CARD_3'],
                ['text' => "Card 4", 'callback_data' => 'CARD_4']
            ]
        ]
    ];

    $encodedKeyboard = json_encode($keyboard);

    $message = "Choose a card:";
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message)."&reply_markup=".$encodedKeyboard;
    file_get_contents($url);
}

// Handle callback queries
if (isset($update["callback_query"])) {
    $callbackQuery = $update["callback_query"];
    $chatId = $callbackQuery["message"]["chat"]["id"];
    $data = $callbackQuery["data"];

    handleCardSelection($chatId, $data);
}

function handleCardSelection($chatId, $data) {
    global $website;
    $message = "You selected: " . $data;
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

?>

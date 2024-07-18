<?php

$apiToken = "7328905715:AAFU-FLZfoTz5To-pwr0QRybmSBIApS1jgo";
$website = "https://api.telegram.org/bot".$apiToken;

// Sample card data
$cards = [
    ["image" => "https://via.placeholder.com/150", "name" => "Business 1"],
    ["image" => "https://via.placeholder.com/150", "name" => "Business 2"],
    ["image" => "https://via.placeholder.com/150", "name" => "Business 3"],
    ["image" => "https://via.placeholder.com/150", "name" => "Business 4"]
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

    if ($action == "like") {
        sendConfetti($chatId);
    } elseif ($action == "dislike") {
        $nextIndex = $currentIndex + 1;
        if ($nextIndex < count($cards)) {
            sendCard($chatId, $nextIndex);
        } else {
            sendEndMessage($chatId);
        }
    }
}

function sendCard($chatId, $index) {
    global $website, $cards;

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => "👍", 'callback_data' => "like_$index"],
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

function sendEndMessage($chatId) {
    global $website;
    $message = "No more businesses to show.";
    $url = $website."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
    file_get_contents($url);
}

?>

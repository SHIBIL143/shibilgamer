<?php
$telegramToken = '7282716690:AAEE8pt3tiE2gmtMWzDSxLR5S7S1c2TJMAM';
$apiURL = "https://api.telegram.org/bot$telegramToken/";
$databaseFile = 'database.txt';


$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

if (isset($update['message']['document'])) {
    handleFileUpload($update['message'], 'document');
} elseif (isset($update['message']['photo'])) {
    handleFileUpload($update['message'], 'photo');
} elseif (isset($update['message']['video'])) {
    handleFileUpload($update['message'], 'video');
} elseif (isset($update['message']['audio'])) {
    handleFileUpload($update['message'], 'audio');
} elseif (isset($update['message']['sticker'])) {
    handleFileUpload($update['message'], 'sticker');
} elseif (isset($update['message']['text'])) {
    handleTextMessage($update['message']);
}

function handleFileUpload($message, $type) {
    global $apiURL;

    $chatId = $message['chat']['id'];
    $fileId = '';
    $fileName = '';

    switch ($type) {
        case 'document':
            $fileId = $message['document']['file_id'];
            $fileName = $message['document']['file_name'];
            break;
        case 'photo':
            $fileId = end($message['photo'])['file_id']; // Get the highest resolution photo
            $fileName = "photo_" . uniqid() . ".jpg";
            break;
        case 'video':
            $fileId = $message['video']['file_id'];
            $fileName = $message['video']['file_name'];
            break;
        case 'audio':
            $fileId = $message['audio']['file_id'];
            $fileName = $message['audio']['file_name'];
            break;
        case 'sticker':
            $fileId = $message['sticker']['file_id'];
            $fileName = "sticker_" . uniqid() . ".webp";
            break;
    }

    $caption = isset($message['caption']) ? $message['caption'] : '';

    // Check if there is an existing link ID in the user's message history or create a new one
    $linkId = getOrCreateLinkId($chatId);

    // Save file info to the database
    saveFileInfo($fileId, $fileName, $type, $caption, $linkId);

    // Send confirmation message to the user
    file_get_contents($apiURL . "sendMessage?chat_id=$chatId&text=File uploaded. Send /finish after sending all files.");
}

function handleTextMessage($message) {
    global $apiURL;

    $chatId = $message['chat']['id'];
    $text = $message['text'];

    if ($text == '/start') {
        file_get_contents($apiURL . "sendMessage?chat_id=$chatId&text=Send me any content to share!");
    } elseif (strpos($text, '/start') === 0) {
        // Extract link ID from the start command
        $linkId = substr($text, 7);
        sendFilesFromLink($chatId, $linkId);
    } elseif ($text == '/finish') {
        // Finish the upload session
        $linkId = finishUploadSession($chatId);
        if ($linkId) {
            $shareableLink = "https://t.me/YOURBOTUSERNAME?start=$linkId";
            file_get_contents($apiURL . "sendMessage?chat_id=$chatId&text=Shareable link: $shareableLink");
        }
    }
}

function getOrCreateLinkId($chatId) {
    global $databaseFile;

    $database = json_decode(file_get_contents($databaseFile), true);

    // Check if there is an active link ID for the chat
    foreach ($database as $entry) {
        if ($entry['chat_id'] == $chatId && $entry['active']) {
            return $entry['link_id'];
        }
    }

    // Create a new link ID if none exists
    $linkId = uniqid();
    $database[] = [
        'link_id' => $linkId,
        'chat_id' => $chatId,
        'active' => true,
        'files' => []
    ];
    file_put_contents($databaseFile, json_encode($database));

    return $linkId;
}

function saveFileInfo($fileId, $fileName, $type, $caption, $linkId) {
    global $databaseFile;

    $database = json_decode(file_get_contents($databaseFile), true);

    foreach ($database as &$entry) {
        if ($entry['link_id'] == $linkId) {
            $entry['files'][] = [
                'file_id' => $fileId,
                'file_name' => $fileName,
                'type' => $type,
                'caption' => $caption
            ];
            break;
        }
    }

    file_put_contents($databaseFile, json_encode($database));
}

function finishUploadSession($chatId) {
    global $databaseFile;

    $database = json_decode(file_get_contents($databaseFile), true);

    foreach ($database as &$entry) {
        if ($entry['chat_id'] == $chatId && $entry['active']) {
            $entry['active'] = false;
            file_put_contents($databaseFile, json_encode($database));
            return $entry['link_id'];
        }
    }

    return false;
}

function sendFilesFromLink($chatId, $linkId) {
    global $databaseFile, $apiURL;

    $database = json_decode(file_get_contents($databaseFile), true);

    foreach ($database as $entry) {
        if ($entry['link_id'] == $linkId) {
            foreach ($entry['files'] as $file) {
                $params = [
                    'chat_id' => $chatId,
                    $file['type'] => $file['file_id']
                ];
                if (!empty($file['caption'])) {
                    $params['caption'] = $file['caption'];
                }

                $url = $apiURL . "send" . ucfirst($file['type']);
                file_get_contents($url . '?' . http_build_query($params));
            }
            break;
        }
    }
}
?>

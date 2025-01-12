<?php

include $_SERVER['DOCUMENT_ROOT'].'/init.php'; // 引入文件

$jsonFile = $GLOBALS['ffmpeg-setting'];

// 读取 JSON 文件内容
function readJsonFile() {
    global $jsonFile;
    if (!file_exists($jsonFile)) {
        return [];
    }
    $data = file_get_contents($jsonFile);
    return json_decode($data, true);
}

// 查：根据 ID 查找项
function getItem($id) {
    $data = readJsonFile();
    foreach ($data as $item) {
        if ($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}




?>

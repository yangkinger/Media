<?php


include $_SERVER['DOCUMENT_ROOT'].'/action/function.php'; // 引入文件


// 写入 JSON 数据到文件
function writeJsonFile($data) {
    global $jsonFile;
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// 增：添加新项
function addItem($name, $value) {
    $data = readJsonFile();
    $newId = count($data) > 0 ? max(array_column($data, 'id')) + 1 : 1;  // 获取新的 ID
    $data[] = ["id" => $newId, "name" => $name, "value" => $value];
    writeJsonFile($data);
}

// 删：根据 ID 删除项
function deleteItem($id) {
    $data = readJsonFile();
    $data = array_filter($data, function($item) use ($id) {
        return $item['id'] != $id;
    });
    $data = array_values($data);  // 重置数组索引
    writeJsonFile($data);
}

// 获取所有配置项
function getAllItems() {
    return readJsonFile();
}


// 改：根据 ID 更新项
function updateItem($id, $name, $value) {
    $data = readJsonFile();
    foreach ($data as &$item) {
        if ($item['id'] == $id) {
            $item['name'] = $name;
            $item['value'] = $value;
        }
    }
    writeJsonFile($data);
}

// 获取 POST 请求的数据
$action = $_POST['action'] ?? '';  // 操作类型：add, delete, get, update
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$value = $_POST['value'] ?? null;

// 根据 action 进行不同操作
switch ($action) {
    case 'add':
        if ($name && $value) {
            addItem($name, $value);
            echo json_encode(['status' => 'success', 'message' => 'Item added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Name and value are required']);
        }
        break;

    case 'delete':
        if ($id) {
            deleteItem($id);
            echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        }
        break;

    case 'get':
        if ($id) {
            $item = getItem($id);
            if ($item) {
                echo json_encode(['status' => 'success', 'item' => $item]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Item not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        }
        break;
        
    case 'getAll':
        $data = getAllItems();
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'update':
        if ($id && $name && $value) {
            updateItem($id, $name, $value);
            echo json_encode(['status' => 'success', 'message' => 'Item updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID, name, and value are required']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

?>

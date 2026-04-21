<?php
// $argv[0] คือชื่อไฟล์ (index.php)
// $grgv[1] คือคำสั่งที่ผู้ใช้พิมพ์ (เช่น add, list)

// if (isset($argv[1])) {
//     echo "คำสั่งของคุณคือ: " . $argv[1];
// } else {
//     echo "กรุณาใส่คำสั่งด้วยครับ เช่น: php index.php list";
// }

$fileName = 'task.json';

//1. ตรวจสอบวและเตรียมไฟล์ JSON
if (!file_exists($fileName)) {
    file_put_contents($fileName, json_encode([]));
}

//2. รับคำสั่งหลัก (add, update, delete, list)
$action = $argv[1] ?? null;

if ($action === 'add') {

    //รับคำอธิบาย Task จาก $argv[2] เช่น: php index.php add "ซักผ้า"
    $description = $argv[2] ?? 'No description';

    //อ่านข้อมูลเดิม
    $tasks = json_decode(file_get_contents($fileName), true);

    if (!is_array($tasks)) {
        $tasks = [];
    }

    //สร้าง Task ใหม่
    $maxId = 0;
    foreach ($tasks as $task) {
        if ($task['id'] > $maxId) {
            $maxId = $task['id'];
        }
    }
    $newId = $maxId + 1;

    $newTask = [
        "id" => $newId,
        "description" => $description,
        "status" => 'todo',
        "createdAt" => date("Y-m-d H:i:s"),
        "updatedAt" => date("Y-m-d H:i:s"),
    ];

    // เพิ่มเข้า Array และบันทึก
    $tasks[] = $newTask;
    file_put_contents($fileName, json_encode($tasks, JSON_PRETTY_PRINT));

    echo "เพิ่มงานเรียบร้อยแล้ว! (ID: {$newTask['id']})\n";
} elseif ($action === 'list') {
    //แสดงรายการทั้งหมด
    $filterStatus = $argv[2] ?? null;
    $jsonContent = file_get_contents($fileName);
    $tasks = json_decode($jsonContent, true);
    //ตรวจสอบว่ามีข้อมูลไหม
    if (empty($tasks)) {
        echo "ไม่มีรายการในขณะนี้\n";
    } else {
        foreach ($tasks as $task) {
            if ($filterStatus === null || $task['status'] === $filterStatus) {
                echo "[{$task['id']}] {$task['description']} ({$task['status']})\n";
            }
        } //foreach
    } //else
} elseif ($action === 'delete') {
    $idToDelete = $argv[2] ?? null;
    $tasks = json_decode(file_get_contents($fileName), true);

    $newtasks = [];
    foreach ($tasks as $task) {
        if ($task['id'] != $idToDelete) {
            $newtasks[] = $task;
        }
    }
    file_put_contents($fileName, json_encode($newtasks, JSON_PRETTY_PRINT));
    echo "ลบรายการ ID: $idToDelete เรียบร้อยแล้ว\n";
} elseif ($action === 'mark-done') {
    $idToUpdate = $argv[2] ?? null;
    $tasks = json_decode(file_get_contents($fileName), true);
    $found = false;

    foreach ($tasks as &$task) {
        if ($task['id'] == $idToUpdate) {
            $task['status'] = 'done';
            $task['updatedAt'] = date("Y-m-d H:i:s");
            $found = true;
            break;
        }
    }
    if ($found) {
        file_put_contents($fileName, json_encode($tasks, JSON_PRETTY_PRINT));
        echo "อัปเดตรายการ ID: $idToUpdate เป็น done เรียบร้อยแล้ว\n";
    } else {
        echo "ไม่พบรายการ ID: $idToUpdate\n";
    }
} elseif ($action === 'mark-todo') {
    $idToUpdate = $argv[2] ?? null;
    $tasks = json_decode(file_get_contents($fileName), true);
    $found = false;

    foreach ($tasks as &$task) {
        if ($task['id'] == $idToUpdate) {
            $task['status'] = 'todo';
            $task['updatedAt'] = date("Y-m-d H:i:s");
            $found = true;
            break;
        }
    }
    if ($found) {
        file_put_contents($fileName, json_encode($tasks, JSON_PRETTY_PRINT));
        echo "อัปเดตรายการ ID: $idToUpdate เป็น todo เรียบร้อยแล้ว\n";
    } else {
        echo "ไม่พบรายการ ID: $idToUpdate\n";
    }
} elseif ($action === 'mark-in-progress') {
    $idToUpdate = $argv[2] ?? null;
    $tasks = json_decode(file_get_contents($fileName), true);
    $found = false;

    foreach ($tasks as &$task) {
        if ($task['id'] == $idToUpdate) {
            $task['status'] = 'in-progress';
            $task['updatedAt'] = date("Y-m-d H:i:s");
            $found = true;
            break;
        }
    }
    if ($found) {
        file_put_contents($fileName, json_encode($tasks, JSON_PRETTY_PRINT));
        echo "อัปเดตรายการ ID: $idToUpdate เป็น in-progress เรียบร้อยแล้ว\n";
    } else {
        echo "ไม่พบรายการ ID: $idToUpdate\n";
    }
} elseif ($action === 'update') {
    $idToUpdate = $argv[2] ?? null;
    $newDescription = $argv[3] ?? null;
    $tasks = json_decode(file_get_contents($fileName), true);
    $found = false;

    foreach ($tasks as &$task) {
        if ($task['id'] == $idToUpdate) {
            $task['description'] = $newDescription;
            $task['updatedAt'] = date("Y-m-d H:i:s");
            $found = true;
            break;
        }
    }
    if ($found) {
        file_put_contents($fileName, json_encode($tasks, JSON_PRETTY_PRINT));
        echo "อัปเดต Description ของรายการ ID: $idToUpdate เรียบร้อยแล้ว\n";
    } else {
        echo "ไม่พบรายการ ID: $idToUpdate\n";
    }
}

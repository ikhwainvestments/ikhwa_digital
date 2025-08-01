<?php

// السماح فقط بالطلبات POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'الطريقة غير مسموحة']);
    exit;
}

// عنوان API الداخلي (بدون SSL)
$apiUrl = 'http://api.ikhwadigital.com/api/ikhwadigital/contact';

// تجهيز البيانات المرسلة
$fields = [];
foreach ($_POST as $key => $value) {
    $fields[$key] = $value;
}

// إذا كان هناك ملف مرفق
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $filePath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];

    $fields['file'] = new CURLFile($filePath, $fileType, $fileName);
}

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['message' => 'فشل الاتصال بالخادم']);
    exit;
}

curl_close($ch);

// إعادة رد الـ API
http_response_code($httpCode);
header('Content-Type: application/json');
echo $response;

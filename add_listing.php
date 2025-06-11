<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}

require_once './config.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_listing'])) {
    header('Content-Type: application/json');

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category_id'] ?? 1;
    $price = $_POST['price'] ?? 0.00;
    $location = trim($_POST['location'] ?? '');
    $currency = $_POST['currency'] ?? 'UZS';

    // Maydonlarni tekshirish
    if (empty($title) || empty($price)) {
        echo json_encode([
            'success' => false,
            'title' => '⚠️ Diqqat!',
            'message' => 'Iltimos, asosiy maydonlarni to‘ldiring!'
        ]);
        exit;
    }

    $data = [
        'user_id' => $_SESSION['user']['id'],
        'title' => htmlspecialchars($title),
        'description' => htmlspecialchars($description),
        'category_id' => $category_id,
        'price' => floatval($price),
        'location' => $location,
        'images' => json_encode([]),
        'currency' => $currency
    ];

    $db->insert('listings', $data);
    echo json_encode([
        'success' => true,
        'title' => '✅ Muvaffaqiyat!',
        'message' => 'E’lon muvaffaqiyatli qo‘shildi!',
        'redirect' => 'listings.php'
    ]);
    exit;
} else {
    echo json_encode([
        'success' => false,
        'title' => '❌ Xato!',
        'message' => 'Noto‘g‘ri so‘rov!'
    ]);
    exit;
}
?>
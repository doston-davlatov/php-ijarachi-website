<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}
require_once './config.php';
$db = new Database();
$user = $_SESSION['user'];

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$category_id = intval($_POST['category_id'] ?? 0);
$location = $_POST['location'] ?? '';
$price = floatval($_POST['price'] ?? 0);
$currency = $_POST['currency'] ?? 'UZS';

if ($title && $description && $category_id && $price > 0) {
    if (isset($_GET['edit'])) {
        // Tahrirlash
        $id = intval($_GET['edit']);
        $listing = $db->select('listings', '*', 'id = ?', [$id], 'i');
        if ($listing && $listing[0]['user_id'] == $user['id']) {
            $db->update('listings',
                ['title'=>$title, 'description'=>$description, 'category_id'=>$category_id, 'location'=>$location, 'price'=>$price, 'currency'=>$currency],
                'id = ?', [$id], 'i'
            );
        }
    } else {
        // Qo‘shish
        $db->insert('listings',
            ['title'=>$title, 'description'=>$description, 'category_id'=>$category_id, 'location'=>$location, 'price'=>$price, 'currency'=>$currency, 'user_id'=>$user['id']]
        );
    }
}
header('Location: my_listings.php');
exit;
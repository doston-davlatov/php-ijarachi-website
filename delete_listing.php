<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}
require_once './config.php';
$db = new Database();
$user = $_SESSION['user'];
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $listing = $db->select('listings', '*', 'id = ?', [$id], 'i');
    if ($listing && $listing[0]['user_id'] == $user['id']) {
        $db->delete('listings', 'id = ?', [$id], 'i');
    }
}
header('Location: my_listings.php');
exit;
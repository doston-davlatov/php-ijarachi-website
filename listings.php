<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}
require_once './config.php';
$db = new Database();
$listings = $db->select('listings', '*', '', [], '');
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8" />
    <title>Barcha e’lonlar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
</head>
<body>
<div class="container mt-5">
    <nav class="mb-4">
        <a href="listings.php" class="btn btn-primary">Barcha e’lonlar</a>
        <a href="add_listing_form.php" class="btn btn-success">Yangi e’lon qo‘shish</a>
        <a href="my_listings.php" class="btn btn-info">Xabarlarim</a>
        <a href="logout.php" class="btn btn-danger float-end">Chiqish</a>
    </nav>
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title">Barcha e’lonlar</h5>
            <?php if ($listings): ?>
                <ul class="list-group">
                    <?php foreach ($listings as $listing):
                        $user = $db->select('users', 'name', 'id = ?', [$listing['user_id']], 'i');
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($listing['title']) ?></strong><br>
                                <small><?= htmlspecialchars($listing['location'] ?? 'Joylashuv kiritilmagan') ?>
                                    - <?= number_format($listing['price'], 2) ?> <?= htmlspecialchars($listing['currency'] ?? 'UZS') ?></small>
                                <br><small>Qo‘shgan: <?= htmlspecialchars($user[0]['name'] ?? 'Noma’lum') ?></small>
                            </div>
                            <?php if ($listing['user_id'] == $_SESSION['user']['id']): ?>
                                <div>
                                    <a href="add_listing_form.php?edit=<?= $listing['id'] ?>" class="btn btn-sm btn-warning me-2">Tahrirlash</a>
                                    <a href="delete_listing.php?id=<?= $listing['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('E’lonni o‘chirishni xohlaysizmi?')">O‘chirish</a>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-center">Hali hech qanday e’lon yo‘q.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}
require_once './config.php';
$db = new Database();
$categories = $db->select('categories');
$isEdit = isset($_GET['edit']);
$listing = null;
if ($isEdit) {
    $id = intval($_GET['edit']);
    $listingArr = $db->select('listings', '*', 'id = ?', [$id], 'i');
    if ($listingArr && $listingArr[0]['user_id'] == $_SESSION['user']['id']) {
        $listing = $listingArr[0];
    } else {
        header('Location: listings.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8" />
    <title><?= $isEdit ? 'E’lonni tahrirlash' : 'Yangi e’lon qo‘shish' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
</head>
<body>
<div class="container mt-5">
    <?= include 'template/header.php'; ?>

    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title"><?= $isEdit ? 'E’lonni tahrirlash' : 'Yangi e’lon qo‘shish' ?></h5>
            <form method="POST" action="add_listing.php<?= $isEdit ? '?edit=' . $listing['id'] : '' ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Sarlavha</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($listing['title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Tavsif</label>
                    <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($listing['description'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Turkum</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Tanlang...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($listing['category_id']) && $listing['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Joylashuv</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($listing['location'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Narx</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($listing['price'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="currency" class="form-label">Valyuta</label>
                    <input type="text" class="form-control" id="currency" name="currency" value="<?= htmlspecialchars($listing['currency'] ?? 'UZS') ?>">
                </div>
                <button type="submit" class="btn btn-success"><?= $isEdit ? 'Saqlash' : 'Qo‘shish' ?></button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
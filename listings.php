<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}

require_once './config.php';

$db = new Database();

// Barcha e’lonlarni olish
$listings = $db->select('listings', '*'); // Filtrlarsiz barcha e’lonlar
$categories = $db->select('categories');
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Barcha E’lonlar - Ijarachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <?php include 'template/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Barcha E’lonlar <i class="fas fa-bullhorn"></i></h1>

                <!-- Yangi e’lon qo‘shish formasi -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Yangi E’lon Qo‘shish</h5>
                        <form id="addListingForm" method="POST" action="add_listing.php">
                            <div class="mb-3">
                                <label for="title" class="form-label">Sarlavha</label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Sarlavha" required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Tavsif</label>
                                <textarea class="form-control" name="description" id="description" placeholder="Tavsif" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Toifa <i class="fas fa-layer-group"></i></label>
                                <select class="form-select" name="category_id" id="category_id" required>
                                    <option value="" disabled selected>Tanlang...</option>
                                    <?php
                                    usort($categories, function($a, $b) {
                                        return strcmp($a['name'], $b['name']);
                                    });
                                    foreach ($categories as $cat): 
                                    ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Narx</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" id="price" placeholder="Narx" step="0.01" required />
                                    <select class="form-select" name="currency" id="currency" required>
                                        <option value="UZS">so'm (UZS)</option>
                                        <option value="USD">$ (USD)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Joylashuv</label>
                                <input type="text" class="form-control" name="location" id="location" placeholder="Joylashuv (masalan, Toshkent)" />
                            </div>
                            <button type="submit" name="add_listing" class="btn btn-primary w-100">Qo‘shish</button>
                        </form>
                    </div>
                </div>

                <!-- E’lonlar ro‘yxati -->
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">E’lonlar Ro‘yxati</h5>
                        <?php if ($listings): ?>
                            <ul class="list-group">
                                <?php foreach ($listings as $listing): 
                                    $user = $db->select('users', 'name', 'id = ?', [$listing['user_id']], 'i');
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($listing['title']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($listing['location'] ?? 'Joylashuv kiritilmagan'); ?>
                                                - <?php echo number_format($listing['price'], 2); ?> <?php echo htmlspecialchars($listing['currency'] ?? 'UZS'); ?></small>
                                            <br><small>Qo‘shgan: <?php echo htmlspecialchars($user[0]['name'] ?? 'Noma’lum'); ?></small>
                                        </div>
                                        <?php if ($listing['user_id'] == $_SESSION['user']['id']): ?>
                                            <div>
                                                <a href="listings.php?edit=<?php echo $listing['id']; ?>" class="btn btn-sm btn-warning me-2">Tahrirlash</a>
                                                <a href="listings.php?delete=<?php echo $listing['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('E’lonni o‘chirishni xohlaysizmi?');">O‘chirish</a>
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
        </div>
    </div>

    <?php include 'template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('addListingForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('add_listing.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Tarmoq xatosi: ' + response.status);
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: result.title,
                            text: result.message,
                            confirmButtonText: 'OK',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = result.redirect || 'listings.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: result.title,
                            text: result.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ Tarmoq xatosi',
                        text: 'Server bilan bog‘lanishda muammo yuz berdi: ' + error.message
                    });
                    console.error('Fetch error:', error);
                });
        });

        document.querySelectorAll('a[href*="delete"]').forEach(link => {
            link.addEventListener('click', function (e) {
                if (!confirm('E’lonni o‘chirishni xohlaysizmi?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>
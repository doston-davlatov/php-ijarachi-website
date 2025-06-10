<?php
session_start();

// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header('Location: login/index.php');
//     exit;
// }

require_once 'src/config/config.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_listing'])) {
    header('Content-Type: application/json');

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category_id'] ?? 1;
    $price = $_POST['price'] ?? 0.00;
    $location = trim($_POST['location'] ?? '');

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
        'images' => '[]'
    ];
    $db->insert('listings', $data);
    echo json_encode([
        'success' => true,
        'title' => '✅ Muvaffaqiyat!',
        'message' => 'E’lon muvaffaqiyatli qo‘shildi!',
        'redirect' => 'listings.php'
    ]);
    exit;
}

$listings = $db->select('listings', '*', 'user_id = ?', [$_SESSION['user']['id']], 'i');
$categories = $db->select('categories');
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mening E’lonlarim - Ijarachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body class="bg-light">
    <?php include 'template/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Mening E’lonlarim</h1>

                <!-- Yangi e’lon qo‘shish formasi -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Yangi E’lon Qo‘shish</h5>
                        <form id="addListingForm" method="POST">
                            <div class="mb-3">
                                <label for="title" class="form-label">Sarlavha</label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Sarlavha"
                                    required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Tavsif</label>
                                <textarea class="form-control" name="description" id="description" placeholder="Tavsif"
                                    rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Toifa</label>
                                <select class="form-select" name="category_id" id="category_id" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Narx</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" id="price" placeholder="Narx"
                                        step="0.01" required />
                                    <select class="form-select" name="currency" id="currency" required>
                                        <option value="UZS">so'm (UZS)</option>
                                        <option value="USD">$ (USD)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Joylashuv</label>
                                <input type="text" class="form-control" name="location" id="location"
                                    placeholder="Joylashuv (masalan, Toshkent)" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Xaritadan tanlash</label>
                                <div id="map" style="height: 300px; border-radius: 8px; margin-bottom: 10px;"></div>
                                <small class="text-muted">Xaritada biror joyni bosing, koordinatalar avtomatik
                                    yoziladi.</small>
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
                                <?php foreach ($listings as $listing): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($listing['title']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($listing['location'] ?? 'Joylashuv kiritilmagan'); ?>
                                                - <?php echo number_format($listing['price'], 2); ?> so'm</small>
                                        </div>
                                        <div>
                                            <a href="listings.php?edit=<?php echo $listing['id']; ?>"
                                                class="btn btn-sm btn-warning me-2">Tahrirlash</a>
                                            <a href="listings.php?delete=<?php echo $listing['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('E’lonni o‘chirishni xohlaysizmi?');">O‘chirish</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center">Hali hech qanday e’loningiz yo‘q.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('addListingForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
                .then(response => response.json())
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
                        text: 'Server bilan bog‘lanishda muammo yuz berdi.'
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

        // Xaritani yaratish
        var map = L.map('map').setView([41.3111, 69.2797], 10); // Boshlang‘ich nuqta: Toshkent

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker;

        map.on('click', function (e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);
            var coords = lat + ", " + lng;

            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById('location').value = coords;
        });
    </script>
</body>

</html>
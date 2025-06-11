<?php
session_start();

require_once './config.php';

$db = new Database();

// Autentifikatsiya tekshiruvi
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header('Location: login/index.php');
    exit;
}

$user = $_SESSION['user'];

// Xabarlar boshqaruvi
$msg = '';
if (isset($_GET['msg'])) {
    $msg = urldecode($_GET['msg']);
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ijarachi - Asosiy Sahifa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="src/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
    <?php include 'template/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Xush kelibsiz, <?php echo htmlspecialchars($user['name']); ?>! <i class="fas fa-user-check"></i></h1>
                <?php if ($msg): ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Muvaffaqiyat!',
                            text: '<?php echo addslashes($msg); ?>',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>
                <?php endif; ?>

                <!-- Joriy sana va vaqt -->
                <p class="text-center text-muted mb-4">Bugun: <?php echo date('l, F d, Y', strtotime('2025-06-11')); ?> (09:44 AM +05) <i class="fas fa-calendar-day"></i></p>

                <!-- Tez kirish knopkalari -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <a href="listings.php" class="btn btn-primary w-100"><i class="fas fa-list-ul"></i> Barcha E’lonlar</a>
                    </div>
                    <div class="col-md-4">
                        <a href="add_listing.php" class="btn btn-success w-100"><i class="fas fa-plus"></i> Yangi E’lon Qo‘shish</a>
                    </div>
                    <div class="col-md-4">
                        <a href="messages.php" class="btn btn-info w-100"><i class="fas fa-envelope"></i> Xabarlarim</a>
                    </div>
                </div>

                <!-- Foydalanuvchi profilingiz qisqacha ma’lumotlari -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Profilingiz <i class="fas fa-user"></i></h5>
                        <p><strong><i class="fas fa-signature"></i> Ism:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong><i class="fas fa-user-tag"></i> Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong><i class="fas fa-phone"></i> Telefon:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Kiritilmagan'); ?></p>
                        <a href="profile.php" class="btn btn-warning"><i class="fas fa-edit"></i> Profilingizni Tahrirlash</a>
                    </div>
                </div>

                <!-- So‘nggi e’lonlar -->
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">So‘nggi E’lonlarim <i class="fas fa-bullhorn"></i></h5>
                        <?php
                        $listings = $db->select('listings', '*', 'user_id = ?', [$user['id']], 'i');
                        if ($listings): ?>
                            <ul class="list-group">
                                <?php foreach (array_slice($listings, 0, 3) as $listing): ?>
                                    <li class="list-group-item">
                                        <?php echo htmlspecialchars($listing['title']); ?> - 
                                        <?php echo number_format($listing['price'], 2); ?> so'm
                                        <a href="listings.php?edit=<?php echo $listing['id']; ?>" class="btn btn-sm btn-secondary float-end"><i class="fas fa-edit"></i></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="listings.php" class="btn btn-link mt-2"><i class="fas fa-eye"></i> Barchasini ko‘rish</a>
                        <?php else: ?>
                            <p class="text-center">Hali hech qanday e’loningiz yo‘q. <i class="fas fa-exclamation-triangle"></i></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
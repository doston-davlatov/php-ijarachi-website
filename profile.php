<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}

require_once './config.php';

$db = new Database();
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!empty($name)) {
        $data = ['name' => htmlspecialchars($name), 'phone' => $phone ?: null];
        $db->update('users', $data, 'id = ?', [$_SESSION['user']['id']], 'i');
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $phone ?: null;
        header('Location: profile.php?msg=' . urlencode('Profil muvaffaqiyatli yangilandi!'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil - Ijarachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="src/css/style.css" />
</head>
<body class="bg-light">
    <?php include 'template/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Profil <i class="fas fa-user"></i></h1>
                <?php if (isset($_GET['msg'])): ?>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Muvaffaqiyat!',
                            text: '<?php echo addslashes(urldecode($_GET['msg'])); ?>',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Profil Ma’lumotlari</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Ism</label>
                                <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required />
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon (ixtiyoriy)</label>
                                <input type="tel" class="form-control" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" />
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Saqlash</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
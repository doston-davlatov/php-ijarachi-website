<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login/index.php');
    exit;
}

require_once './config.php';

$db = new Database();

// Masofiy misol: Xabarlar jadvali mavjud deb hisoblaymiz
$messages = $db->select('messages', '*', 'user_id = ?', [$_SESSION['user']['id']], 'i');
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Xabarlarim - Ijarachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body class="bg-light">
    <?php include 'template/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Xabarlarim <i class="fas fa-envelope"></i></h1>
                <div class="card shadow">
                    <div class="card-body">
                        <?php if ($messages): ?>
                            <ul class="list-group">
                                <?php foreach ($messages as $message): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($message['subject'] ?? 'Subjektsiz'); ?></strong><br>
                                            <small><?php echo htmlspecialchars($message['message'] ?? 'Xabar yo‘q'); ?></small>
                                        </div>
                                        <span class="badge bg-secondary"><?php echo date('Y-m-d', strtotime($message['created_at'] ?? 'now')); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-center">Hali hech qanday xabaringiz yo‘q. <i class="fas fa-exclamation-triangle"></i></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
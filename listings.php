<?php
session_start();

require_once 'src/config/config.php';
$db = new Database();

// E’lonlarni bazadan olish (LEFT JOIN orqali toifa nomi bilan birga)
$sql = "
    SELECT l.*, c.name AS category_name, u.username
    FROM listings l
    LEFT JOIN categories c ON l.category_id = c.id
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
";

$result = $db->executeQuery($sql);

if (is_string($result)) {
    die($result); // Agar xato bo‘lsa, uni chiqarish
}

$listings = $result->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Barcha E’lonlar - Ijarachi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body class="bg-light">
    <?php include 'template/header.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-4">Barcha E’lonlar</h1>

        <?php if ($listings): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($listings as $listing): ?>
                    <div class="col">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($listing['title']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($listing['category_name'] ?? 'Toifa yo‘q'); ?></h6>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
                                <p class="mb-1"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($listing['location'] ?? 'Joylashuv kiritilmagan'); ?></p>
                                <p class="fw-bold"><?php echo number_format($listing['price'], 2); ?> so‘m</p>
                                <p class="small text-muted mb-2">E’lon beruvchi: <?php echo htmlspecialchars($listing['username'] ?? 'Anonim'); ?></p>

                                <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $listing['user_id']): ?>
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="listings.php?edit=<?php echo $listing['id']; ?>" class="btn btn-warning btn-sm w-100">Tahrirlash</a>
                                        <a href="listings.php?delete=<?php echo $listing['id']; ?>" class="btn btn-danger btn-sm w-100"
                                           onclick="return confirm('E’lonni o‘chirishni xohlaysizmi?');">O‘chirish</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">Hozircha hech qanday e’lon mavjud emas.</p>
        <?php endif; ?>
    </div>

    <?php include 'template/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

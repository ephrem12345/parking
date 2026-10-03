<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('driver');

$areas = $pdo->query('SELECT * FROM parking_areas ORDER BY created_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Parking Areas</h1>
        <div class="row g-3">
            <?php foreach ($areas as $area): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5><?= sanitize($area['name']) ?></h5>
                            <p class="text-muted mb-2"><?= sanitize($area['address']) ?></p>
                            <p class="mb-1">Slots: <?= sanitize($area['total_slots']) ?></p>
                            <p class="mb-3">Rate: &#8369;<?= number_format($area['price_per_hour'], 2) ?>/hr</p>
                            <a class="btn btn-success" href="reserve.php?area=<?= $area['id'] ?>">Reserve Slot</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

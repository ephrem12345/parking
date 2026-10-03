<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['area_name'])) {
    $name = sanitize($_POST['area_name']);
    $address = sanitize($_POST['area_address']);
    $total = (int)$_POST['total_slots'];
    $price = (float)$_POST['price_per_hour'];
    if ($name && $address && $total > 0) {
        $stmt = $pdo->prepare('INSERT INTO parking_areas (name, address, total_slots, price_per_hour) VALUES (:name,:address,:total,:price)');
        $stmt->execute(['name'=>$name,'address'=>$address,'total'=>$total,'price'=>$price]);
        flash('success', 'Parking area added.');
        header('Location: parking_areas.php');
        exit;
    }
}

$stmt = $pdo->query('SELECT * FROM parking_areas ORDER BY created_at DESC');
$areas = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3">Parking Areas</h1>
                <p class="text-muted">Add new areas and review existing locations.</p>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#areaModal">Add Area</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Address</th><th>Total Slots</th><th>Price/Hour</th><th>Created</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($areas as $area): ?>
                            <tr>
                                <td><?= sanitize($area['id']) ?></td>
                                <td><?= sanitize($area['name']) ?></td>
                                <td><?= sanitize($area['address']) ?></td>
                                <td><?= sanitize($area['total_slots']) ?></td>
                                <td>&#8369;<?= number_format($area['price_per_hour'], 2) ?></td>
                                <td><?= sanitize($area['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="areaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="parking_areas.php">
                <div class="modal-header">
                    <h5 class="modal-title">Add Parking Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="area_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Address</label><input type="text" name="area_address" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Total Slots</label><input type="number" name="total_slots" class="form-control" min="1" required></div>
                    <div class="mb-3"><label class="form-label">Price per Hour</label><input type="number" step="0.01" name="price_per_hour" class="form-control" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Area</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

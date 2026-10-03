<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_number'])) {
    $area = (int)$_POST['area_id'];
    $slotNumber = sanitize($_POST['slot_number']);
    $type = sanitize($_POST['slot_type']);
    $status = sanitize($_POST['slot_status']);
    if ($area && $slotNumber) {
        $stmt = $pdo->prepare('INSERT INTO parking_slots (area_id, slot_number, slot_type, status) VALUES (:area,:slot,:type,:status)');
        $stmt->execute(['area'=>$area,'slot'=>$slotNumber,'type'=>$type,'status'=>$status]);
        header('Location: parking_slots.php');
        exit;
    }
}

$areas = $pdo->query('SELECT id,name FROM parking_areas')->fetchAll();
$slots = $pdo->query('SELECT p.*, a.name AS area_name FROM parking_slots p JOIN parking_areas a ON p.area_id=a.id ORDER BY p.created_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div><h1 class="h3">Parking Slots</h1><p class="text-muted">Create and review slots with status and type.</p></div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#slotModal">Add Slot</button>
        </div>
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Slot</th><th>Area</th><th>Type</th><th>Status</th><th>Created</th></tr></thead>
                    <tbody>
                        <?php foreach ($slots as $slot): ?>
                            <tr>
                                <td><?= sanitize($slot['id']) ?></td>
                                <td><?= sanitize($slot['slot_number']) ?></td>
                                <td><?= sanitize($slot['area_name']) ?></td>
                                <td><?= sanitize($slot['slot_type']) ?></td>
                                <td><span class="badge bg-<?= $slot['status'] === 'Available' ? 'success' : ($slot['status'] === 'Reserved' ? 'warning text-dark' : ($slot['status'] === 'Occupied' ? 'danger' : 'secondary')) ?>"><?= sanitize($slot['status']) ?></span></td>
                                <td><?= sanitize($slot['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="slotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="parking_slots.php">
                <div class="modal-header"><h5 class="modal-title">Add Parking Slot</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Parking Area</label><select name="area_id" class="form-select" required><option value="">Select area</option><?php foreach ($areas as $area): ?><option value="<?= $area['id'] ?>"><?= sanitize($area['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><label class="form-label">Slot Number</label><input name="slot_number" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Slot Type</label><select name="slot_type" class="form-select"><option value="standard">Standard</option><option value="vip">VIP</option><option value="disabled">Disabled</option></select></div>
                    <div class="mb-3"><label class="form-label">Status</label><select name="slot_status" class="form-select"><option value="Available">Available</option><option value="Reserved">Reserved</option><option value="Occupied">Occupied</option><option value="Maintenance">Maintenance</option></select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-success">Create Slot</button></div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

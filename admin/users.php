<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Manage Users</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= sanitize($user['id']) ?></td>
                                    <td><?= sanitize($user['full_name']) ?></td>
                                    <td><?= sanitize($user['email']) ?></td>
                                    <td><?= sanitize($user['phone']) ?></td>
                                    <td><span class="badge bg-<?= $user['role'] === 'admin' ? 'dark' : ($user['role'] === 'guard' ? 'info' : 'success') ?>"><?= sanitize($user['role']) ?></span></td>
                                    <td><?= sanitize($user['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

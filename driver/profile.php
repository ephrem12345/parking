<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('driver');
$user = currentUser();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }
    $name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    if (!$name || !$phone) {
        $errors[] = 'Full name and phone are required.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE users SET full_name=:name, phone=:phone WHERE id=:id');
        $stmt->execute(['name'=>$name,'phone'=>$phone,'id'=>$user['id']]);
        $_SESSION['user']['full_name'] = $name;
        $_SESSION['user']['phone'] = $phone;
        flash('success', 'Profile updated successfully.');
        header('Location: profile.php');
        exit;
    }
}
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">My Profile</h1>
        <?php if ($errors): ?><div class="alert alert-danger"><ul><?php foreach ($errors as $error): ?><li><?= sanitize($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="profile.php">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" value="<?= sanitize($user['full_name']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" readonly></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= sanitize($user['phone']) ?>" required></div>
                    <button type="submit" class="btn btn-success">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

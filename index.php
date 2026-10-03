<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    $role = currentUser()['role'];
    if ($role === 'admin') {
        header('Location: /park system/admin/dashboard.php');
        exit;
    }
    if ($role === 'guard') {
        header('Location: /park system/guard/dashboard.php');
        exit;
    }
    if ($role === 'driver') {
        header('Location: /park system/driver/dashboard.php');
        exit;
    }
}
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold">Smart Parking Management System</h1>
            <p class="lead">A modern parking management platform for universities, malls, airports, and offices. Reserve slots, manage parking areas, scan QR codes, and track payments in real time.</p>
            <a href="login.php" class="btn btn-success btn-lg me-2">Login</a>
            <a href="register.php" class="btn btn-outline-success btn-lg">Register</a>
        </div>
        <div class="col-lg-6 text-center">
            <img src="assets/images/park illustration.svg" alt="Parking dashboard" class="img-fluid" style="max-height:400px;">
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>

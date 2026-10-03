<?php
$user = currentUser();
$role = $user['role'] ?? null;
$menu = [];
if ($role === 'admin') {
    $menu = [
        ['label' => 'Dashboard', 'link' => '/park system/admin/dashboard.php', 'icon' => 'bi-speedometer2'],
        ['label' => 'Users', 'link' => '/park system/admin/users.php', 'icon' => 'bi-people-fill'],
        ['label' => 'Parking Areas', 'link' => '/park system/admin/parking_areas.php', 'icon' => 'bi-building'],
        ['label' => 'Parking Slots', 'link' => '/park system/admin/parking_slots.php', 'icon' => 'bi-columns-gap'],
        ['label' => 'Reservations', 'link' => '/park system/admin/reservations.php', 'icon' => 'bi-journal-check'],
        ['label' => 'Payments', 'link' => '/park system/admin/payments.php', 'icon' => 'bi-receipt'],
        ['label' => 'Reports', 'link' => '/park system/admin/reports.php', 'icon' => 'bi-graph-up'],
        ['label' => 'Settings', 'link' => '/park system/admin/settings.php', 'icon' => 'bi-gear']
    ];
} elseif ($role === 'driver') {
    $menu = [
        ['label' => 'Dashboard', 'link' => '/park system/driver/dashboard.php', 'icon' => 'bi-house'],
        ['label' => 'Parking Areas', 'link' => '/park system/driver/parking_areas.php', 'icon' => 'bi-building'],
        ['label' => 'Reserve Slot', 'link' => '/park system/driver/reserve.php', 'icon' => 'bi-calendar-check'],
        ['label' => 'My Reservations', 'link' => '/park system/driver/my_reservations.php', 'icon' => 'bi-check2-square'],
        ['label' => 'Payments', 'link' => '/park system/driver/payment_history.php', 'icon' => 'bi-cash-stack'],
        ['label' => 'Profile', 'link' => '/park system/driver/profile.php', 'icon' => 'bi-person-circle']
    ];
} elseif ($role === 'guard') {
    $menu = [
        ['label' => 'Dashboard', 'link' => '/park system/guard/dashboard.php', 'icon' => 'bi-shield-lock'],
        ['label' => 'Scan Entry', 'link' => '/park system/guard/scan_entry.php', 'icon' => 'bi-qr-code-scan'],
        ['label' => 'Scan Exit', 'link' => '/park system/guard/scan_exit.php', 'icon' => 'bi-box-arrow-right'],
        ['label' => 'Active Vehicles', 'link' => '/park system/guard/active_vehicles.php', 'icon' => 'bi-car-front-fill']
    ];
}
?>
<div class="sidebar bg-white border-end">
    <div class="sidebar-header p-3">
        <h5>Navigation</h5>
        <?php if ($user): ?>
            <p class="text-muted mb-0">Welcome, <?= sanitize($user['full_name']) ?></p>
        <?php endif; ?>
    </div>
    <ul class="nav flex-column px-2">
        <?php foreach ($menu as $item): ?>
            <li class="nav-item mb-1">
                <a class="nav-link rounded d-flex align-items-center" href="<?= $item['link'] ?>">
                    <i class="bi <?= $item['icon'] ?> me-2"></i> <?= $item['label'] ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

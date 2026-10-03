# Smart Parking Management System

A modern web-based parking management system with role-based access for drivers, administrators, and security guards.

## Features

- Driver registration and login
- Admin dashboard and management panels
- Security guard QR entry and exit
- Real-time parking slot monitoring
- Slot reservation and cancellation
- QR code generation and validation
- Automatic fee calculation and payment records
- Notifications and analytics reports
- Responsive Bootstrap 5 interface

## Setup Instructions

1. Copy the project folder into your XAMPP `htdocs` directory.
2. Create a MySQL database named `parking_system`.
3. Import `sql/parking_system.sql` into the database.
4. Update database settings in `config/database.php` if needed.
5. Start Apache and MySQL in XAMPP.
6. Open your browser and visit `http://localhost/park system/`.
7. Log in using one of the sample accounts:
   - Admin: `admin@parking.com` / `admin123`
   - Guard: `guard@parking.com` / `guard123`
   - Driver: `driver@parking.com` / `driver123`

## Notes

- Uses PHP 8+ and PDO for secure database access.
- Includes CSRF protection, password hashing, validation, and role-based authorization.
- Additional APIs are available under the `api/` folder.

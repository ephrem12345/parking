-- Smart Parking Management System database schema

DROP DATABASE IF EXISTS `parking_system`;
CREATE DATABASE `parking_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `parking_system`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('driver','admin','guard') NOT NULL DEFAULT 'driver',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `parking_areas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `total_slots` INT NOT NULL DEFAULT 0,
  `price_per_hour` DECIMAL(10,2) NOT NULL DEFAULT 50.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `parking_slots` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `area_id` INT NOT NULL,
  `slot_number` VARCHAR(20) NOT NULL,
  `slot_type` ENUM('standard','vip','disabled') NOT NULL DEFAULT 'standard',
  `status` ENUM('Available','Reserved','Occupied','Maintenance') NOT NULL DEFAULT 'Available',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`area_id`) REFERENCES `parking_areas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `slot_id` INT NOT NULL,
  `qr_code` VARCHAR(255) NOT NULL,
  `reservation_date` DATE NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `check_in_time` DATETIME NULL,
  `check_out_time` DATETIME NULL,
  `status` ENUM('Pending','Reserved','CheckedIn','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `total_fee` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`slot_id`) REFERENCES `parking_slots`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reservation_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `transaction_reference` VARCHAR(100) NULL,
  `status` ENUM('Paid','Pending','Failed') NOT NULL DEFAULT 'Paid',
  `paid_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `favorites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `area_id` INT NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`area_id`) REFERENCES `parking_areas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percentage','fixed') NOT NULL DEFAULT 'fixed',
  `discount_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `expiry_date` DATE NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO `users` (`full_name`,`email`,`phone`,`password`,`role`) VALUES
('System Administrator','admin@parking.com','09170000000','$2y$10$rlByJPhjEWKu1jkO9N1ACuOoifB.jjcUrP7iICdyTcVy80kI8WQ1a','admin'),
('Security Guard','guard@parking.com','09170000001','$2y$10$yInBtFRRaFyOm.fFWFm6HupST/g8W3l9K.aYBtJiPBluF8PH5NX9a','guard'),
('Jane Doe','driver@parking.com','09170000002','$2y$10$LHDtO49lQG/NWQ4GW.Vh1u2vc.tt4GRKL1g3n/acpoCDrg5sYrbRK','driver'),
('Alice Santos','alice@parking.com','09170000003','$2y$10$LHDtO49lQG/NWQ4GW.Vh1u2vc.tt4GRKL1g3n/acpoCDrg5sYrbRK','driver'),
('Mark Cruz','mark@parking.com','09170000004','$2y$10$LHDtO49lQG/NWQ4GW.Vh1u2vc.tt4GRKL1g3n/acpoCDrg5sYrbRK','driver'),
('Luis Reyes','luis@parking.com','09170000005','$2y$10$LHDtO49lQG/NWQ4GW.Vh1u2vc.tt4GRKL1g3n/acpoCDrg5sYrbRK','driver'),
('Rhonda Lopez','rhonda@parking.com','09170000006','$2y$10$LHDtO49lQG/NWQ4GW.Vh1u2vc.tt4GRKL1g3n/acpoCDrg5sYrbRK','driver');

INSERT INTO `parking_areas` (`name`,`address`,`total_slots`,`price_per_hour`) VALUES
('City Campus Parking','123 University Ave','35',80.00),
('Metro Mall Parking','88 Mall Boulevard','40',100.00),
('Airport Terminal Parking','Terminal 2 Road','25',150.00);

INSERT INTO `parking_slots` (`area_id`,`slot_number`,`slot_type`,`status`) VALUES
(1,'A1','standard','Available'),(1,'A2','standard','Available'),(1,'A3','vip','Available'),(1,'A4','disabled','Available'),
(1,'A5','standard','Reserved'),(1,'A6','standard','Occupied'),(1,'A7','standard','Maintenance'),
(2,'B1','standard','Available'),(2,'B2','vip','Available'),(2,'B3','standard','Available'),(2,'B4','standard','Reserved'),
(2,'B5','disabled','Available'),(2,'B6','standard','Occupied'),(2,'B7','standard','Available'),
(3,'C1','standard','Available'),(3,'C2','standard','Available'),(3,'C3','vip','Reserved'),(3,'C4','disabled','Occupied');

INSERT INTO `reservations` (`user_id`,`slot_id`,`qr_code`,`reservation_date`,`start_time`,`end_time`,`status`,`total_fee`) VALUES
(3,5,'QR-DRIVER-0001',CURDATE(),CONCAT(CURDATE(),' 09:00:00'),CONCAT(CURDATE(),' 12:00:00'),'Reserved',240.00),
(4,6,'QR-DRIVER-0002',CURDATE(),CONCAT(CURDATE(),' 08:30:00'),CONCAT(CURDATE(),' 11:30:00'),'CheckedIn',300.00);

INSERT INTO `payments` (`reservation_id`,`amount`,`payment_method`,`transaction_reference`,`status`) VALUES
(1,240.00,'Cash','CASH-2026-001','Paid'),
(2,300.00,'Card','CARD-2026-002','Paid');

INSERT INTO `notifications` (`user_id`,`title`,`message`) VALUES
(3,'Reservation Confirmed','Your parking reservation has been confirmed.'),(4,'Maintenance Notice','One of your favorite parking slots is under maintenance.');

INSERT INTO `coupons` (`code`,`discount_type`,`discount_value`,`expiry_date`,`is_active`) VALUES
('WELCOME10','percentage',10.00,DATE_ADD(CURDATE(), INTERVAL 30 DAY),1);

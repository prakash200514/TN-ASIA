-- ============================================================
-- TNSTC Smart Bus Management System – Tirunelveli District
-- Database: tnstc_tirunelveli
-- ============================================================

CREATE DATABASE IF NOT EXISTS `tnstc_tirunelveli` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tnstc_tirunelveli`;<<<<

-- ============================================================
-- TABLE: users (all roles)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `user_id`    INT AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(180) NOT NULL UNIQUE,
  `phone`      VARCHAR(15)  NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('passenger','driver','conductor','depot_manager','minister','admin') NOT NULL DEFAULT 'passenger',
  `status`     ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `otp`        VARCHAR(10)  DEFAULT NULL,
  `otp_expiry` DATETIME     DEFAULT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: depots
-- ============================================================
CREATE TABLE IF NOT EXISTS `depots` (
  `depot_id`   INT AUTO_INCREMENT PRIMARY KEY,
  `depot_name` VARCHAR(120) NOT NULL,
  `location`   VARCHAR(200) NOT NULL,
  `district`   VARCHAR(80)  NOT NULL DEFAULT 'Tirunelveli',
  `manager_id` INT          DEFAULT NULL,
  `latitude`   DECIMAL(10,7) DEFAULT NULL,
  `longitude`  DECIMAL(10,7) DEFAULT NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: buses
-- ============================================================
CREATE TABLE IF NOT EXISTS `buses` (
  `bus_id`       INT AUTO_INCREMENT PRIMARY KEY,
  `bus_number`   VARCHAR(20)  NOT NULL UNIQUE,
  `depot_id`     INT          NOT NULL,
  `bus_type`     ENUM('ordinary','express','super_express','ac','mini') NOT NULL DEFAULT 'ordinary',
  `total_seats`  INT          NOT NULL DEFAULT 40,
  `status`       ENUM('active','maintenance','inactive') NOT NULL DEFAULT 'active',
  FOREIGN KEY (`depot_id`) REFERENCES `depots`(`depot_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: routes
-- ============================================================
CREATE TABLE IF NOT EXISTS `routes` (
  `route_id`       INT AUTO_INCREMENT PRIMARY KEY,
  `route_number`   VARCHAR(20)  NOT NULL,
  `source`         VARCHAR(120) NOT NULL,
  `destination`    VARCHAR(120) NOT NULL,
  `distance`       DECIMAL(8,2) NOT NULL COMMENT 'in km',
  `estimated_time` INT          NOT NULL COMMENT 'in minutes'
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: route_stops
-- ============================================================
CREATE TABLE IF NOT EXISTS `route_stops` (
  `stop_id`      INT AUTO_INCREMENT PRIMARY KEY,
  `route_id`     INT          NOT NULL,
  `stop_name`    VARCHAR(120) NOT NULL,
  `stop_order`   INT          NOT NULL,
  `arrival_time` TIME         DEFAULT NULL,
  FOREIGN KEY (`route_id`) REFERENCES `routes`(`route_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: bus_stops
-- ============================================================
CREATE TABLE IF NOT EXISTS `bus_stops` (
  `stop_id`   INT AUTO_INCREMENT PRIMARY KEY,
  `stop_name` VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: drivers
-- ============================================================
CREATE TABLE IF NOT EXISTS `drivers` (
  `driver_id`      INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT          NOT NULL,
  `license_number` VARCHAR(30)  NOT NULL UNIQUE,
  `depot_id`       INT          NOT NULL,
  `status`         ENUM('active','inactive','on_leave') NOT NULL DEFAULT 'active',
  FOREIGN KEY (`user_id`)   REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`depot_id`)  REFERENCES `depots`(`depot_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: conductors
-- ============================================================
CREATE TABLE IF NOT EXISTS `conductors` (
  `conductor_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT NOT NULL,
  `depot_id`     INT NOT NULL,
  `status`       ENUM('active','inactive','on_leave') NOT NULL DEFAULT 'active',
  FOREIGN KEY (`user_id`)  REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`depot_id`) REFERENCES `depots`(`depot_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: schedules
-- ============================================================
CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id`    INT AUTO_INCREMENT PRIMARY KEY,
  `bus_id`         INT  NOT NULL,
  `route_id`       INT  NOT NULL,
  `driver_id`      INT  DEFAULT NULL,
  `conductor_id`   INT  DEFAULT NULL,
  `departure_time` TIME NOT NULL,
  `arrival_time`   TIME NOT NULL,
  `travel_date`    DATE NOT NULL,
  `status`         ENUM('scheduled','in_progress','completed','cancelled','delayed') NOT NULL DEFAULT 'scheduled',
  `delay_reason`   VARCHAR(255) DEFAULT NULL,
  `delay_minutes`  INT DEFAULT 0,
  FOREIGN KEY (`bus_id`)       REFERENCES `buses`(`bus_id`) ON DELETE CASCADE,
  FOREIGN KEY (`route_id`)     REFERENCES `routes`(`route_id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`)    REFERENCES `drivers`(`driver_id`) ON DELETE SET NULL,
  FOREIGN KEY (`conductor_id`) REFERENCES `conductors`(`conductor_id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: tickets
-- ============================================================
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id`      INT AUTO_INCREMENT PRIMARY KEY,
  `passenger_id`   INT          NOT NULL,
  `schedule_id`    INT          NOT NULL,
  `seat_number`    INT          NOT NULL,
  `source_stop`    VARCHAR(120) NOT NULL,
  `dest_stop`      VARCHAR(120) NOT NULL,
  `fare`           DECIMAL(8,2) NOT NULL,
  `qr_code`        VARCHAR(255) DEFAULT NULL,
  `payment_status` ENUM('paid','pending','refunded') NOT NULL DEFAULT 'paid',
  `ticket_status`  ENUM('active','used','cancelled') NOT NULL DEFAULT 'active',
  `booking_date`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`passenger_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`schedule_id`)  REFERENCES `schedules`(`schedule_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: bus_pass
-- ============================================================
CREATE TABLE IF NOT EXISTS `bus_pass` (
  `pass_id`       INT AUTO_INCREMENT PRIMARY KEY,
  `passenger_id`  INT          NOT NULL,
  `pass_type`     ENUM('monthly','student') NOT NULL,
  `source`        VARCHAR(120) NOT NULL,
  `destination`   VARCHAR(120) NOT NULL,
  `proof_document` VARCHAR(255) DEFAULT NULL,
  `status`        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `qr_code`       VARCHAR(255) DEFAULT NULL,
  `valid_from`    DATE DEFAULT NULL,
  `valid_to`      DATE DEFAULT NULL,
  `depot_id`      INT DEFAULT NULL,
  `verified_by`   INT DEFAULT NULL,
  `remarks`       VARCHAR(255) DEFAULT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`passenger_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: complaints
-- ============================================================
CREATE TABLE IF NOT EXISTS `complaints` (
  `complaint_id` INT AUTO_INCREMENT PRIMARY KEY,
  `passenger_id` INT          NOT NULL,
  `depot_id`     INT          DEFAULT NULL,
  `bus_id`       INT          DEFAULT NULL,
  `category`     ENUM('delay','staff_behavior','cleanliness','ticket_issue','route_issue','other') NOT NULL,
  `description`  TEXT         NOT NULL,
  `status`       ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `reply`        TEXT         DEFAULT NULL,
  `replied_by`   INT          DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`passenger_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`depot_id`)     REFERENCES `depots`(`depot_id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: lost_found
-- ============================================================
CREATE TABLE IF NOT EXISTS `lost_found` (
  `item_id`     INT AUTO_INCREMENT PRIMARY KEY,
  `reported_by` INT          NOT NULL,
  `report_type` ENUM('lost','found') NOT NULL DEFAULT 'lost',
  `item_name`   VARCHAR(120) NOT NULL,
  `description` TEXT         NOT NULL,
  `bus_id`      INT          DEFAULT NULL,
  `depot_id`    INT          DEFAULT NULL,
  `status`      ENUM('open','claimed','closed') NOT NULL DEFAULT 'open',
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`reported_by`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`bus_id`)      REFERENCES `buses`(`bus_id`) ON DELETE SET NULL,
  FOREIGN KEY (`depot_id`)    REFERENCES `depots`(`depot_id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: live_tracking
-- ============================================================
CREATE TABLE IF NOT EXISTS `live_tracking` (
  `tracking_id` INT AUTO_INCREMENT PRIMARY KEY,
  `bus_id`      INT           NOT NULL UNIQUE,
  `latitude`    DECIMAL(10,7) NOT NULL,
  `longitude`   DECIMAL(10,7) NOT NULL,
  `speed`       DECIMAL(5,2)  DEFAULT 0,
  `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`bus_id`) REFERENCES `buses`(`bus_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: emergency_alerts
-- ============================================================
CREATE TABLE IF NOT EXISTS `emergency_alerts` (
  `alert_id`   INT AUTO_INCREMENT PRIMARY KEY,
  `bus_id`     INT          NOT NULL,
  `driver_id`  INT          DEFAULT NULL,
  `depot_id`   INT          NOT NULL,
  `message`    TEXT         NOT NULL,
  `status`     ENUM('active','resolved') NOT NULL DEFAULT 'active',
  `latitude`   DECIMAL(10,7) DEFAULT NULL,
  `longitude`  DECIMAL(10,7) DEFAULT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`bus_id`)    REFERENCES `buses`(`bus_id`) ON DELETE CASCADE,
  FOREIGN KEY (`depot_id`)  REFERENCES `depots`(`depot_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `notif_id`   INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT          DEFAULT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `message`    TEXT         NOT NULL,
  `type`       ENUM('delay','booking','pass','complaint','emergency','general') NOT NULL DEFAULT 'general',
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin user (password: Admin@123)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`status`) VALUES
('System Admin','admin@tnstc.tn.gov.in','9999900000','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','admin','active');

-- Minister user (password: Minister@123)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`status`) VALUES
('District Minister','minister@tnstc.tn.gov.in','9999900001','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','minister','active');

-- Depot Managers (password: Manager@123)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`status`) VALUES
('Thamirabarani Manager','manager1@tnstc.tn.gov.in','9444100001','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active'),
('Bye-Pass Manager','manager2@tnstc.tn.gov.in','9444100002','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active'),
('KTC Nagar Manager','manager3@tnstc.tn.gov.in','9444100003','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active'),
('Cheranmahadevi Manager','manager4@tnstc.tn.gov.in','9444100004','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active'),
('Valliyoor Manager','manager5@tnstc.tn.gov.in','9444100005','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active'),
('Thisayanvilai Manager','manager6@tnstc.tn.gov.in','9444100006','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active'),
('Papanasam Manager','manager7@tnstc.tn.gov.in','9444100007','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','depot_manager','active');

-- Sample Passenger (password: Pass@123)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`status`) VALUES
('Arun Kumar','arun@gmail.com','9876543210','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','passenger','active');

-- Sample Driver user (password: Driver@123)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`status`) VALUES
('Rajan Kumar','driver1@tnstc.tn.gov.in','9876100001','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','driver','active'),
('Selvam P','driver2@tnstc.tn.gov.in','9876100002','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','driver','active');

-- Sample Conductor user
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`status`) VALUES
('Murugan S','conductor1@tnstc.tn.gov.in','9876200001','$2y$10$hVCClR4irKRkKJ6QXQOEuewxYqy7lS7empimaazEcNhSin8ttC8KS','conductor','active');

-- 7 Depots (manager_id set after user creation)
INSERT INTO `depots` (`depot_name`,`location`,`district`,`manager_id`,`latitude`,`longitude`) VALUES
('Thamirabarani Depot','Vannarpettai, Tirunelveli','Tirunelveli',3,8.7139,77.7567),
('Bye-Pass Depot','Vannarpettai, Tirunelveli','Tirunelveli',4,8.7200,77.7480),
('Kattabomman Nagar Depot','KTC Nagar, Tirunelveli','Tirunelveli',5,8.7050,77.7600),
('Cheranmahadevi Depot','Cheranmahadevi, Tirunelveli','Tirunelveli',6,8.6800,77.7300),
('Valliyoor Depot','Valliyoor, Tirunelveli','Tirunelveli',7,8.3862,77.6253),
('Thisayanvilai Depot','Thisayanvilai, Tirunelveli','Tirunelveli',8,8.3200,77.8700),
('Papanasam Depot','Papanasam, Tirunelveli','Tirunelveli',9,8.9619,77.3490);

-- Buses
INSERT INTO `buses` (`bus_number`,`depot_id`,`bus_type`,`total_seats`,`status`) VALUES
('TN72 A 0001',1,'ordinary',45,'active'),
('TN72 A 0002',1,'express',40,'active'),
('TN72 B 0003',2,'ordinary',45,'active'),
('TN72 B 0004',2,'super_express',38,'active'),
('TN72 C 0005',3,'ordinary',45,'active'),
('TN72 D 0006',4,'express',40,'active'),
('TN72 E 0007',5,'ordinary',45,'active'),
('TN72 F 0008',6,'mini',25,'active'),
('TN72 G 0009',7,'ordinary',45,'active'),
('TN72 A 0010',1,'ac',36,'maintenance');

-- Routes
INSERT INTO `routes` (`route_number`,`source`,`destination`,`distance`,`estimated_time`) VALUES
('58A','Tirunelveli Junction','Valliyoor',35,75),
('58B','Tirunelveli Junction','Thisayanvilai',60,110),
('77','Tirunelveli Junction','Papanasam',55,100),
('99','Palayamkottai','Cheranmahadevi',25,50),
('110','Tirunelveli Junction','Nagercoil',80,150),
('12','Tirunelveli Junction','Nanguneri',45,90),
('34','Palayamkottai','Ambasamudram',30,65);

-- Route Stops for Route 58A
INSERT INTO `route_stops` (`route_id`,`stop_name`,`stop_order`,`arrival_time`) VALUES
(1,'Tirunelveli Junction',1,'06:00:00'),
(1,'Vannarpettai',2,'06:15:00'),
(1,'Panpoli',3,'06:30:00'),
(1,'Puliyarai',4,'06:45:00'),
(1,'Radhapuram',5,'07:00:00'),
(1,'Valliyoor',6,'07:15:00');

-- Route Stops for Route 77
INSERT INTO `route_stops` (`route_id`,`stop_name`,`stop_order`,`arrival_time`) VALUES
(3,'Tirunelveli Junction',1,'07:00:00'),
(3,'Palayamkottai',2,'07:15:00'),
(3,'Ambasamudram',3,'07:45:00'),
(3,'Papanasam',4,'08:40:00');

-- Drivers (user_id 11 = Rajan Kumar, 12 = Selvam P)
INSERT INTO `drivers` (`user_id`,`license_number`,`depot_id`,`status`) VALUES
(11,'TN72-DL-001234',1,'active'),
(12,'TN72-DL-005678',2,'active');

-- Conductors (user_id 13 = Murugan S)
INSERT INTO `conductors` (`user_id`,`depot_id`,`status`) VALUES
(13,1,'active');

-- Schedules
INSERT INTO `schedules` (`bus_id`,`route_id`,`driver_id`,`conductor_id`,`departure_time`,`arrival_time`,`travel_date`,`status`) VALUES
(1,1,1,1,'06:00:00','07:15:00',CURDATE(),'scheduled'),
(2,3,2,NULL,'07:00:00','08:40:00',CURDATE(),'scheduled'),
(3,2,NULL,NULL,'08:00:00','09:50:00',CURDATE(),'scheduled'),
(5,4,NULL,NULL,'09:00:00','09:50:00',CURDATE(),'scheduled'),
(7,1,NULL,NULL,'10:00:00','11:15:00',CURDATE(),'scheduled');

-- Live tracking seed
INSERT INTO `live_tracking` (`bus_id`,`latitude`,`longitude`) VALUES
(1,8.7280,77.7210),
(2,8.7139,77.7567),
(3,8.7200,77.7480);

-- Sample complaint (passenger_id 10 = Arun Kumar)
INSERT INTO `complaints` (`passenger_id`,`depot_id`,`category`,`description`,`status`) VALUES
(10,1,'delay','Bus TN72 A 0001 was 30 minutes late on 18-May-2026 morning.','open');

-- Bus Stops Seed Data
INSERT INTO `bus_stops` (`stop_name`) VALUES
('Ariyakulam'),
('Balabagya Nagar North'),
('Burkitmanagaram'),
('C N Village'),
('Chellathai Nagar'),
('Chidambaram Nagar, Keelanatham'),
('Gomathy Nagar, Balabagya Nagar North'),
('Gomathy Nagar, Manimoorthispuram'),
('K.t.c Nagar'),
('Karaieruppu'),
('Kayalpattinam, Thirunagar, Tirunelveli Town'),
('Lalugapuram'),
('Manappadaividu, Thoothukudi'),
('Manimoorthispuram'),
('Mehalingapuram, Selva Vignesh Nagar'),
('Melakarai New Colony'),
('Melakulam'),
('Melapalayam'),
('Naranammalpuram, Thoothukudi'),
('Palayamkottai'),
('Palayanchettikulam'),
('Palayapettai'),
('Poyalan Nagar'),
('Ramnagar, Thattarmadam'),
('Santhi Nagar'),
('Selva Vignesh Nagar'),
('Senthimangalam'),
('Sharon Nagar'),
('Sripuram, Thirunagar, Tirunelveli Town'),
('Sugar Mill Colony, Sugar Mill Colony, Balabagya Nagar South, Tirunelveli Town'),
('Thachanallur'),
('Thalavaaipuram'),
('Thattarmadam, Thachanallur'),
('Thimmarajapuram'),
('Thirunagar, Tirunelveli Town'),
('Thiyagarajanagar'),
('Tirunelveli'),
('Tirunelveli Junction'),
('Tirunelveli Town'),
('Tvs Nagar'),
('Udaya Nagar'),
('V.m.chatram'),
('Vanarapettai');

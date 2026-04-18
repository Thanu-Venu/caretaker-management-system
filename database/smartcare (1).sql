-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: smartcare1
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','manager','client','caretaker') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_accounts_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'Thanushya_Venugoban','thanu.venu28@gmail.com','$2y$10$c3dw8EhzPH1.WIubp4p1qeAcuFcIyJ1Qf0VgegiEUcZqPuDagneXu','admin','Active','2026-01-20 21:47:05','2026-04-16 02:15:27'),(2,'Nanduni','nanduni@gmail.com','$2y$10$0JclTI6/iGjsCY0anbGOj.L49mSDQsP5vT1Dq9bZNIcmMkoyBwmX6','manager','Active','2026-01-20 22:02:23','2026-04-16 02:26:32'),(77,'miss','miss@gmail.com','$2y$10$CUwouYe6Uk1yoDFma0P1zePX/MRNeG0uRij8S0aWuXFH8SXEKMLei','manager','Active','2026-04-15 18:33:04','2026-04-16 02:16:10'),(78,'Rajasekar Satheesan','satheesan@gmail.com','$2y$10$8Ra11N4.TEVYqAd/XeeajeFJ5v4gzxCPjTg4uQQXCq5QIqwjuxtsO','client','Active','2026-04-15 18:34:41','2026-04-16 02:23:04'),(82,'Kavitha Rajendran','kavitha@gmail.com','$2y$10$jqzOD95UA9pwtAn1sn2qze1JBHufGei0MP9LOmGgymnmEm1GjIrNq','caretaker','Active','2026-04-15 10:25:15','2026-04-16 02:14:51'),(83,'Nivetha Suresh','nivetha@gmail.com','$2y$10$ooHHjQozEEHkBlHF42jSc.PE8RYBMnWzxqYkXlbTwD6pwoxV8ZGrG','caretaker','Active','2026-04-15 10:31:00','2026-04-16 02:14:19'),(84,'Nadeesha Perera','nadeesha@gmail.com','$2y$10$kRQmJMOQrJYwN5o0l.Ps8OPJNIlIoBB.W3b.BKkjVk0lawrnH/rQu','caretaker','Active','2026-04-15 10:31:00','2026-04-16 02:14:35'),(85,'Indrani Silva','indrani@gmail.com','$2y$10$rgxucoeaf98LohjWkSb1QedutpC/0ixXsr26aHG6IgNqnFWyreM8q','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:14:00'),(86,'Piumi Gunawardena','piumi@gmail.com','$2y$10$0Th9RY0Cda9Cpi9XNavpveWLMAYzNtx9ExPYWI8FHouz4B/7Z6aWa','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:13:44'),(87,'Tharshini Kumar','tharshini@gmail.com','$2y$10$UfvllvjO3T.nLjCFxbG2JOH7SY0LxMn2bhZTvtJYdFG0xe.ekrDPe','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:13:23'),(88,'Meena Nadarajah','meena@gmail.com','$2y$10$VMtdRRx56LNE7QiQg94B4ODyeB/B2wiAt5BPE.4MF9.WK3URxBQcy','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:13:06'),(89,'Rajesh Nadarajah','rajesh@gmail.com','$2y$10$4ZWGs5zxkAIYVEEl/Izas.aWlqbGrM4hWH.XlQmSD3KYOLG4h3Aw2','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:12:52'),(90,'Vasanth Thiruchelvam','vasanth@gmail.com','$2y$10$TPp98KDIzxLU5yCsOQHelunmHzX34nXuBl5CcfRghXZpJaeuSkE1u','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:12:36'),(91,'Chamari Perera','chamari@gmail.com','$2y$10$gJsQ/XCmyveC6vloDAwb1.VfyiGHiUkLDP2cTjzOxfv0DFzQKalqK','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:12:20'),(92,'Kusum Wickramasinghe','kusum@gmail.com','$2y$10$K3nzLHD4VMvtlZLSwBjaSOHTxswbbEVR3u6UWYhdDFzqg4itdSTZ.','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:12:04'),(94,'Malani Jayawardena','malani@gmail.com','$2y$10$hLwjfonPzhR281asB9w9tO5oCFrhvJWE/cEDWQ5KbWujsUIFfFhe2','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:11:51'),(95,'Kumari Perera','kumari@gmail.com','$2y$10$82S8Xjq3ZNxR/vYHQ60UduDE33wuxCCGcrNdsXdGv1D56G8jFoXiG','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:11:38'),(96,'Sunetha Silva','sunetha@gmail.com','$2y$10$rzhct9L2yG8mtSjqa3fGEeZ2LKiloycpLH7pIX2Al8CLghRrwEKPO','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:11:24'),(97,'Nirmala Gunawardena','nirmala@gmail.com','$2y$10$.yfdHEUxeC0VLMeT.YN4mOBrdWVR2D4.q4AQZekKo5ZpLzzmr0/SW','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:10:41'),(98,'Lakshmi Murugan','lakshmi@gmail.com','$2y$10$8SkGZ.fJAN.kohEvCRv7Qelfo7DRFvicnwCCGUDj4yo4hBQy6dJhq','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:10:24'),(99,'Revathi Thiruchelvam','revathi@gmail.com','$2y$10$d4OtTuyrH2.DKVE3b7/00ekH.vT/yFPrde3tNyPJdly9LtS0nc5s.','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:10:09'),(100,'Jayanthi Yogendran','jayanthi@gmail.com','$2y$10$ZJvPwqd8LrnCZtjJ65RJ8ehLElefzLNS3g5NdDtK82xmoM8jLv7DC','caretaker','Active','2026-04-15 18:43:31','2026-04-16 02:09:54'),(101,'Rajasekar Kajan','kajan@gmail.com','$2y$10$GC1iq3EB0TcjlhGSIbWQteb7dBeUTf2GEVuFr3EMvkSK7XO0QvWlS','client','Active','2026-04-15 19:42:59','2026-04-16 02:23:49');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `target_role` enum('users','caregiver','client','All') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (10,'Leave Update','Leave for 3 days becuse of the new year','',1,'2026-04-15 20:37:53'),(11,'Meeting','Year end meeting','',1,'2026-04-15 21:11:48');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_reassignments`
--

DROP TABLE IF EXISTS `booking_reassignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_reassignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL COMMENT 'Booking being reassigned',
  `old_caretaker_id` int NOT NULL COMMENT 'Original caretaker on leave',
  `new_caretaker_id` int NOT NULL COMMENT 'Replacement caretaker',
  `start_date` date NOT NULL COMMENT 'Reassignment period start',
  `end_date` date NOT NULL COMMENT 'Reassignment period end',
  `reassigned_by` int NOT NULL COMMENT 'HR user who approved the reassignment',
  `reassigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When reassignment was created',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'HR note about the reassignment',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reassign_booking` (`booking_id`),
  KEY `idx_reassign_old_caretaker` (`old_caretaker_id`),
  KEY `idx_reassign_new_caretaker` (`new_caretaker_id`),
  KEY `idx_reassign_dates` (`start_date`,`end_date`),
  KEY `idx_reassign_replacement_period` (`new_caretaker_id`,`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_reassignments`
--

LOCK TABLES `booking_reassignments` WRITE;
/*!40000 ALTER TABLE `booking_reassignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_reassignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `service_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `basis` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `duration` int NOT NULL,
  `preferred_time` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `booking_date` date NOT NULL,
  `service_start_date` date DEFAULT NULL COMMENT 'Agreed service start date',
  `service_location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `street` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address_line1` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `address_line2` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `customization` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `total_payment` decimal(10,2) NOT NULL,
  `status` enum('Requested','Payment_Requested','Advance_Paid','Accepted','Change_Requested','Rejected','Cancelled','Completed','Reschedule_Requested') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `advance_paid_date` datetime DEFAULT NULL COMMENT 'When advance payment was completed',
  `advance_months` int DEFAULT '0' COMMENT 'Number of months covered by advance payment',
  `total_months` int DEFAULT '0' COMMENT 'Total billing months (years converted to months)',
  `advance_balance` decimal(10,2) DEFAULT '0.00' COMMENT 'Monetary value of prepaid service period',
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `cancelled_at` datetime DEFAULT NULL,
  `customization_hours` int NOT NULL DEFAULT '0',
  `customization_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `caretaker_changed_once` tinyint(1) NOT NULL DEFAULT '0',
  `refund_status` enum('none','pending','approved','declined','completed') COLLATE utf8mb4_general_ci DEFAULT 'none' COMMENT 'Refund processing status',
  `advance_amount` decimal(10,2) DEFAULT '0.00' COMMENT 'Advance payment amount received',
  `service_days_used` int DEFAULT '0' COMMENT 'Number of days/months of service used before cancellation',
  PRIMARY KEY (`id`),
  KEY `idx_bookings_client` (`client_id`),
  KEY `idx_bookings_caretaker` (`caretaker_id`),
  KEY `idx_bookings_status` (`status`),
  KEY `idx_bookings_date` (`booking_date`),
  KEY `idx_bookings_service_start` (`service_start_date`),
  KEY `idx_bookings_advance_paid` (`advance_paid_date`),
  KEY `idx_bookings_refund_status` (`refund_status`),
  CONSTRAINT `fk_bookings_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (17,9,10,'Elder Care','Monthly',2,'Morning (8am - 12pm)','2026-04-17','2026-04-17',NULL,'Colombo','Muthaliyar Kovilady ,','Alaveddy Center,Alaveddy','','40000','',96000.00,'Cancelled','2026-04-15 18:46:20',NULL,1,2,48000.00,'Dont need ','2026-04-16 01:09:24',2,36000.00,0,'pending',0.00,0),(18,9,4,'Babysitter','Daily',1,'Morning (8am - 12pm)','2026-04-13','2026-04-13',NULL,'Colombo','Muthaliyar Kovilady ,','Alaveddy Center,Alaveddy','','40000','',1320.00,'Completed','2026-04-15 19:13:21',NULL,0,0,0.00,NULL,NULL,0,0.00,0,'none',0.00,0),(19,9,18,'Maid','Hourly',6,'08:00','2026-04-16','2026-04-16',NULL,'Colombo','Colombo main street 18','dehiwala fly over ','','1400','',3000.00,'Accepted','2026-04-15 19:31:57','2026-04-16 01:04:22',0,0,0.00,NULL,NULL,0,0.00,0,'none',0.00,0),(20,9,6,'Babysitter','Daily',17,'Morning (8am - 12pm)','2026-04-20','2026-04-20',NULL,'Colombo','Colombo 07','wellawatta daya road','','1400','I need extratime from 12 to 1',27540.00,'Accepted','2026-04-15 20:16:15','2026-04-16 02:01:36',0,0,16200.00,NULL,NULL,1,5100.00,0,'none',0.00,0),(21,9,14,'Maid','Hourly',3,'08:01','2026-04-20','2026-04-20',NULL,'Kandy','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',1500.00,'Reschedule_Requested','2026-04-16 02:32:13',NULL,0,0,0.00,NULL,NULL,0,0.00,0,'none',0.00,0),(22,9,16,'Maid','Daily',10,'Full Time (8am - 5pm)','2026-04-16','2026-04-16',NULL,'Jaffna','303/10,mannar road paddanichoor vavuniya','B10','','43000','',20000.00,'Accepted','2026-04-09 02:40:22','2026-04-09 08:21:40',0,0,0.00,NULL,NULL,0,0.00,0,'none',0.00,0),(23,9,5,'Babysitter','Daily',20,'Full Time (8am - 5pm)','2026-04-20','2026-04-20',NULL,'Matara','303/10,mannar road paddanichoor vavuniya','B10','','43000','',44000.00,'Requested','2026-04-16 02:56:37',NULL,0,0,22000.00,NULL,NULL,0,0.00,0,'none',0.00,0);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caretaker_profile_change_requests`
--

DROP TABLE IF EXISTS `caretaker_profile_change_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caretaker_profile_change_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caretaker_id` int NOT NULL,
  `requested_name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `requested_email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `requested_phone` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `requested_experience` varchar(120) COLLATE utf8mb4_general_ci DEFAULT '',
  `requested_location` varchar(120) COLLATE utf8mb4_general_ci DEFAULT '',
  `requested_qualifications` text COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `admin_note` text COLLATE utf8mb4_general_ci,
  `reviewed_by` int DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_profile_change_caretaker` (`caretaker_id`),
  KEY `idx_profile_change_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caretaker_profile_change_requests`
--

LOCK TABLES `caretaker_profile_change_requests` WRITE;
/*!40000 ALTER TABLE `caretaker_profile_change_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `caretaker_profile_change_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caretakers`
--

DROP TABLE IF EXISTS `caretakers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caretakers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `service_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `experience` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qualifications` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default.jpg',
  `location` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_caretakers_account_id` (`account_id`),
  CONSTRAINT `fk_caretakers_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caretakers`
--

LOCK TABLES `caretakers` WRITE;
/*!40000 ALTER TABLE `caretakers` DISABLE KEYS */;
INSERT INTO `caretakers` VALUES (1,82,'Kavitha Rajendran','kavitha@gmail.com','$2y$10$jqzOD95UA9pwtAn1sn2qze1JBHufGei0MP9LOmGgymnmEm1GjIrNq','0778437599','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','1776268514_e39bbe1c.png','Jaffna',NULL,'2026-04-15 10:25:15'),(2,83,'Nivetha Suresh','nivetha@gmail.com','$2y$10$ooHHjQozEEHkBlHF42jSc.PE8RYBMnWzxqYkXlbTwD6pwoxV8ZGrG','0769400150','Babysitter','Active','2 years','Childcare Training | First Aid | Baby Care | Safe & Clean Environment','1776268860_c14a8776.webp','Vavuniya',NULL,'2026-04-15 10:31:00'),(3,84,'Nadeesha Perera','nadeesha@gmail.com','$2y$10$kRQmJMOQrJYwN5o0l.Ps8OPJNIlIoBB.W3b.BKkjVk0lawrnH/rQu','0769400150','Babysitter','Active','7 years','Babysitting Care | First Aid Knowledge | Play & Learning | Safe Care','1776268860_profile.png','Colombo',NULL,'2026-04-15 10:31:00'),(4,85,'Indrani Silva','indrani@gmail.com','$2y$10$rgxucoeaf98LohjWkSb1QedutpC/0ixXsr26aHG6IgNqnFWyreM8q','0769400150','Babysitter','Active','1 years','Babysitting Level 1 | CPR & First Aid | Infant & Child Care | Safety & Hygiene','1776271059_0069a970.jpg','Colombo',NULL,'2026-04-15 11:07:39'),(5,86,'Piumi Gunawardena','piumi@gmail.com','$2y$10$0Th9RY0Cda9Cpi9XNavpveWLMAYzNtx9ExPYWI8FHouz4B/7Z6aWa','0747895654','Babysitter','Active','5 years','Professional Babysitter | CPR Certified | Child Care | Play & Safety','1776271140_2190b2e4.webp','Matara',NULL,'2026-04-15 11:09:01'),(6,87,'Tharshini Kumar','tharshini@gmail.com','$2y$10$UfvllvjO3T.nLjCFxbG2JOH7SY0LxMn2bhZTvtJYdFG0xe.ekrDPe','0767825498','Babysitter','Active','4 years','Babysitting Care | First Aid Knowledge | Play & Learning | Safe Care','1776271226_78eff5b0.jpg','Colombo',NULL,'2026-04-15 11:10:26'),(7,88,'Meena Nadarajah','meena@gmail.com','$2y$10$VMtdRRx56LNE7QiQg94B4ODyeB/B2wiAt5BPE.4MF9.WK3URxBQcy','0769400150','Elder Care','Active','4 years','Elder Care Level 2 | CPR & First Aid | Medication Support | Personal Hygiene','1776272060_10185439.jpg','Jaffna',NULL,'2026-04-15 11:24:20'),(8,89,'Rajesh Nadarajah','rajesh@gmail.com','$2y$10$4ZWGs5zxkAIYVEEl/Izas.aWlqbGrM4hWH.XlQmSD3KYOLG4h3Aw2','0769874598','Elder Care','Active','5 years','Senior Care | First Aid | Daily Assistance | Mobility Support','1776272309_6c86c5cc.jpg','Vavuniya',NULL,'2026-04-15 11:28:29'),(9,90,'Vasanth Thiruchelvam','vasanth@gmail.com','$2y$10$TPp98KDIzxLU5yCsOQHelunmHzX34nXuBl5CcfRghXZpJaeuSkE1u','0769478155','Elder Care','Active','5 years','Skilled Elder Care | Personal Care | Mobility Assistance | Clean Environment','1776272545_0739d86c.jpg','Kandy',NULL,'2026-04-15 11:32:25'),(10,91,'Chamari Perera','chamari@gmail.com','$2y$10$gJsQ/XCmyveC6vloDAwb1.VfyiGHiUkLDP2cTjzOxfv0DFzQKalqK','0769400150','Elder Care','Active','1 year','Elder Care Services | First Aid Knowledge | Daily Support | Hygiene & Safety','1776274035_e9889ae5.jpg','Colombo',NULL,'2026-04-15 11:35:21'),(11,92,'Kusum Wickramasinghe','kusum@gmail.com','$2y$10$K3nzLHD4VMvtlZLSwBjaSOHTxswbbEVR3u6UWYhdDFzqg4itdSTZ.','0769895782','Elder Care','Active','4 years','Elder Care Services | First Aid Knowledge | Daily Support | Hygiene & Safety','1776272840_f965fad8.jpg','Matara',NULL,'2026-04-15 11:37:20'),(12,94,'Malani Jayawardena','malani@gmail.com','$2y$10$hLwjfonPzhR281asB9w9tO5oCFrhvJWE/cEDWQ5KbWujsUIFfFhe2','0778495687','Elder Care','Active','2 years','Senior Care | First Aid | Daily Assistance | Mobility Support','1776273010_e78bd450.png','Colombo',NULL,'2026-04-15 11:40:10'),(13,95,'Kumari Perera','kumari@gmail.com','$2y$10$82S8Xjq3ZNxR/vYHQ60UduDE33wuxCCGcrNdsXdGv1D56G8jFoXiG','0769400150','Maid','Active','4 years','Maid Services | Cleaning & Housekeeping | Hygiene & Organization | Home Care','1776273120_d6f5b648.webp','Matara',NULL,'2026-04-15 11:42:00'),(14,96,'Sunetha Silva','sunetha@gmail.com','$2y$10$rzhct9L2yG8mtSjqa3fGEeZ2LKiloycpLH7pIX2Al8CLghRrwEKPO','0769400150','Maid','Active','2 years','Housekeeping Support | Cleaning | Laundry | Home Maintenance','1776273345_b5ef9031.jpg','Kandy',NULL,'2026-04-15 11:45:45'),(15,97,'Nirmala Gunawardena','nirmala@gmail.com','$2y$10$.yfdHEUxeC0VLMeT.YN4mOBrdWVR2D4.q4AQZekKo5ZpLzzmr0/SW','0769400158','Maid','Active','1 year','Professional Maid | Cleaning Services | Organized Living | Hygiene Care','1776273639_49796732.jpg','Colombo',NULL,'2026-04-15 11:50:39'),(16,98,'Lakshmi Murugan','lakshmi@gmail.com','$2y$10$8SkGZ.fJAN.kohEvCRv7Qelfo7DRFvicnwCCGUDj4yo4hBQy6dJhq','0769400150','Maid','Active','7 years','Home Care Assistant | Daily Cleaning | Laundry | Clean Environment','1776273692_ea2543b3.jpg','Jaffna',NULL,'2026-04-15 11:51:32'),(17,99,'Revathi Thiruchelvam','revathi@gmail.com','$2y$10$d4OtTuyrH2.DKVE3b7/00ekH.vT/yFPrde3tNyPJdly9LtS0nc5s.','0717895648','Maid','Active','4 years','Skilled Maid | House Cleaning | Kitchen Help | Hygiene Focus','1776273775_3f2678ed.jpg','Vavuniya',NULL,'2026-04-15 11:52:55'),(18,100,'Jayanthi Yogendran','jayanthi@gmail.com','$2y$10$ZJvPwqd8LrnCZtjJ65RJ8ehLElefzLNS3g5NdDtK82xmoM8jLv7DC','0728574963','Maid','Active','5 years','Maid Services | Home Cleaning | Organization | Safe & Clean Living','1776273846_87fe9ff6.jpg','Colombo',NULL,'2026-04-15 11:54:06');
/*!40000 ALTER TABLE `caretakers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_requests`
--

DROP TABLE IF EXISTS `change_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `change_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `old_caretaker_id` int NOT NULL,
  `new_caretaker_id` int NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  `hr_note` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cr_booking` (`booking_id`),
  KEY `fk_cr_client` (`client_id`),
  KEY `fk_cr_old_caretaker` (`old_caretaker_id`),
  KEY `fk_cr_new_caretaker` (`new_caretaker_id`),
  CONSTRAINT `fk_cr_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_new_caretaker` FOREIGN KEY (`new_caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cr_old_caretaker` FOREIGN KEY (`old_caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_requests`
--

LOCK TABLES `change_requests` WRITE;
/*!40000 ALTER TABLE `change_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `change_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('client') COLLATE utf8mb4_general_ci DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_clients_account_id` (`account_id`),
  CONSTRAINT `fk_clients_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (9,78,'Rajasekar Satheesan','satheesan@gmail.com','0769400150','1776306352_satheesan.jpg','$2y$10$0QY./FXDLS3ldt1y13y.euqXufNguggNMK06J2GFBYtISgJhDWEQe','client','2026-04-15 18:34:41'),(10,101,'Rajasekar Kajan','kajan@gmail.com','0728596745',NULL,'$2y$10$kq9v5KXGGYCRrNXawiYhju5sFENT47/aqeT000Hf0HUBOCEPqs2T6','client','2026-04-15 19:42:59');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `Id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary key – unique complaint identifier',
  `client_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Name of the client filing the complaint',
  `caretaker_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Name of the caretaker involved',
  `category` enum('Caretaker Behavior','Service Quality','Late Arrival','Unprofessional','Other') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Type or category of the complaint',
  `details` text COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Detailed description of the complaint',
  `status` enum('Open','In Progress','Resolved','Closed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Open' COMMENT 'Current status of the complaint',
  `complaint_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time the complaint was submitted',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table storing client complaints about caretakers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` VALUES (48,'Rajasekar Satheesan','Jayanthi Yogendran','Service Quality','Not like profesional work','Open','2026-04-15 20:33:44');
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ct_complaints`
--

DROP TABLE IF EXISTS `ct_complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ct_complaints` (
  `complaint_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `service_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `service_date` date NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Open',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`complaint_id`),
  KEY `client_id` (`client_id`),
  KEY `caretaker_id` (`caretaker_id`),
  CONSTRAINT `ct_complaints_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ct_complaints_ibfk_2` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ct_complaints`
--

LOCK TABLES `ct_complaints` WRITE;
/*!40000 ALTER TABLE `ct_complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `ct_complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedbacks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `rating` int NOT NULL,
  `feedback` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_feedbacks_booking_id` (`booking_id`),
  KEY `idx_feedbacks_client_id` (`client_id`),
  KEY `idx_feedbacks_caretaker_id` (`caretaker_id`),
  CONSTRAINT `fk_feedbacks_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedbacks`
--

LOCK TABLES `feedbacks` WRITE;
/*!40000 ALTER TABLE `feedbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedbacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history_logs`
--

DROP TABLE IF EXISTS `history_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `section` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role` (`role`),
  KEY `created_at` (`created_at`),
  KEY `section` (`section`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history_logs`
--

LOCK TABLES `history_logs` WRITE;
/*!40000 ALTER TABLE `history_logs` DISABLE KEYS */;
INSERT INTO `history_logs` VALUES (66,4,'Thanushya Venugoban','admin','Added user: miss','Staffs','2026-04-16 00:03:04'),(67,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 3)','Caretakers','2026-04-16 00:43:00'),(68,7,'miss','Manager','Requested advance payment for Booking #19','Pending Requests','2026-04-16 01:02:28'),(69,7,'miss','Manager','Approved payment #17 for Booking #19','Payments','2026-04-16 01:04:22'),(70,7,'miss','Manager','Requested advance payment for Booking #20','Pending Requests','2026-04-16 01:59:14'),(71,7,'miss','Manager','Approved payment #18 for Booking #20','Payments','2026-04-16 02:01:36'),(72,7,'miss','Manager','Approved leave request (Leave ID: 38)','Leaves','2026-04-16 02:32:02'),(73,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 18)','Caretakers','2026-04-16 07:39:54'),(74,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 17)','Caretakers','2026-04-16 07:40:09'),(75,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 16)','Caretakers','2026-04-16 07:40:24'),(76,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 15)','Caretakers','2026-04-16 07:40:41'),(77,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 14)','Caretakers','2026-04-16 07:41:24'),(78,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 13)','Caretakers','2026-04-16 07:41:38'),(79,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 12)','Caretakers','2026-04-16 07:41:51'),(80,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 11)','Caretakers','2026-04-16 07:42:04'),(81,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 10)','Caretakers','2026-04-16 07:42:20'),(82,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 9)','Caretakers','2026-04-16 07:42:36'),(83,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 8)','Caretakers','2026-04-16 07:42:52'),(84,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-04-16 07:43:06'),(85,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 6)','Caretakers','2026-04-16 07:43:23'),(86,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 5)','Caretakers','2026-04-16 07:43:44'),(87,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 4)','Caretakers','2026-04-16 07:44:00'),(88,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 2)','Caretakers','2026-04-16 07:44:19'),(89,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 3)','Caretakers','2026-04-16 07:44:35'),(90,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 1)','Caretakers','2026-04-16 07:44:51'),(91,4,'Thanushya Venugoban','admin','Updated user (ID: 4)','Staffs','2026-04-16 07:45:27'),(92,4,'Thanushya Venugoban','admin','Updated user (ID: 5)','Staffs','2026-04-16 07:45:45'),(93,4,'Thanushya Venugoban','admin','Updated user (ID: 7)','Staffs','2026-04-16 07:46:10'),(94,5,'Nanduni','Manager','Requested advance payment for Booking #22','Pending Requests','2026-04-09 08:10:48'),(95,5,'Nanduni','Manager','Approved payment #19 for Booking #22','Payments','2026-04-09 08:21:40');
/*!40000 ALTER TABLE `history_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_booking_reassignment`
--

DROP TABLE IF EXISTS `leave_booking_reassignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_booking_reassignment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leave_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `old_caretaker_id` int NOT NULL,
  `new_caretaker_id` int NOT NULL,
  `reassign_start` date NOT NULL,
  `reassign_end` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_leave_booking` (`leave_id`,`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_booking_reassignment`
--

LOCK TABLES `leave_booking_reassignment` WRITE;
/*!40000 ALTER TABLE `leave_booking_reassignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_booking_reassignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaves` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leave_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `can_edit_until` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `user_id` int NOT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `replacement_caretaker_id` int DEFAULT NULL,
  `hr_note` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
INSERT INTO `leaves` VALUES (38,'Vacation','2026-04-18','2026-04-20','09:00:00','17:00:00','Family trip','2026-04-16 22:58:08','Approved',18,7,'2026-04-16 02:32:02',13,'');
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_role` enum('admin','Manager','client','caretaker') COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_read` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (267,5,'Manager','New Booking Request','New booking placed.\nBooking #16 | Elder Care| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-16 | Time: Morning (8am - 12pm) | \nDuration: 3 Monthly | \nLocation: Colombo, Muthaliyar Kovilady , | \nTotal: LKR 90,000\nCaretaker: Malani Jayawardena\n','http://localhost/CMA/hr/hr_pending_request?booking_id=16',1,'2026-04-15 18:45:01'),(268,5,'Manager','New Booking Request','New booking placed.\nBooking #17 | Elder Care| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-17 | Time: Morning (8am - 12pm) | \nDuration: 2 Monthly | \nLocation: Colombo, Muthaliyar Kovilady , | \nTotal: LKR 96,000\nCaretaker: Chamari Perera\n','http://localhost/CMA/hr/hr_pending_request?booking_id=17',1,'2026-04-15 18:46:20'),(269,9,'client','Booking Payment Reminder','Your booking #17 is scheduled for tomorrow (2026-04-17). Please complete advance payment and confirmation before the service date.','http://localhost/CMA/client/paymentDetails/17',1,'2026-04-15 18:46:21'),(270,9,'client','Booking Payment Reminder','Your booking #16 is scheduled for tomorrow (2026-04-16). Please complete advance payment and confirmation before the service date.','http://localhost/CMA/client/paymentDetails/16',1,'2026-04-15 18:59:09'),(271,9,'client','Booking Payment Reminder','Your booking #16 is scheduled for tomorrow (2026-04-16). Please complete advance payment and confirmation before the service date.','http://localhost/CMA/client/paymentDetails/16',1,'2026-04-15 18:59:09'),(272,5,'Manager','New Booking Request','New booking placed.\nBooking #18 | Babysitter| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-13 | Time: Morning (8am - 12pm) | \nDuration: 1 Daily | \nLocation: Colombo, Muthaliyar Kovilady , | \nTotal: LKR 1,320\nCaretaker: Indrani Silva\n','http://localhost/CMA/hr/hr_pending_request?booking_id=18',1,'2026-04-15 19:13:21'),(273,5,'Manager','New Booking Request','New booking placed.\nBooking #19 | Maid| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-16 | Time: 08:00 | \nDuration: 6 Hourly | \nLocation: Colombo, Colombo main street 18 | \nTotal: LKR 3,000\nCaretaker: Jayanthi Yogendran\n','http://localhost/CMA/hr/hr_pending_request?booking_id=19',1,'2026-04-15 19:31:57'),(274,9,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #19 | Service: Maid | Date: 2026-04-16 | Time: 08:00 | Duration: 6 Hourly | Caregiver: Jayanthi Yogendran\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=19',1,'2026-04-15 19:32:28'),(275,18,'caretaker','Booking Accepted','Booking #19 has been accepted after payment approval. Client: Rajasekar Satheesan. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=19&tab=upcoming',1,'2026-04-15 19:34:22'),(276,9,'client','Payment Approved','HR approved your payment for booking #19. Amount: LKR 3,000.00.','http://localhost/CMA/client/payments?tab=paid_history',1,'2026-04-15 19:34:22'),(277,9,'client','Booking Cancelled','Your booking #17 has been cancelled.\n\nService: Elder Care\nBooking Date: 2026-04-17\n\nNo refund applicable for this cancellation based on our refund policy.','http://localhost/CMA/client/c_cancelledBookings',1,'2026-04-15 19:39:24'),(278,10,'caretaker','Booking Cancelled','Booking #17 has been cancelled by the client.\n\nService: Elder Care\nBooking Date: 2026-04-17\n\nThis booking is no longer active.','http://localhost/CMA/caretaker/ct_bookings',0,'2026-04-15 19:39:24'),(279,5,'Manager','Booking Cancellation - Action Required','Booking #17 has been cancelled.\n\nClient ID: 9\nService: Elder Care\nCaretaker ID: 10\n\nNo refund applicable.','http://localhost/CMA/hr/refunds',1,'2026-04-15 19:39:24'),(280,5,'Manager','New Booking Request','New booking placed.\nBooking #20 | Babysitter| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-20 | Time: Morning (8am - 12pm) | \nDuration: 17 Daily | \nLocation: Colombo, Colombo 07 | \nTotal: LKR 27,540\nCaretaker: Tharshini Kumar\n','http://localhost/CMA/hr/hr_pending_request?booking_id=20',1,'2026-04-15 20:16:15'),(281,9,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #20 | Service: Babysitter | Date: 2026-04-20 | Time: Morning (8am - 12pm) | Duration: 17 Daily | Caregiver: Tharshini Kumar\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=20',1,'2026-04-15 20:29:14'),(282,6,'caretaker','Booking Accepted','Booking #20 has been accepted after payment approval. Client: Rajasekar Satheesan. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=20&tab=upcoming',0,'2026-04-15 20:31:36'),(283,9,'client','Payment Approved','HR approved your payment for booking #20. Amount: LKR 16,200.00.','http://localhost/CMA/client/payments?tab=paid_history',1,'2026-04-15 20:31:36'),(284,4,'admin','New Complaint','A new complaint was submitted by Rajasekar Satheesan (Caregiver: Jayanthi Yogendran).','http://localhost/CMA/admin/ad_feedback',1,'2026-04-15 20:33:44'),(285,5,'Manager','New Leave Request','Caretaker ID: 18\nType: Vacation\nDates: 2026-04-18 to 2026-04-20\nTime: 09:00 - 17:00\nReason: Family trip','http://localhost/CMA/hr/hr_leave',1,'2026-04-15 20:58:08'),(286,7,'Manager','New Leave Request','Caretaker ID: 18\nType: Vacation\nDates: 2026-04-18 to 2026-04-20\nTime: 09:00 - 17:00\nReason: Family trip','http://localhost/CMA/hr/hr_leave',1,'2026-04-15 20:58:08'),(287,4,'admin','New Leave Request','Caretaker ID: 18\nType: Vacation\nDates: 2026-04-18 to 2026-04-20\nTime: 09:00 - 17:00\nReason: Family trip','http://localhost/CMA/admin/ad_leave',1,'2026-04-15 20:58:08'),(288,18,'caretaker','Leave Approved','Your leave request has been approved.\nPeriod: 2026-04-18 to 2026-04-20\nNote: —','http://localhost/CMA/caretaker/ct_leave',1,'2026-04-15 21:02:02'),(289,5,'Manager','New Booking Request','New booking placed.\nBooking #21 | Maid| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-20 | Time: 08:01 | \nDuration: 3 Hourly | \nLocation: Kandy, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 1,500\nCaretaker: Sunetha Silva\n','http://localhost/CMA/hr/hr_pending_request?booking_id=21',1,'2026-04-16 02:32:13'),(290,7,'Manager','New Booking Request','New booking placed.\nBooking #21 | Maid| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-20 | Time: 08:01 | \nDuration: 3 Hourly | \nLocation: Kandy, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 1,500\nCaretaker: Sunetha Silva\n','http://localhost/CMA/hr/hr_pending_request?booking_id=21',0,'2026-04-16 02:32:13'),(291,5,'Manager','Reschedule Request','Client Rajasekar Satheesan has requested to reschedule booking #21 from 2026-04-20 to 2026-04-21.','http://localhost/CMA/hr/rescheduleRequests',1,'2026-04-16 02:35:40'),(292,5,'Manager','New Booking Request','New booking placed.\nBooking #22 | Maid| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-16 | Time: Full Time (8am - 5pm) | \nDuration: 10 Daily | \nLocation: Jaffna, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 20,000\nCaretaker: Lakshmi Murugan\n','http://localhost/CMA/hr/hr_pending_request?booking_id=22',1,'2026-04-09 02:40:22'),(293,7,'Manager','New Booking Request','New booking placed.\nBooking #22 | Maid| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-16 | Time: Full Time (8am - 5pm) | \nDuration: 10 Daily | \nLocation: Jaffna, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 20,000\nCaretaker: Lakshmi Murugan\n','http://localhost/CMA/hr/hr_pending_request?booking_id=22',0,'2026-04-09 02:40:22'),(294,9,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #22 | Service: Maid | Date: 2026-04-16 | Time: Full Time (8am - 5pm) | Duration: 10 Daily | Caregiver: Lakshmi Murugan\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=22',1,'2026-04-09 02:40:48'),(295,16,'caretaker','Booking Accepted','Booking #22 has been accepted after payment approval. Client: Rajasekar Satheesan. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=22&tab=upcoming',1,'2026-04-09 02:51:40'),(296,9,'client','Payment Approved','HR approved your payment for booking #22. Amount: LKR 20,000.00.','http://localhost/CMA/client/payments?tab=paid_history',1,'2026-04-09 02:51:40'),(297,5,'Manager','New Booking Request','New booking placed.\nBooking #23 | Babysitter| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-20 | Time: Full Time (8am - 5pm) | \nDuration: 20 Daily | \nLocation: Matara, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 44,000\nCaretaker: Piumi Gunawardena\n','http://localhost/CMA/hr/hr_pending_request?booking_id=23',1,'2026-04-16 02:56:37'),(298,7,'Manager','New Booking Request','New booking placed.\nBooking #23 | Babysitter| \nClient: Rajasekar Satheesan (satheesan@gmail.com) |\nDate: 2026-04-20 | Time: Full Time (8am - 5pm) | \nDuration: 20 Daily | \nLocation: Matara, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 44,000\nCaretaker: Piumi Gunawardena\n','http://localhost/CMA/hr/hr_pending_request?booking_id=23',0,'2026-04-16 02:56:37');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `total_booking_amount` decimal(10,2) NOT NULL,
  `customization_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL,
  `remaining_balance` decimal(10,2) DEFAULT '0.00',
  `payment_method` enum('credit_card','debit_card','mobile_wallet','bank_transfer') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_type` enum('advance','reminder','final') COLLATE utf8mb4_general_ci DEFAULT 'advance',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `is_reminder_sent` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (17,19,9,18,3000.00,0.00,3000.00,0.00,'credit_card','advance','approved',NULL,NULL,0,'2026-04-15 19:33:12','2026-04-16 01:04:22'),(18,20,9,6,27540.00,5100.00,16200.00,11340.00,'credit_card','advance','approved',NULL,NULL,0,'2026-04-15 20:30:39','2026-04-16 02:01:36'),(19,22,9,16,20000.00,0.00,20000.00,0.00,'credit_card','advance','approved',NULL,NULL,0,'2026-04-09 02:41:07','2026-04-09 08:21:40');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurring_payments`
--

DROP TABLE IF EXISTS `recurring_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `cycle_number` int NOT NULL COMMENT 'Payment cycle number (1, 2, 3...)',
  `cycle_type` enum('monthly','15_day','daily') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'monthly' COMMENT 'Type of billing cycle',
  `due_date` date NOT NULL COMMENT 'Payment due date',
  `amount` decimal(10,2) NOT NULL COMMENT 'Amount due for this cycle',
  `status` enum('pending','paid','overdue','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL COMMENT 'When payment was completed',
  `payment_id` int DEFAULT NULL COMMENT 'Reference to payments table when paid',
  `reminder_7_days_sent` tinyint(1) DEFAULT '0' COMMENT '7 day reminder sent',
  `reminder_3_days_sent` tinyint(1) DEFAULT '0' COMMENT '3 day reminder sent',
  `reminder_due_date_sent` tinyint(1) DEFAULT '0' COMMENT 'Due date reminder sent',
  `grace_period_end` date DEFAULT NULL COMMENT 'End of 3-day grace period',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recurring_booking` (`booking_id`),
  KEY `idx_recurring_client` (`client_id`),
  KEY `idx_recurring_status` (`status`),
  KEY `idx_recurring_due_date` (`due_date`),
  KEY `idx_recurring_cycle` (`booking_id`,`cycle_number`),
  KEY `fk_recurring_caretaker` (`caretaker_id`),
  CONSTRAINT `fk_recurring_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recurring_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_recurring_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks recurring payment schedules and reminders for bookings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_payments`
--

LOCK TABLES `recurring_payments` WRITE;
/*!40000 ALTER TABLE `recurring_payments` DISABLE KEYS */;
INSERT INTO `recurring_payments` VALUES (25,20,9,6,1,'15_day','2026-05-05',11340.00,'pending',NULL,NULL,0,0,0,'2026-05-08','2026-04-15 20:31:36','2026-04-15 20:31:36');
/*!40000 ALTER TABLE `recurring_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refunds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `cancellation_type` enum('before_service_start','after_service_start','during_recurring','daily_service','auto_nonpayment') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Type of cancellation',
  `total_paid` decimal(10,2) NOT NULL COMMENT 'Total amount paid by client',
  `service_used_amount` decimal(10,2) DEFAULT '0.00' COMMENT 'Value of service already used',
  `cancellation_fee` decimal(10,2) DEFAULT '0.00' COMMENT 'Cancellation fee deducted',
  `refund_amount` decimal(10,2) NOT NULL COMMENT 'Final refund amount',
  `refund_calculation` text COLLATE utf8mb4_general_ci COMMENT 'JSON string with calculation breakdown',
  `status` enum('pending','approved','declined','processed','completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `approved_by` int DEFAULT NULL COMMENT 'HR/Admin user who approved',
  `approved_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL COMMENT 'When refund was actually processed',
  `refund_method` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Bank transfer, cash, etc.',
  `refund_reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Transaction reference number',
  `admin_notes` text COLLATE utf8mb4_general_ci COMMENT 'Internal notes from HR/Admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_refund_booking` (`booking_id`),
  KEY `idx_refund_client` (`client_id`),
  KEY `idx_refund_status` (`status`),
  KEY `idx_refund_created` (`created_at`),
  CONSTRAINT `fk_refund_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_refund_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks refund calculations and processing for cancelled bookings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
INSERT INTO `refunds` VALUES (6,17,9,'before_service_start',0.00,0.00,5000.00,0.00,'{\"scenario\":\"Cancellation before service start\",\"advance_paid\":0,\"total_paid\":0,\"approved_payments\":0,\"pending_payments\":0,\"cancellation_fee\":5000,\"calculation\":\"0 - 5000 = 0\",\"message\":\"Service has not started yet. Refund processed after deducting cancellation fee.\"}','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-15 19:39:24','2026-04-15 19:39:24');
/*!40000 ALTER TABLE `refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reschedule_requests`
--

DROP TABLE IF EXISTS `reschedule_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reschedule_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `old_date` date NOT NULL,
  `new_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  `hr_note` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `fk_rr_booking` (`booking_id`),
  KEY `fk_rr_client` (`client_id`),
  CONSTRAINT `fk_rr_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rr_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reschedule_requests`
--

LOCK TABLES `reschedule_requests` WRITE;
/*!40000 ALTER TABLE `reschedule_requests` DISABLE KEYS */;
INSERT INTO `reschedule_requests` VALUES (2,21,9,'2026-04-20','2026-04-21','','pending','2026-04-16 02:35:39',NULL,NULL);
/*!40000 ALTER TABLE `reschedule_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','Manager') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_account_id` (`account_id`),
  CONSTRAINT `fk_users_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,1,'Thanushya_Venugoban','thanu.venu28@gmail.com','$2y$10$M85ZBJZ91pFPi3sWwEgLiudhJxFdC.2sGhJwPrMcW0EUVHQKkzOhO','admin','Active','0702248119','697045605c41b.jpg','2026-01-20 21:47:05'),(5,2,'Nanduni','nanduni@gmail.com','$2y$10$0JclTI6/iGjsCY0anbGOj.L49mSDQsP5vT1Dq9bZNIcmMkoyBwmX6','Manager','Active','0773607650','697048f0bb99b.jpg','2026-01-20 22:02:23'),(7,77,'miss','miss@gmail.com','$2y$10$CUwouYe6Uk1yoDFma0P1zePX/MRNeG0uRij8S0aWuXFH8SXEKMLei','Manager','Active','0769400150',NULL,'2026-04-15 18:33:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-16  8:37:08

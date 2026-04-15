-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: smartcare
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
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','client','caretaker') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_accounts_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'Thanushya Venugoban','thanu28@gmail.com','$2y$10$M85ZBJZ91pFPi3sWwEgLiudhJxFdC.2sGhJwPrMcW0EUVHQKkzOhO','admin','Active','2026-01-21 03:17:05','2026-03-08 08:15:46'),(2,'nanduni','nanduni@gmail.com','$2y$10$yTeb45tZN4DneyGv7KeciujsHSsJrR2ZgiGOUpwubZVMF3ni5iCPi','manager','Active','2026-01-21 03:32:23','2026-03-08 08:15:46'),(4,'Thanushya Venugoban','thanu.venu28@gmail.com','$2y$10$rMfcf206RoBO1lE9K2E7MeKZqoms/aIbnZ8yFG1bXX6BAORUxwRMm','client','Active','2025-12-31 04:06:31','2026-03-08 08:16:30'),(5,'piyula xdfsf','piyu@gmail.com','$2y$10$RgVZsIZFH/M2LMmd688PSu0VYh7um9XKc0vr4WpeYSkjlcacAFzVa','client','Active','2026-01-08 06:03:04','2026-03-08 08:16:30'),(6,'shinthurie kuganathan','shinthu@gmail.com','$2y$10$tDmb7Z5hLXKpcB5pC63E5.O8stiB2vv/S8AOhm4PYDUTB.FaUbWv2','client','Active','2026-01-23 02:04:42','2026-03-08 08:16:30'),(7,'shaganjaly  sivanenthiran','shaga@gmail.com','$2y$10$OgZdyQNQYdzzU2bkEMOVDeJOEUIXQ8BTCOi1kbBZKN9bpDg0oW3xG','client','Active','2026-01-24 02:56:40','2026-03-08 08:16:30'),(8,'vishnugah ramanathan','vishnu@gmail.com','$2y$10$xGEa7iH2n7G/kGHfb4t87OYokM1VGClzmcYwWE.973Q7xKDFXk7y2','client','Active','2026-01-31 15:35:08','2026-03-08 08:16:30'),(9,'sulojan rajkumar','sulojan@gmail.com','$2y$10$viCbnW7nkprZ/4/zZbIaOO..GgUKEAQYDPAFKYoFGbMvRtL98C/CG','client','Active','2026-02-26 08:43:32','2026-03-08 08:16:30'),(11,'sujany','suja@gmail.com','$2y$10$6JE4dWOzRIB3Ell3gMP3XOFrC302mN1uKaYtEwgkFfOIMIwrZMs7G','caretaker','Active','2026-01-20 14:30:19','2026-03-08 08:16:45'),(12,'evon','evon@gmail.com','$2y$10$77JG0VUup.YLRJoh7323SuNaG9BTjXeTJBZ87bddpYkrWuHAHMSS2','caretaker','Active','2026-01-20 14:32:35','2026-03-08 08:16:45'),(13,'pugalanthi','pugal@gmail.com','$2y$10$0I45XXxkaxOBwUnJTuTKVeHUT6VQLxIc7GGHa92Y1foolmDBqEUoy','caretaker','Active','2026-01-20 17:15:06','2026-03-08 08:16:45'),(14,'vijay','vijay@gmail.com','$2y$10$ZSTy1Nh4OF09XrfV5.cGOesSiNiUz6J5PgGNs/IIyvaQSm52NPVWS','caretaker','Active','2026-01-23 02:18:12','2026-03-08 08:16:45'),(15,'bhavani','bhavani@gmail.com','$2y$10$deJeJG8/BLDYsrhnfa/EAeMug.DoJVdzsg6aZxTT3uXk5DHQgaz9e','caretaker','Active','2026-01-24 09:26:50','2026-03-08 08:16:45'),(16,'Nimal Perera','ct01@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(17,'Shanthi Silva','ct02@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(18,'Sunil Fernando','ct03@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(19,'Kusum Jay','ct04@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(20,'Rohana Dias','ct05@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Inactive','2026-01-25 02:04:30','2026-03-08 08:16:45'),(21,'Saman Perera','ct06@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(22,'Nirosha Kumari','ct07@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(23,'Upali Silva','ct08@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(24,'Indika Fernando','ct09@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(25,'Padmini Weerasinghe','ct10@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(26,'Malini Jay','ct11@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(27,'Hasini Perera','ct12@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(28,'Roshani Silva','ct13@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(29,'Thilini Jay','ct14@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Inactive','2026-01-25 02:04:30','2026-03-08 08:16:45'),(30,'Sanduni Fernando','ct15@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(31,'Ishara Perera','ct16@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(32,'Nadeesha Kumari','ct17@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(33,'Dilani Silva','ct18@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(34,'Sachini Jay','ct19@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(35,'Dinithi Perera','ct20@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(36,'Suresh Fernando','ct21@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(37,'Kavitha Raj','ct22@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(38,'Ramesh Kumar','ct23@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(39,'Anusha Silva','ct24@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Inactive','2026-01-25 02:04:30','2026-03-08 08:16:45'),(40,'Janaki Perera','ct25@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(41,'Priyanka Fernando','ct26@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(42,'Lakshmi Devi','ct27@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(43,'Saroja Kumari','ct28@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(44,'Kumara Silva','ct29@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(45,'Samanthi Jay','ct30@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','caretaker','Active','2026-01-25 02:04:30','2026-03-08 08:16:45'),(46,'shanuja venugoban','shanu6@gmail.com','$2y$10$0fwfUJxrHd6coCsaBJOToOs6etPpy1TtFQFHHQNuJ8zF2RXvx1kI2','caretaker','Active','2026-01-28 02:09:15','2026-03-08 08:16:45'),(47,'amala','amala@gmail.com','$2y$10$8wN1haWE42/zHGr3XolPD.oM5L1x3kns/qJPRR22wkxUIrT.kb4ES','caretaker','Active','2026-01-30 00:57:53','2026-03-08 08:16:45'),(48,'akshara','aksha@gmail.com','$2y$10$pzDQnKAWYiBaKisHJ/oVB.rZtpxbU/PUI/QAGmGFm.TLaUP7zb61e','caretaker','Active','2026-01-31 10:19:41','2026-03-08 08:16:45'),(49,'banumathi','banu@gmail.com','$2y$10$.IraKdV14Q6XEy87eoOV.e6UDlx1GG9hDpltYun3whJ0hl5wFZTQC','caretaker','Active','2026-01-31 10:25:07','2026-03-08 08:16:45'),(50,'abinaya','abi@gmail.com','$2y$10$wfj1cKw9ZMpigLwYpWWb9u4v0KWn.Kds2IVElIyuc/KNS7JcqBTwK','caretaker','Active','2026-03-07 09:53:09','2026-03-08 08:16:45'),(51,'shaithu','shaithra@gmail.com','$2y$10$dXCfDx/PFzwMX1/9pIfQUeTwDwTKhBpjo3TcUMUx2M65JG3rZJlIa','caretaker','Active','2026-03-07 09:54:05','2026-03-08 08:16:45'),(74,'bhavani','bhavani1@gmail.com','$2y$10$Td16ucIy6c9GqIbCC5gd7Og3WIbl1vH6oKI95/9s0DP8a1poExoWe','caretaker','Active','2026-03-08 08:22:42','2026-03-08 08:22:42'),(75,'venuogban nadarajah','venu@gmail.com','$2y$10$G2lA8w7WmjMQPVrjvUbdkOe4AIuDSzXy2tu/Qj5s.ioFIsx4RgzTK','client','Active','2026-03-08 09:04:08','2026-03-08 09:04:08');
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
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `target_role` enum('users','caregiver','client','All') DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'system maintenance','there will be a system maintenance from 12pm to 1pm','All',1,'2025-12-23 11:18:59'),(2,'caregiver job hunting','dgstrhdtyh','users',1,'2026-01-08 05:34:31'),(3,'bdnhgn','fbdnh','users',1,'2026-01-08 07:05:41'),(4,'gdgtrhd','vdstdrh','users',1,'2026-01-20 11:26:57'),(5,'htjyj','hthty','caregiver',1,'2026-02-02 08:50:50'),(6,'fbth','gsrhtrh','All',1,'2026-02-04 04:38:04'),(7,'fbth','gsrhtrh','All',1,'2026-02-04 04:38:26'),(8,'system maintenance','there will be a system maintenance from today 12pm to evening 5pm.Sorry for the inconvinience we have caused','caregiver',1,'2026-02-04 04:55:41'),(9,'system down','rgtrhthjyhreg','users',1,'2026-03-08 08:25:20');
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
  CONSTRAINT `fk_bookings_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (9,8,18,'Elder Care','Monthly',11,'Full Time (8am - 5pm)','2026-03-12','2026-03-12',NULL,'Kandy','93','peterson lane wellawatte','','00600','',550000.00,'Payment_Requested','2026-03-08 09:37:35',NULL,3,11,150000.00,NULL,NULL,0,0.00,0,'none',0.00,0),(10,8,14,'Elder Care','Yearly',1,'Full Time (8am - 5pm)','2026-03-12','2026-03-12',NULL,'Kandy','93','peterson lane wellawatte','','00600','',550000.00,'Requested','2026-03-08 09:38:15',NULL,4,12,183333.33,NULL,NULL,0,0.00,0,'none',0.00,0),(11,8,10,'Elder Care','Yearly',2,'Full Time (8am - 5pm)','2026-03-12','2026-03-12',NULL,'Kandy','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',1100000.00,'Payment_Requested','2026-03-08 09:38:57',NULL,6,24,275000.00,NULL,NULL,0,0.00,0,'none',0.00,0),(12,8,45,'Babysitter','Daily',25,'Full Time (8am - 5pm)','2026-03-12','2026-03-12',NULL,'colombo','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',55000.00,'Requested','2026-03-08 09:40:11',NULL,0,0,33000.00,NULL,NULL,0,0.00,0,'none',0.00,0),(13,8,6,'Babysitter','Monthly',1,'Full Time (8am - 5pm)','2026-03-12','2026-03-12',NULL,'colombo','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',45000.00,'Requested','2026-03-08 09:42:16',NULL,1,1,45000.00,NULL,NULL,0,0.00,0,'none',0.00,0),(14,8,34,'Maid','Hourly',5,'16:13','2026-03-12','2026-03-12',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',2500.00,'Payment_Requested','2026-03-08 09:43:51',NULL,0,0,0.00,NULL,NULL,0,0.00,0,'none',0.00,0),(15,7,30,'Maid','Monthly',5,'Full Time (8am - 5pm)','2026-03-12','2026-03-12',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',190000.00,'Accepted','2026-03-08 09:56:26','2026-03-08 15:44:25',1,5,38000.00,NULL,NULL,0,0.00,0,'none',0.00,0);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings_bak`
--

DROP TABLE IF EXISTS `bookings_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings_bak` (
  `id` int NOT NULL DEFAULT '0',
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `service_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `basis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `duration` int NOT NULL,
  `preferred_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `booking_date` date NOT NULL,
  `service_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `district` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `street` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address_line1` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address_line2` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `customization` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_payment` decimal(10,2) NOT NULL,
  `status` enum('Requested','Payment_Requested','Advance_Paid','Accepted','Change_Requested','Rejected','Cancelled','Completed','Reschedule_Requested') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cancelled_at` datetime DEFAULT NULL,
  `customization_hours` int NOT NULL DEFAULT '0',
  `customization_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `caretaker_changed_once` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings_bak`
--

LOCK TABLES `bookings_bak` WRITE;
/*!40000 ALTER TABLE `bookings_bak` DISABLE KEYS */;
INSERT INTO `bookings_bak` VALUES (1,2,9,'Elder Care','Monthly',5,'Full Time (8am - 5pm)','2026-02-13',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','wellawatte','peterson lane','43000','',200000.00,'Cancelled','2026-02-09 04:56:30','grth','2026-02-10 02:55:11',0,0.00,0),(2,2,31,'Maid','Hourly',4,'Full Time (8am - 5pm)','2026-02-14',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','B10','','43000','',2900.00,'Completed','2026-02-09 06:28:24',NULL,NULL,3,900.00,0),(3,2,39,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-02-14',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','B10','','43000','',120600.00,'Cancelled','2026-02-09 06:44:58','uhoij','2026-02-10 02:57:50',2,600.00,0),(4,2,17,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-02-14',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','B10','','43000','',120000.00,'Completed','2026-02-10 02:41:15',NULL,NULL,0,0.00,0),(5,7,17,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-03-04',NULL,'Colombo','93','peterson lane wellawatte','','00600','',121500.00,'Cancelled','2026-02-28 03:19:32','gdhytjyj','2026-02-28 22:49:16',5,1500.00,0),(6,7,17,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-03-06',NULL,'Colombo','93','peterson lane wellawatte','','00600','',121200.00,'Cancelled','2026-02-28 03:38:30','fhjyj','2026-02-28 22:48:52',4,1200.00,0),(7,7,11,'Elder Care','Yearly',3,'Full Time (8am - 5pm)','2026-03-05',NULL,'Galle','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',1351500.00,'Reschedule_Requested','2026-02-28 04:04:48',NULL,NULL,5,1500.00,0),(8,7,40,'Maid','Hourly',5,'10:00','2026-03-05',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',2800.00,'Payment_Requested','2026-02-28 04:10:25',NULL,NULL,1,300.00,0),(9,7,25,'Babysitter','Monthly',4,'Morning (8am - 12pm)','2026-03-13',NULL,'Kurunegala','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',96000.00,'Payment_Requested','2026-02-28 04:12:39',NULL,NULL,0,0.00,0),(10,7,2,'Maid','Daily',5,'Full Time (8am - 5pm)','2026-03-04',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',15000.00,'Accepted','2026-02-28 05:28:09',NULL,NULL,0,0.00,0),(11,7,20,'Babysitter','Daily',3,'Full Time (8am - 5pm)','2026-03-04',NULL,'Gampaha','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',9000.00,'Advance_Paid','2026-02-28 14:04:25',NULL,NULL,0,0.00,0),(12,7,5,'Babysitter','Monthly',2,'Morning (8am - 12pm)','2026-03-12',NULL,'kilinochchi','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',48000.00,'Payment_Requested','2026-03-01 10:30:43',NULL,NULL,0,0.00,0),(13,7,27,'Babysitter','Daily',6,'Morning (8am - 12pm)','2026-05-06',NULL,'Colombo','93','peterson lane wellawatte','','00600','',10800.00,'Requested','2026-03-02 01:19:22',NULL,NULL,0,0.00,0),(14,7,4,'Elder Care','Monthly',2,'Morning (8am - 12pm)','2026-03-07',NULL,'matara','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',72000.00,'Requested','2026-03-03 01:18:46',NULL,NULL,1,18000.00,0),(15,7,30,'Maid','Daily',10,'Full Time (8am - 5pm)','2026-03-07',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',33000.00,'Cancelled','2026-03-03 02:32:49','bghj','2026-03-03 08:10:45',1,3000.00,0);
/*!40000 ALTER TABLE `bookings_bak` ENABLE KEYS */;
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
  `requested_name` varchar(120) NOT NULL,
  `requested_email` varchar(150) NOT NULL,
  `requested_phone` varchar(30) NOT NULL,
  `requested_experience` varchar(120) DEFAULT '',
  `requested_location` varchar(120) DEFAULT '',
  `requested_qualifications` text,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `admin_note` text,
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
INSERT INTO `caretaker_profile_change_requests` VALUES (1,44,'abinaya','abi@gmail.com','0702248119','4  years','vavuniya','Basic education  Experience in household work  Knowledge of cleaning and hygiene  Ability to cook simple meals  Honest and trustworthy  Physically fit  Good time management','Pending',NULL,NULL,NULL,'2026-03-07 11:06:31');
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
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `experience` varchar(100) DEFAULT NULL,
  `qualifications` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `location` varchar(50) NOT NULL,
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
INSERT INTO `caretakers` VALUES (2,11,'sujany','suja@gmail.com','$2y$10$6JE4dWOzRIB3Ell3gMP3XOFrC302mN1uKaYtEwgkFfOIMIwrZMs7G','0773607650','Maid','Active','2 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','1768919419_1ffce2128e255d2acb8e6cf549f34ed9.jpg','Vavuniya',NULL,'2026-01-20 14:30:19'),(4,12,'evon','evon@gmail.com','$2y$10$77JG0VUup.YLRJoh7323SuNaG9BTjXeTJBZ87bddpYkrWuHAHMSS2','0702248119','Elder Care','Active','1 year','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','1768919555_IMG-20250419-WA0308.jpg','matara',NULL,'2026-01-20 14:32:35'),(5,13,'pugalanthi','pugal@gmail.com','$2y$10$0I45XXxkaxOBwUnJTuTKVeHUT6VQLxIc7GGHa92Y1foolmDBqEUoy','0702248119','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','1768929306_IMG-20241205-WA0061.jpg','kilinochchi',NULL,'2026-01-20 17:15:06'),(6,14,'vijay','vijay@gmail.com','$2y$10$ZSTy1Nh4OF09XrfV5.cGOesSiNiUz6J5PgGNs/IIyvaQSm52NPVWS','0702248119','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','1769134692_64-646527_sarkar-vijay-full-size.jpg','colombo',NULL,'2026-01-23 02:18:12'),(8,15,'bhavani','bhavani@gmail.com','$2y$10$deJeJG8/BLDYsrhnfa/EAeMug.DoJVdzsg6aZxTT3uXk5DHQgaz9e','0702248119','Maid','Active','2 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','1769246810_92122dcc.jpg','jaffna',NULL,'2026-01-24 09:26:50'),(9,16,'Nimal Perera','ct01@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111001','Elder Care','Active','5 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Colombo',4.5,'2026-01-25 02:04:30'),(10,17,'Shanthi Silva','ct02@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111002','Elder Care','Active','7 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kandy',4.8,'2026-01-25 02:04:30'),(11,18,'Sunil Fernando','ct03@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111003','Elder Care','Active','3 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Galle',4.1,'2026-01-25 02:04:30'),(12,19,'Kusum Jay','ct04@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111004','Elder Care','Active','6 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Matara',4.6,'2026-01-25 02:04:30'),(13,20,'Rohana Dias','ct05@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111005','Elder Care','Inactive','2 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Colombo',3.9,'2026-01-25 02:04:30'),(14,21,'Saman Perera','ct06@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111006','Elder Care','Active','8 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kandy',4.9,'2026-01-25 02:04:30'),(15,22,'Nirosha Kumari','ct07@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111007','Elder Care','Active','4 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Negombo',4.3,'2026-01-25 02:04:30'),(16,23,'Upali Silva','ct08@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111008','Elder Care','Active','5 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kurunegala',4.2,'2026-01-25 02:04:30'),(17,24,'Indika Fernando','ct09@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111009','Elder Care','Active','6 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Colombo',4.6,'2026-01-25 02:04:30'),(18,25,'Padmini Weerasinghe','ct10@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111010','Elder Care','Active','7 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kandy',4.7,'2026-01-25 02:04:30'),(19,26,'Malini Jay','ct11@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222001','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Colombo',4.4,'2026-01-25 02:04:30'),(20,27,'Hasini Perera','ct12@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222002','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Gampaha',4.6,'2026-01-25 02:04:30'),(21,28,'Roshani Silva','ct13@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222003','Babysitter','Active','2 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Kandy',4.1,'2026-01-25 02:04:30'),(22,29,'Thilini Jay','ct14@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222004','Babysitter','Inactive','1 year','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Matara',3.8,'2026-01-25 02:04:30'),(23,30,'Sanduni Fernando','ct15@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222005','Babysitter','Active','5 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Colombo',4.9,'2026-01-25 02:04:30'),(24,31,'Ishara Perera','ct16@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222006','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Negombo',4.3,'2026-01-25 02:04:30'),(25,32,'Nadeesha Kumari','ct17@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222007','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Kurunegala',4.5,'2026-01-25 02:04:30'),(26,33,'Dilani Silva','ct18@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222008','Babysitter','Active','2 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Galle',4.0,'2026-01-25 02:04:30'),(27,34,'Sachini Jay','ct19@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222009','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Colombo',4.2,'2026-01-25 02:04:30'),(28,35,'Dinithi Perera','ct20@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222010','Babysitter','Active','6 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Kandy',4.8,'2026-01-25 02:04:30'),(29,36,'Suresh Fernando','ct21@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333001','Maid','Active','8 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Jaffna',4.2,'2026-01-25 02:04:30'),(30,37,'Kavitha Raj','ct22@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333002','Maid','Active','5 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Vavuniya',4.4,'2026-01-25 02:04:30'),(31,38,'Ramesh Kumar','ct23@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333003','Maid','Active','6 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Colombo',4.3,'2026-01-25 02:04:30'),(32,39,'Anusha Silva','ct24@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333004','Maid','Inactive','2 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Kandy',3.7,'2026-01-25 02:04:30'),(33,40,'Janaki Perera','ct25@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333005','Maid','Active','4 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Matara',4.1,'2026-01-25 02:04:30'),(34,41,'Priyanka Fernando','ct26@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333006','Maid','Active','7 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Colombo',4.6,'2026-01-25 02:04:30'),(35,42,'Lakshmi Devi','ct27@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333007','Maid','Active','5 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Jaffna',4.5,'2026-01-25 02:04:30'),(36,43,'Saroja Kumari','ct28@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333008','Maid','Active','3 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Vavuniya',4.0,'2026-01-25 02:04:30'),(37,44,'Kumara Silva','ct29@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333009','Maid','Active','6 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Galle',4.4,'2026-01-25 02:04:30'),(38,45,'Samanthi Jay','ct30@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333010','Maid','Active','4 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Negombo',4.2,'2026-01-25 02:04:30'),(39,46,'shanuja venugoban','shanu6@gmail.com','$2y$10$0fwfUJxrHd6coCsaBJOToOs6etPpy1TtFQFHHQNuJ8zF2RXvx1kI2','0702248119','Elder Care','Active','3 years','Certified in Elder Care and First Aid  Trained in Geriatric Health and Dementia Care  Knowledge of Patient Safety and Medication Management  Skilled in Daily Living Assistance (feeding, bathing, mobility)','1769566155_3edff30f.jpg','Colombo',NULL,'2026-01-28 02:09:15'),(40,47,'amala','amala@gmail.com','$2y$10$8wN1haWE42/zHGr3XolPD.oM5L1x3kns/qJPRR22wkxUIrT.kb4ES','8989900','Maid','Active','1 year','Basic education  Experience in household work  Knowledge of cleaning and hygiene  Ability to cook simple meals  Honest and trustworthy  Physically fit  Good time management','1769734673_9b1aa7e0c83eaa15657cb00bef9bdba2.jpg','Vavuniya',NULL,'2026-01-30 00:57:53'),(41,48,'akshara','aksha@gmail.com','$2y$10$pzDQnKAWYiBaKisHJ/oVB.rZtpxbU/PUI/QAGmGFm.TLaUP7zb61e','0704356787','Babysitter','Active','5 years','“Certificate in Child Care / Early Childhood Care.”  “First Aid and Basic Life Support (BLS) certified.”  “Knowledge of child nutrition, hygiene, and safety.”','1769854781_263154e6c7babfd94bdbf7e37ac83f27.jpg','Negambo',NULL,'2026-01-31 10:19:41'),(42,49,'banumathi','banu@gmail.com','$2y$10$.IraKdV14Q6XEy87eoOV.e6UDlx1GG9hDpltYun3whJ0hl5wFZTQC','07634455767','Babysitter','Active','5 Years','Certified in Elder Care and First Aid  Trained in Geriatric Health and Dementia Care  Knowledge of Patient Safety and Medication Management  Skilled in Daily Living Assistance (feeding, bathing, mobility)','1769855107_23.webp','Negambo',NULL,'2026-01-31 10:25:07'),(44,50,'abinaya','abi@gmail.com','$2y$10$wfj1cKw9ZMpigLwYpWWb9u4v0KWn.Kds2IVElIyuc/KNS7JcqBTwK','0702248119','Elder Care','Active','3 years','Basic education  Experience in household work  Knowledge of cleaning and hygiene  Ability to cook simple meals  Honest and trustworthy  Physically fit  Good time management','1772877189_00613d0a.jpg','vavuniya',NULL,'2026-03-07 09:53:09'),(45,51,'shaithu','shaithra@gmail.com','$2y$10$dXCfDx/PFzwMX1/9pIfQUeTwDwTKhBpjo3TcUMUx2M65JG3rZJlIa','0702248119','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','1772877245_7616d621.jpg','colombo',NULL,'2026-03-07 09:54:05'),(46,74,'bhavani','bhavani1@gmail.com','$2y$10$Td16ucIy6c9GqIbCC5gd7Og3WIbl1vH6oKI95/9s0DP8a1poExoWe','0702248119','Babysitter','Active','1 year','“Certificate in Child Care / Early Childhood Care.”  “First Aid and Basic Life Support (BLS) certified.”  “Knowledge of child nutrition, hygiene, and safety.”','1772958162_9982ea80.jpg','colombo',NULL,'2026-03-08 08:22:42');
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
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  `hr_note` varchar(255) DEFAULT NULL,
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
-- Table structure for table `change_requests_bak`
--

DROP TABLE IF EXISTS `change_requests_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `change_requests_bak` (
  `id` int NOT NULL DEFAULT '0',
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `old_caretaker_id` int NOT NULL,
  `new_caretaker_id` int NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  `hr_note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_requests_bak`
--

LOCK TABLES `change_requests_bak` WRITE;
/*!40000 ALTER TABLE `change_requests_bak` DISABLE KEYS */;
INSERT INTO `change_requests_bak` VALUES (1,10,7,30,2,'the caregiver is not supportive','rejected','2026-02-28 05:56:50',NULL,NULL),(2,10,7,30,2,'the caregiver is not supportive','rejected','2026-02-28 05:59:52',NULL,NULL),(3,5,7,9,17,'grht','approved','2026-02-28 16:55:36',NULL,NULL),(4,10,7,2,30,'gfhtrh','rejected','2026-02-28 17:42:03',NULL,NULL),(5,10,7,30,36,'gfhtyjy','pending','2026-03-01 04:20:47',NULL,NULL);
/*!40000 ALTER TABLE `change_requests_bak` ENABLE KEYS */;
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
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client') DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_clients_account_id` (`account_id`),
  CONSTRAINT `fk_clients_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,4,'Thanushya Venugoban','thanu.venu28@gmail.com','0702248119',NULL,'$2y$10$rMfcf206RoBO1lE9K2E7MeKZqoms/aIbnZ8yFG1bXX6BAORUxwRMm','client','2025-12-31 04:06:31'),(2,5,'piyula xdfsf','piyu@gmail.com','0702248119','1768906969_ai-data-machine-learning-artificial-intelligence-icon-isolated-transparent-background_1184980-856.avif','$2y$10$RgVZsIZFH/M2LMmd688PSu0VYh7um9XKc0vr4WpeYSkjlcacAFzVa','client','2026-01-08 06:03:04'),(3,6,'shinthurie kuganathan','shinthu@gmail.com','0702248119',NULL,'$2y$10$tDmb7Z5hLXKpcB5pC63E5.O8stiB2vv/S8AOhm4PYDUTB.FaUbWv2','client','2026-01-23 02:04:42'),(5,7,'shaganjaly  sivanenthiran','shaga@gmail.com','0702248119','1769223458_20190319_173531.jpg','$2y$10$OgZdyQNQYdzzU2bkEMOVDeJOEUIXQ8BTCOi1kbBZKN9bpDg0oW3xG','client','2026-01-24 02:56:40'),(6,8,'vishnugah ramanathan','vishnu@gmail.com','07467898773',NULL,'$2y$10$xGEa7iH2n7G/kGHfb4t87OYokM1VGClzmcYwWE.973Q7xKDFXk7y2','client','2026-01-31 15:35:08'),(7,9,'sulojan rajkumar','sulojan@gmail.com','0702248119',NULL,'$2y$10$viCbnW7nkprZ/4/zZbIaOO..GgUKEAQYDPAFKYoFGbMvRtL98C/CG','client','2026-02-26 08:43:32'),(8,75,'venuogban nadarajah','venu@gmail.com','0702248119',NULL,'$2y$10$G2lA8w7WmjMQPVrjvUbdkOe4AIuDSzXy2tu/Qj5s.ioFIsx4RgzTK','client','2026-03-08 09:04:08');
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
  `client_name` varchar(200) NOT NULL COMMENT 'Name of the client filing the complaint',
  `caretaker_name` varchar(200) NOT NULL COMMENT 'Name of the caretaker involved',
  `category` enum('Caretaker Behavior','Service Quality','Late Arrival','Unprofessional','Other') NOT NULL COMMENT 'Type or category of the complaint',
  `details` text NOT NULL COMMENT 'Detailed description of the complaint',
  `status` enum('Open','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Open' COMMENT 'Current status of the complaint',
  `complaint_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date and time the complaint was submitted',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table storing client complaints about caretakers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
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
  `service_type` varchar(100) NOT NULL,
  `service_date` date NOT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','Resolved','Rejected') DEFAULT 'Pending',
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
  `feedback` text NOT NULL,
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
-- Table structure for table `feedbacks_backup`
--

DROP TABLE IF EXISTS `feedbacks_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedbacks_backup` (
  `id` int NOT NULL DEFAULT '0',
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `caretaker_id` int NOT NULL,
  `rating` int NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedbacks_backup`
--

LOCK TABLES `feedbacks_backup` WRITE;
/*!40000 ALTER TABLE `feedbacks_backup` DISABLE KEYS */;
INSERT INTO `feedbacks_backup` VALUES (1,3,2,2,5,'hhtdhtyhytj','2026-01-20 16:27:10'),(2,5,2,2,3,'bgngngn','2026-01-21 02:52:48');
/*!40000 ALTER TABLE `feedbacks_backup` ENABLE KEYS */;
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
  `username` varchar(100) NOT NULL,
  `role` varchar(30) NOT NULL,
  `action` varchar(255) NOT NULL,
  `section` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role` (`role`),
  KEY `created_at` (`created_at`),
  KEY `section` (`section`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history_logs`
--

LOCK TABLES `history_logs` WRITE;
/*!40000 ALTER TABLE `history_logs` DISABLE KEYS */;
INSERT INTO `history_logs` VALUES (1,23,'Thanushya Venugoban','admin','Updated leave status to Approved (Leave ID: 19)','Leaves','2026-01-20 22:32:29'),(2,23,'Thanushya Venugoban','admin','Updated admin profile details','Settings','2026-01-20 22:37:24'),(3,23,'Thanushya Venugoban','admin','Added caretaker: pugalanthi','Caretakers','2026-01-20 22:45:06'),(4,23,'Thanushya Venugoban','admin','Added user: vishnuga','Staffs','2026-01-20 22:48:53'),(5,23,'Thanushya Venugoban','admin','Updated user (ID: 23)','Staffs','2026-01-20 22:49:15'),(6,2,'admin','admin','Added user: Thanushya Venugoban','Staffs','2026-01-21 08:47:05'),(7,4,'Thanushya Venugoban','admin','Updated admin profile details','Settings','2026-01-21 08:47:52'),(8,4,'Thanushya Venugoban','admin','Added user: nanduni','Staffs','2026-01-21 09:02:23'),(9,4,'Thanushya Venugoban','admin','Added caretaker: vijay','Caretakers','2026-01-23 07:48:12'),(10,4,'Thanushya Venugoban','admin','Added caretaker: bhavani','Caretakers','2026-01-24 14:31:10'),(11,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-01-24 14:34:13'),(12,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-01-24 14:41:07'),(13,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-01-24 14:43:55'),(14,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 1)','Caretakers','2026-01-24 14:55:58'),(15,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 7)','Caretakers','2026-01-24 14:56:03'),(16,4,'Thanushya Venugoban','admin','Added caretaker: bhavani','Caretakers','2026-01-24 14:56:50'),(17,4,'Thanushya Venugoban','admin','Added caretaker: shanuja venugoban','Caretakers','2026-01-28 07:39:15'),(18,2,'admin','admin','Deleted user (ID: 2)','Staffs','2026-01-29 19:52:22'),(19,4,'Thanushya Venugoban','admin','Deleted user (ID: 1)','Staffs','2026-01-29 19:52:45'),(20,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 3)','Caretakers','2026-01-29 20:21:20'),(21,5,'nanduni','Manager','Updated leave status to Approved (Leave ID: 28)','Leaves','2026-01-29 21:32:53'),(22,5,'nanduni','Manager','Updated leave status to Approved (Leave ID: 6)','Leaves','2026-01-29 21:33:05'),(23,5,'nanduni','admin','Updated user (ID: 5)','Staffs','2026-01-29 22:12:08'),(24,4,'Thanushya Venugoban','admin','Added caretaker: amala','Caretakers','2026-01-30 06:27:53'),(25,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 39)','Caretakers','2026-01-31 09:22:57'),(26,4,'Thanushya Venugoban','admin','Added caretaker: akshara','Caretakers','2026-01-31 15:49:41'),(27,4,'Thanushya Venugoban','admin','Added caretaker: banumathi','Caretakers','2026-01-31 15:55:07'),(28,4,'Thanushya Venugoban','admin','Updated admin profile details','Settings','2026-02-04 11:07:21'),(29,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 8)','Caretakers','2026-02-04 19:48:46'),(30,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 6)','Caretakers','2026-02-04 19:49:00'),(31,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 5)','Caretakers','2026-02-04 19:49:41'),(32,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 4)','Caretakers','2026-02-04 19:50:09'),(33,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 2)','Caretakers','2026-02-04 19:50:24'),(34,4,'Thanushya Venugoban','admin','Added caretaker: nanduni','Caretakers','2026-02-06 07:02:30'),(35,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 43)','Caretakers','2026-02-06 07:22:10'),(36,5,'nanduni','Manager','Requested advance payment for Booking #13','Pending Requests','2026-03-05 14:02:19'),(37,5,'nanduni','Manager','Updated client complaint status to In Progress (Complaint ID: 45)','Complaints','2026-03-05 14:14:33'),(38,5,'nanduni','Manager','Rejected leave request (Leave ID: 34)','Leaves','2026-03-05 14:23:37'),(39,5,'nanduni','Manager','Requested advance payment for Booking #17','Pending Requests','2026-03-07 12:15:49'),(40,5,'nanduni','Manager','Approved payment #9 for Booking #17','Payments','2026-03-07 12:17:17'),(41,5,'nanduni','Manager','Approved payment #9 for Booking #17','Payments','2026-03-07 12:18:28'),(42,5,'nanduni','Manager','Requested advance payment for Booking #19','Pending Requests','2026-03-07 14:29:16'),(43,5,'nanduni','Manager','Approved payment #10 for Booking #19','Payments','2026-03-07 14:31:36'),(44,5,'nanduni','Manager','Requested advance payment for Booking #20','Pending Requests','2026-03-07 14:50:03'),(45,5,'nanduni','Manager','Requested advance payment for Booking #18','Pending Requests','2026-03-07 14:50:10'),(46,4,'Thanushya Venugoban','admin','Added caretaker: abinaya','Caretakers','2026-03-07 15:23:09'),(47,4,'Thanushya Venugoban','admin','Added caretaker: shaithu','Caretakers','2026-03-07 15:24:05'),(48,5,'nanduni','Manager','Requested advance payment for Booking #21','Pending Requests','2026-03-07 15:30:39'),(49,5,'nanduni','Manager','Approved payment #11 for Booking #21','Payments','2026-03-07 15:31:48'),(50,5,'nanduni','Manager','Rejected leave request (Leave ID: 37)','Leaves','2026-03-07 17:37:08'),(51,5,'nanduni','Manager','Approved Refund #3','Refunds','2026-03-07 21:43:48'),(52,5,'nanduni','Manager','Approved payment #12 for Booking #18','Payments','2026-03-07 21:53:09'),(53,5,'nanduni','Manager','Declined Refund #4','Refunds','2026-03-07 22:03:45'),(54,5,'nanduni','Manager','Requested advance payment for Booking #1','Pending Requests','2026-03-07 22:25:03'),(55,5,'nanduni','Manager','Approved Refund #5','Refunds','2026-03-07 22:27:46'),(56,5,'nanduni','Manager','Requested advance payment for Booking #2','Pending Requests','2026-03-07 22:32:20'),(57,5,'nanduni','Manager','Approved payment #15 for Booking #2','Payments','2026-03-07 22:36:28'),(58,5,'nanduni','Manager','Rejected payment #14 for Booking #1','Payments','2026-03-07 22:42:07'),(59,5,'nanduni','Manager','Requested advance payment for Booking #3','Pending Requests','2026-03-07 23:06:11'),(60,4,'Thanushya Venugoban','admin','Added caretaker: bhavani','Caretakers','2026-03-08 13:52:42'),(61,5,'nanduni','Manager','Requested advance payment for Booking #14','Pending Requests','2026-03-08 15:20:34'),(62,5,'nanduni','Manager','Requested advance payment for Booking #11','Pending Requests','2026-03-08 15:20:39'),(63,5,'nanduni','Manager','Requested advance payment for Booking #9','Pending Requests','2026-03-08 15:20:44'),(64,5,'nanduni','Manager','Requested advance payment for Booking #15','Pending Requests','2026-03-08 15:27:14'),(65,5,'nanduni','Manager','Approved payment #16 for Booking #15','Payments','2026-03-08 15:44:25');
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
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reason` text,
  `can_edit_until` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `user_id` int NOT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `replacement_caretaker_id` int DEFAULT NULL,
  `hr_note` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
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
  `user_role` enum('admin','Manager','client','caretaker') NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=267 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (235,5,'Manager','New Booking Request','New booking placed.\nBooking #1 | Elder Care| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-11 | Time: Full Time (8am - 5pm) | \nDuration: 8 Monthly | \nLocation: Colombo, 93 | \nTotal: LKR 400,000\nCaretaker: Indika Fernando\n','http://localhost/CMA/hr/hr_pending_request?booking_id=1',0,'2026-03-07 16:54:17'),(236,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #1 | Service: Elder Care | Date: 2026-03-11 | Time: Full Time (8am - 5pm) | Duration: 8 Monthly | Caregiver: Indika Fernando\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=1',0,'2026-03-07 16:55:03'),(237,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 150,000.00 for booking #1.','http://localhost/CMA/hr/pendingPayments',0,'2026-03-07 16:55:43'),(238,7,'client','Booking Cancelled','Your booking #1 has been cancelled.\n\nService: Elder Care\nBooking Date: 2026-03-11\n\nRefund Amount: LKR 142,500.00\nYour refund is pending HR approval and will be processed shortly.','http://localhost/CMA/client/c_cancelledBookings',0,'2026-03-07 16:56:50'),(239,17,'caretaker','Booking Cancelled','Booking #1 has been cancelled by the client.\n\nService: Elder Care\nBooking Date: 2026-03-11\n\nThis booking is no longer active.','http://localhost/CMA/caretaker/ct_bookings',0,'2026-03-07 16:56:50'),(240,5,'Manager','Booking Cancellation - Action Required','Booking #1 has been cancelled.\n\nClient ID: 7\nService: Elder Care\nCaretaker ID: 17\n\nRefund Amount: LKR 142,500.00\nAction Required: Approve and process refund.','http://localhost/CMA/hr/refunds',0,'2026-03-07 16:56:50'),(241,7,'client','Refund Approved','Your refund request for Booking #1 has been approved.\n\nRefund Amount: LKR 142,500.00\nThe refund will be processed and transferred to your account shortly.\n','http://localhost/CMA/client/c_cancelledBookings',0,'2026-03-07 16:57:46'),(242,5,'Manager','New Booking Request','New booking placed.\nBooking #2 | Elder Care| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-11 | Time: Full Time (8am - 5pm) | \nDuration: 8 Monthly | \nLocation: Colombo, 93 | \nTotal: LKR 472,000\nCaretaker: shanuja venugoban\n','http://localhost/CMA/hr/hr_pending_request?booking_id=2',0,'2026-03-07 17:00:53'),(243,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #2 | Service: Elder Care | Date: 2026-03-11 | Time: Full Time (8am - 5pm) | Duration: 8 Monthly | Caregiver: shanuja venugoban\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=2',0,'2026-03-07 17:02:20'),(244,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 177,000.00 for booking #2.','http://localhost/CMA/hr/pendingPayments',0,'2026-03-07 17:03:13'),(245,39,'caretaker','Booking Accepted','Booking #2 has been accepted after payment approval. Client: sulojan rajkumar. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=2&tab=upcoming',0,'2026-03-07 17:06:28'),(246,7,'client','Payment Rejected','Payment for booking #1 has been rejected. Reason: . Please contact HR for details.','http://localhost/CMA/client/c_paymentHistory',0,'2026-03-07 17:12:07'),(247,5,'Manager','New Booking Request','New booking placed.\nBooking #3 | Babysitter| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-11 | Time: Full Time (8am - 5pm) | \nDuration: 25 Daily | \nLocation: Gampaha, 93 | \nTotal: LKR 55,000\nCaretaker: Hasini Perera\n','http://localhost/CMA/hr/hr_pending_request?booking_id=3',0,'2026-03-07 17:34:07'),(248,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #3 | Service: Babysitter | Date: 2026-03-11 | Time: Full Time (8am - 5pm) | Duration: 25 Daily | Caregiver: Hasini Perera\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=3',0,'2026-03-07 17:36:11'),(249,5,'Manager','New Booking Request','New booking placed.\nBooking #4 | Elder Care| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 1 Yearly | \nLocation: Kandy, 93 | \nTotal: LKR 550,000\nCaretaker: Padmini Weerasinghe\n','http://localhost/CMA/hr/hr_pending_request?booking_id=4',0,'2026-03-08 09:02:48'),(250,5,'Manager','New Booking Request','New booking placed.\nBooking #5 | Elder Care| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 1 Yearly | \nLocation: Kandy, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 550,000\nCaretaker: Saman Perera\n','http://localhost/CMA/hr/hr_pending_request?booking_id=5',0,'2026-03-08 09:06:14'),(251,5,'Manager','New Booking Request','New booking placed.\nBooking #6 | Elder Care| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 11 Monthly | \nLocation: Kandy, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 550,000\nCaretaker: Padmini Weerasinghe\n','http://localhost/CMA/hr/hr_pending_request?booking_id=6',0,'2026-03-08 09:11:10'),(252,5,'Manager','New Booking Request','New booking placed.\nBooking #7 | Elder Care| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Morning (8am - 12pm) | \nDuration: 2 Yearly | \nLocation: Kandy, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 660,000\nCaretaker: Padmini Weerasinghe\n','http://localhost/CMA/hr/hr_pending_request?booking_id=7',0,'2026-03-08 09:23:11'),(253,5,'Manager','New Booking Request','New booking placed.\nBooking #8 | Babysitter| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 23 Daily | \nLocation: colombo, 93 | \nTotal: LKR 50,600\nCaretaker: shaithu\n','http://localhost/CMA/hr/hr_pending_request?booking_id=8',0,'2026-03-08 09:30:56'),(254,5,'Manager','New Booking Request','New booking placed.\nBooking #9 | Elder Care| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 11 Monthly | \nLocation: Kandy, 93 | \nTotal: LKR 550,000\nCaretaker: Padmini Weerasinghe\n','http://localhost/CMA/hr/hr_pending_request?booking_id=9',0,'2026-03-08 09:37:35'),(255,5,'Manager','New Booking Request','New booking placed.\nBooking #10 | Elder Care| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 1 Yearly | \nLocation: Kandy, 93 | \nTotal: LKR 550,000\nCaretaker: Saman Perera\n','http://localhost/CMA/hr/hr_pending_request?booking_id=10',0,'2026-03-08 09:38:16'),(256,5,'Manager','New Booking Request','New booking placed.\nBooking #11 | Elder Care| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 2 Yearly | \nLocation: Kandy, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 1,100,000\nCaretaker: Shanthi Silva\n','http://localhost/CMA/hr/hr_pending_request?booking_id=11',0,'2026-03-08 09:38:57'),(257,5,'Manager','New Booking Request','New booking placed.\nBooking #12 | Babysitter| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 25 Daily | \nLocation: colombo, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 55,000\nCaretaker: shaithu\n','http://localhost/CMA/hr/hr_pending_request?booking_id=12',0,'2026-03-08 09:40:11'),(258,5,'Manager','New Booking Request','New booking placed.\nBooking #13 | Babysitter| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 1 Monthly | \nLocation: colombo, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 45,000\nCaretaker: vijay\n','http://localhost/CMA/hr/hr_pending_request?booking_id=13',0,'2026-03-08 09:42:16'),(259,5,'Manager','New Booking Request','New booking placed.\nBooking #14 | Maid| \nClient: venuogban nadarajah (venu@gmail.com) |\nDate: 2026-03-12 | Time: 16:13 | \nDuration: 5 Hourly | \nLocation: Colombo, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 2,500\nCaretaker: Priyanka Fernando\n','http://localhost/CMA/hr/hr_pending_request?booking_id=14',0,'2026-03-08 09:43:51'),(260,8,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #14 | Service: Maid | Date: 2026-03-12 | Time: 16:13 | Duration: 5 Hourly | Caregiver: Priyanka Fernando\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=14',0,'2026-03-08 09:50:34'),(261,8,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #11 | Service: Elder Care | Date: 2026-03-12 | Time: Full Time (8am - 5pm) | Duration: 2 Yearly | Caregiver: Shanthi Silva\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=11',0,'2026-03-08 09:50:39'),(262,8,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #9 | Service: Elder Care | Date: 2026-03-12 | Time: Full Time (8am - 5pm) | Duration: 11 Monthly | Caregiver: Padmini Weerasinghe\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=9',0,'2026-03-08 09:50:44'),(263,5,'Manager','New Booking Request','New booking placed.\nBooking #15 | Maid| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-12 | Time: Full Time (8am - 5pm) | \nDuration: 5 Monthly | \nLocation: Vavuniya, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 190,000\nCaretaker: Kavitha Raj\n','http://localhost/CMA/hr/hr_pending_request?booking_id=15',0,'2026-03-08 09:56:26'),(264,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #15 | Service: Maid | Date: 2026-03-12 | Time: Full Time (8am - 5pm) | Duration: 5 Monthly | Caregiver: Kavitha Raj\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=15',0,'2026-03-08 09:57:14'),(265,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 38,000.00 for booking #15.','http://localhost/CMA/hr/pendingPayments',0,'2026-03-08 10:02:19'),(266,30,'caretaker','Booking Accepted','Booking #15 has been accepted after payment approval. Client: sulojan rajkumar. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=15&tab=upcoming',0,'2026-03-08 10:14:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (14,1,7,17,400000.00,0.00,150000.00,250000.00,'debit_card','advance','rejected',NULL,NULL,0,'2026-03-07 16:55:43','2026-03-07 22:42:07'),(15,2,7,39,472000.00,72000.00,177000.00,295000.00,'debit_card','advance','approved',NULL,NULL,0,'2026-03-07 17:03:13','2026-03-07 22:36:28'),(16,15,7,30,190000.00,0.00,38000.00,152000.00,'debit_card','advance','approved',NULL,NULL,0,'2026-03-08 10:02:19','2026-03-08 15:44:25');
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
  CONSTRAINT `fk_recurring_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_recurring_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks recurring payment schedules and reminders for bookings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_payments`
--

LOCK TABLES `recurring_payments` WRITE;
/*!40000 ALTER TABLE `recurring_payments` DISABLE KEYS */;
INSERT INTO `recurring_payments` VALUES (21,15,7,30,1,'monthly','2026-04-12',38000.00,'pending',NULL,NULL,0,0,0,'2026-04-15','2026-03-08 10:14:25','2026-03-08 10:14:25'),(22,15,7,30,2,'monthly','2026-05-12',38000.00,'pending',NULL,NULL,0,0,0,'2026-05-15','2026-03-08 10:14:25','2026-03-08 10:14:25'),(23,15,7,30,3,'monthly','2026-06-12',38000.00,'pending',NULL,NULL,0,0,0,'2026-06-15','2026-03-08 10:14:25','2026-03-08 10:14:25'),(24,15,7,30,4,'monthly','2026-07-12',38000.00,'pending',NULL,NULL,0,0,0,'2026-07-15','2026-03-08 10:14:25','2026-03-08 10:14:25');
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
  CONSTRAINT `fk_refund_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks refund calculations and processing for cancelled bookings';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refunds`
--

LOCK TABLES `refunds` WRITE;
/*!40000 ALTER TABLE `refunds` DISABLE KEYS */;
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
  `reason` text,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL,
  `hr_note` text,
  PRIMARY KEY (`id`),
  KEY `fk_rr_booking` (`booking_id`),
  KEY `fk_rr_client` (`client_id`),
  CONSTRAINT `fk_rr_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rr_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reschedule_requests`
--

LOCK TABLES `reschedule_requests` WRITE;
/*!40000 ALTER TABLE `reschedule_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `reschedule_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reschedule_requests_bak`
--

DROP TABLE IF EXISTS `reschedule_requests_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reschedule_requests_bak` (
  `id` int NOT NULL DEFAULT '0',
  `booking_id` int NOT NULL,
  `client_id` int NOT NULL,
  `old_date` date NOT NULL,
  `new_date` date NOT NULL,
  `reason` text,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reschedule_requests_bak`
--

LOCK TABLES `reschedule_requests_bak` WRITE;
/*!40000 ALTER TABLE `reschedule_requests_bak` DISABLE KEYS */;
INSERT INTO `reschedule_requests_bak` VALUES (1,5,7,'2026-03-04','2026-03-05','','pending','2026-02-28 14:22:12',NULL),(2,6,7,'2026-03-04','2026-03-06','gukg','approved','2026-02-28 16:18:47',NULL),(3,7,7,'2026-03-05','2026-03-11','hjukuk','pending','2026-03-01 11:12:47',NULL);
/*!40000 ALTER TABLE `reschedule_requests_bak` ENABLE KEYS */;
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
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','Manager') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `phone` varchar(20) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_account_id` (`account_id`),
  CONSTRAINT `fk_users_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,1,'Thanushya Venugoban','thanu28@gmail.com','$2y$10$M85ZBJZ91pFPi3sWwEgLiudhJxFdC.2sGhJwPrMcW0EUVHQKkzOhO','admin','Active','','697045605c41b.jpg','2026-01-21 03:17:05'),(5,2,'Nanduni Amasha','nanduni@gmail.com','$2y$10$yTeb45tZN4DneyGv7KeciujsHSsJrR2ZgiGOUpwubZVMF3ni5iCPi','Manager','Active','0773607650','697048f0bb99b.jpg','2026-01-21 03:32:23');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_backup`
--

DROP TABLE IF EXISTS `users_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_backup` (
  `id` int NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client') DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_backup`
--

LOCK TABLES `users_backup` WRITE;
/*!40000 ALTER TABLE `users_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_backup` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-08 15:51:06

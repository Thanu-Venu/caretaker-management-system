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
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `message` text,
  `target_role` enum('users','caretaker','client','All') DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'system maintenance','there will be a system maintenance from 12pm to 1pm','All',1,'2025-12-23 11:18:59'),(2,'caretaker job hunting','dgstrhdtyh','users',1,'2026-01-08 05:34:31'),(3,'bdnhgn','fbdnh','users',1,'2026-01-08 07:05:41'),(4,'gdgtrhd','vdstdrh','users',1,'2026-01-20 11:26:57'),(5,'htjyj','hthty','caretaker',1,'2026-02-02 08:50:50'),(6,'fbth','gsrhtrh','All',1,'2026-02-04 04:38:04'),(7,'fbth','gsrhtrh','All',1,'2026-02-04 04:38:26'),(8,'system maintenance','there will be a system maintenance from today 12pm to evening 5pm.Sorry for the inconvinience we have caused','caretaker',1,'2026-02-04 04:55:41');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
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
  `cancellation_reason` text COLLATE utf8mb4_general_ci,
  `cancelled_at` datetime DEFAULT NULL,
  `customization_hours` int NOT NULL DEFAULT '0',
  `customization_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `caretaker_changed_once` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_bookings_client` (`client_id`),
  KEY `idx_bookings_caretaker` (`caretaker_id`),
  KEY `idx_bookings_status` (`status`),
  KEY `idx_bookings_date` (`booking_date`),
  CONSTRAINT `fk_bookings_caretaker` FOREIGN KEY (`caretaker_id`) REFERENCES `caretakers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,2,9,'Elder Care','Monthly',5,'Full Time (8am - 5pm)','2026-02-13',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','wellawatte','peterson lane','43000','',200000.00,'Cancelled','2026-02-09 04:56:30','grth','2026-02-10 02:55:11',0,0.00,0),(2,2,31,'Maid','Hourly',4,'Full Time (8am - 5pm)','2026-02-14',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','B10','','43000','',2900.00,'Completed','2026-02-09 06:28:24',NULL,NULL,3,900.00,0),(3,2,39,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-02-14',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','B10','','43000','',120600.00,'Completed','2026-02-09 06:44:58','uhoij','2026-02-10 02:57:50',2,600.00,0),(4,2,17,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-02-14',NULL,'Colombo','303/10,mannar road paddanichoor vavuniya','B10','','43000','',120000.00,'Completed','2026-02-10 02:41:15',NULL,NULL,0,0.00,0),(5,7,17,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-03-04',NULL,'Colombo','93','peterson lane wellawatte','','00600','',121500.00,'Cancelled','2026-02-28 03:19:32','gdhytjyj','2026-02-28 22:49:16',5,1500.00,0),(6,7,17,'Elder Care','Monthly',3,'Full Time (8am - 5pm)','2026-03-06',NULL,'Colombo','93','peterson lane wellawatte','','00600','',121200.00,'Cancelled','2026-02-28 03:38:30','fhjyj','2026-02-28 22:48:52',4,1200.00,0),(8,7,40,'Maid','Hourly',5,'10:00','2026-03-05',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',2800.00,'Payment_Requested','2026-02-28 04:10:25',NULL,NULL,1,300.00,0),(9,7,25,'Babysitter','Monthly',4,'Morning (8am - 12pm)','2026-03-13',NULL,'Kurunegala','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',96000.00,'Payment_Requested','2026-02-28 04:12:39',NULL,NULL,0,0.00,0),(10,7,30,'Maid','Daily',5,'Full Time (8am - 5pm)','2026-03-04',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',15000.00,'Completed','2026-02-28 05:28:09',NULL,NULL,0,0.00,1),(11,7,20,'Babysitter','Daily',3,'Full Time (8am - 5pm)','2026-03-04',NULL,'Gampaha','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',9000.00,'Completed','2026-02-28 14:04:25',NULL,NULL,0,0.00,0),(12,7,5,'Babysitter','Monthly',2,'Morning (8am - 12pm)','2026-03-12',NULL,'kilinochchi','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',48000.00,'Accepted','2026-03-01 10:30:43',NULL,NULL,0,0.00,0),(13,7,27,'Babysitter','Daily',6,'Morning (8am - 12pm)','2026-05-06',NULL,'Colombo','93','peterson lane wellawatte','','00600','',10800.00,'Requested','2026-03-02 01:19:22',NULL,NULL,0,0.00,0),(14,7,4,'Elder Care','Monthly',2,'Morning (8am - 12pm)','2026-03-07',NULL,'matara','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',72000.00,'Payment_Requested','2026-03-03 01:18:46',NULL,NULL,1,18000.00,0),(15,7,30,'Maid','Daily',10,'Full Time (8am - 5pm)','2026-03-07',NULL,'Vavuniya','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',33000.00,'Cancelled','2026-03-03 02:32:49','bghj','2026-03-03 08:10:45',1,3000.00,0),(16,7,12,'Elder Care','Monthly',1,'Full Time (8am - 5pm)','2026-03-09',NULL,'Matara','303/10,mannar road paddanichoor vavuniya','peterson lane wellawatte','','43000','',63000.00,'Requested','2026-03-03 05:22:29',NULL,NULL,2,18000.00,0);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
-- Table structure for table `caretakers`
--

DROP TABLE IF EXISTS `caretakers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caretakers` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caretakers`
--

LOCK TABLES `caretakers` WRITE;
/*!40000 ALTER TABLE `caretakers` DISABLE KEYS */;
INSERT INTO `caretakers` VALUES (2,'sujany','suja@gmail.com','$2y$10$6JE4dWOzRIB3Ell3gMP3XOFrC302mN1uKaYtEwgkFfOIMIwrZMs7G','0773607650','Maid','Active','2 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','1768919419_1ffce2128e255d2acb8e6cf549f34ed9.jpg','Vavuniya',NULL,'2026-01-20 14:30:19'),(4,'evon','evon@gmail.com','$2y$10$77JG0VUup.YLRJoh7323SuNaG9BTjXeTJBZ87bddpYkrWuHAHMSS2','0702248119','Elder Care','Active','1 year','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','1768919555_IMG-20250419-WA0308.jpg','matara',NULL,'2026-01-20 14:32:35'),(5,'pugalanthi','pugal@gmail.com','$2y$10$0I45XXxkaxOBwUnJTuTKVeHUT6VQLxIc7GGHa92Y1foolmDBqEUoy','0702248119','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','1768929306_IMG-20241205-WA0061.jpg','kilinochchi',NULL,'2026-01-20 17:15:06'),(6,'vijay','vijay@gmail.com','$2y$10$ZSTy1Nh4OF09XrfV5.cGOesSiNiUz6J5PgGNs/IIyvaQSm52NPVWS','0702248119','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','1769134692_64-646527_sarkar-vijay-full-size.jpg','colombo',NULL,'2026-01-23 02:18:12'),(8,'bhavani','bhavani@gmail.com','$2y$10$deJeJG8/BLDYsrhnfa/EAeMug.DoJVdzsg6aZxTT3uXk5DHQgaz9e','0702248119','Maid','Active','2 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','1769246810_92122dcc.jpg','jaffna',NULL,'2026-01-24 09:26:50'),(9,'Nimal Perera','ct01@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111001','Elder Care','Active','5 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Colombo',4.5,'2026-01-25 02:04:30'),(10,'Shanthi Silva','ct02@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111002','Elder Care','Active','7 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kandy',4.8,'2026-01-25 02:04:30'),(11,'Sunil Fernando','ct03@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111003','Elder Care','Active','3 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Galle',4.1,'2026-01-25 02:04:30'),(12,'Kusum Jay','ct04@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111004','Elder Care','Active','6 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Matara',4.6,'2026-01-25 02:04:30'),(13,'Rohana Dias','ct05@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111005','Elder Care','Inactive','2 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Colombo',3.9,'2026-01-25 02:04:30'),(14,'Saman Perera','ct06@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111006','Elder Care','Active','8 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kandy',4.9,'2026-01-25 02:04:30'),(15,'Nirosha Kumari','ct07@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111007','Elder Care','Active','4 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Negombo',4.3,'2026-01-25 02:04:30'),(16,'Upali Silva','ct08@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111008','Elder Care','Active','5 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kurunegala',4.2,'2026-01-25 02:04:30'),(17,'Indika Fernando','ct09@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111009','Elder Care','Active','6 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Colombo',4.6,'2026-01-25 02:04:30'),(18,'Padmini Weerasinghe','ct10@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0711111010','Elder Care','Active','7 years','NVQ Level 3 Caregiving | First Aid Certified | Elder Mobility Support | Medication Reminders','default.png','Kandy',4.7,'2026-01-25 02:04:30'),(19,'Malini Jay','ct11@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222001','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Colombo',4.4,'2026-01-25 02:04:30'),(20,'Hasini Perera','ct12@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222002','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Gampaha',4.6,'2026-01-25 02:04:30'),(21,'Roshani Silva','ct13@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222003','Babysitter','Active','2 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Kandy',4.1,'2026-01-25 02:04:30'),(22,'Thilini Jay','ct14@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222004','Babysitter','Inactive','1 year','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Matara',3.8,'2026-01-25 02:04:30'),(23,'Sanduni Fernando','ct15@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222005','Babysitter','Active','5 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Colombo',4.9,'2026-01-25 02:04:30'),(24,'Ishara Perera','ct16@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222006','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Negombo',4.3,'2026-01-25 02:04:30'),(25,'Nadeesha Kumari','ct17@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222007','Babysitter','Active','4 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Kurunegala',4.5,'2026-01-25 02:04:30'),(26,'Dilani Silva','ct18@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222008','Babysitter','Active','2 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Galle',4.0,'2026-01-25 02:04:30'),(27,'Sachini Jay','ct19@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222009','Babysitter','Active','3 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Colombo',4.2,'2026-01-25 02:04:30'),(28,'Dinithi Perera','ct20@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0722222010','Babysitter','Active','6 years','Childcare Level 2 | CPR & First Aid | Infant Care | Child Safety & Hygiene','default.png','Kandy',4.8,'2026-01-25 02:04:30'),(29,'Suresh Fernando','ct21@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333001','Maid','Active','8 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Jaffna',4.2,'2026-01-25 02:04:30'),(30,'Kavitha Raj','ct22@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333002','Maid','Active','5 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Vavuniya',4.4,'2026-01-25 02:04:30'),(31,'Ramesh Kumar','ct23@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333003','Maid','Active','6 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Colombo',4.3,'2026-01-25 02:04:30'),(32,'Anusha Silva','ct24@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333004','Maid','Inactive','2 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Kandy',3.7,'2026-01-25 02:04:30'),(33,'Janaki Perera','ct25@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333005','Maid','Active','4 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Matara',4.1,'2026-01-25 02:04:30'),(34,'Priyanka Fernando','ct26@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333006','Maid','Active','7 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Colombo',4.6,'2026-01-25 02:04:30'),(35,'Lakshmi Devi','ct27@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333007','Maid','Active','5 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Jaffna',4.5,'2026-01-25 02:04:30'),(36,'Saroja Kumari','ct28@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333008','Maid','Active','3 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Vavuniya',4.0,'2026-01-25 02:04:30'),(37,'Kumara Silva','ct29@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333009','Maid','Active','6 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Galle',4.4,'2026-01-25 02:04:30'),(38,'Samanthi Jay','ct30@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0733333010','Maid','Active','4 years','Housekeeping | Cooking (Sri Lankan Meals) | Laundry & Ironing | Deep Cleaning','default.png','Negombo',4.2,'2026-01-25 02:04:30'),(39,'shanuja venugoban','shanu6@gmail.com','$2y$10$0fwfUJxrHd6coCsaBJOToOs6etPpy1TtFQFHHQNuJ8zF2RXvx1kI2','0702248119','Elder Care','Active','3 years','Certified in Elder Care and First Aid  Trained in Geriatric Health and Dementia Care  Knowledge of Patient Safety and Medication Management  Skilled in Daily Living Assistance (feeding, bathing, mobility)','1769566155_3edff30f.jpg','Colombo',NULL,'2026-01-28 02:09:15'),(40,'amala','amala@gmail.com','$2y$10$8wN1haWE42/zHGr3XolPD.oM5L1x3kns/qJPRR22wkxUIrT.kb4ES','8989900','Maid','Active','1 year','Basic education  Experience in household work  Knowledge of cleaning and hygiene  Ability to cook simple meals  Honest and trustworthy  Physically fit  Good time management','1769734673_9b1aa7e0c83eaa15657cb00bef9bdba2.jpg','Vavuniya',NULL,'2026-01-30 00:57:53'),(41,'akshara','aksha@gmail.com','$2y$10$pzDQnKAWYiBaKisHJ/oVB.rZtpxbU/PUI/QAGmGFm.TLaUP7zb61e','0704356787','Babysitter','Active','5 years','“Certificate in Child Care / Early Childhood Care.”  “First Aid and Basic Life Support (BLS) certified.”  “Knowledge of child nutrition, hygiene, and safety.”','1769854781_263154e6c7babfd94bdbf7e37ac83f27.jpg','Negambo',NULL,'2026-01-31 10:19:41'),(42,'banumathi','banu@gmail.com','$2y$10$.IraKdV14Q6XEy87eoOV.e6UDlx1GG9hDpltYun3whJ0hl5wFZTQC','07634455767','Babysitter','Active','5 Years','Certified in Elder Care and First Aid  Trained in Geriatric Health and Dementia Care  Knowledge of Patient Safety and Medication Management  Skilled in Daily Living Assistance (feeding, bathing, mobility)','1769855107_23.webp','Negambo',NULL,'2026-01-31 10:25:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_requests`
--

LOCK TABLES `change_requests` WRITE;
/*!40000 ALTER TABLE `change_requests` DISABLE KEYS */;
INSERT INTO `change_requests` VALUES (1,10,7,2,30,'the caregiver is not punctual i need an immediate change','approved','2026-03-03 04:33:31','2026-03-03 10:04:20',NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client') DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Thanushya Venugoban','thanu.venu28@gmail.com','0702248119',NULL,'$2y$10$rMfcf206RoBO1lE9K2E7MeKZqoms/aIbnZ8yFG1bXX6BAORUxwRMm','client','2025-12-31 04:06:31'),(2,'piyula xdfsf','piyu@gmail.com','0702248119','1768906969_ai-data-machine-learning-artificial-intelligence-icon-isolated-transparent-background_1184980-856.avif','$2y$10$RgVZsIZFH/M2LMmd688PSu0VYh7um9XKc0vr4WpeYSkjlcacAFzVa','client','2026-01-08 06:03:04'),(3,'shinthurie kuganathan','shinthu@gmail.com','0702248119',NULL,'$2y$10$tDmb7Z5hLXKpcB5pC63E5.O8stiB2vv/S8AOhm4PYDUTB.FaUbWv2','client','2026-01-23 02:04:42'),(5,'shaganjaly  sivanenthiran','shaga@gmail.com','0702248119','1769223458_20190319_173531.jpg','$2y$10$OgZdyQNQYdzzU2bkEMOVDeJOEUIXQ8BTCOi1kbBZKN9bpDg0oW3xG','client','2026-01-24 02:56:40'),(6,'vishnugah ramanathan','vishnu@gmail.com','07467898773',NULL,'$2y$10$xGEa7iH2n7G/kGHfb4t87OYokM1VGClzmcYwWE.973Q7xKDFXk7y2','client','2026-01-31 15:35:08'),(7,'sulojan rajkumar','sulojan@gmail.com','0702248119',NULL,'$2y$10$viCbnW7nkprZ/4/zZbIaOO..GgUKEAQYDPAFKYoFGbMvRtL98C/CG','client','2026-02-26 08:43:32');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Table storing client complaints about caretakers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` VALUES (31,'sujany thirualan','satheeshan','Caretaker Behavior','thanuvenu','Open','2025-10-22 15:57:10'),(32,'sujany thirualan','parmi','Service Quality','ghdhdt','Open','2025-10-22 15:58:07'),(33,'Thanushya Venugoban','satheeshan','Service Quality','thanushya','Resolved','2025-10-22 16:27:03'),(35,'Thanushya Venugoban','parmi','Late Arrival','not friendly','Resolved','2025-10-23 05:53:54'),(40,'piyula xdfsf','parmi','Service Quality','fghtrhr','Resolved','2026-01-08 10:35:40'),(41,'piyula xdfsf','nanduni','Late Arrival','ggfdhythfjfj','In Progress','2026-01-09 16:19:30'),(42,'piyula xdfsf','satheeshan','Late Arrival','ffbxfghdyhytj','Open','2026-01-09 17:27:31'),(43,'piyula xdfsf','thanu','Unprofessional','fbrhtyh','In Progress','2026-01-11 02:52:31'),(44,'piyula xdfsf','satheeshan','Caretaker Behavior','jfjftyjtfyuj','Open','2026-01-20 11:01:43'),(45,'piyula xdfsf','nanduni','Caretaker Behavior','yhyujtyherfwefertjyjterfef','Open','2026-01-21 02:01:10'),(46,'sulojan rajkumar','pugalanthi','Caretaker Behavior','efthythyuju','Resolved','2026-03-02 01:22:38'),(47,'sulojan rajkumar','satheeshan','Caretaker Behavior','hduu7uu7','In Progress','2026-03-02 01:47:14');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history_logs`
--

LOCK TABLES `history_logs` WRITE;
/*!40000 ALTER TABLE `history_logs` DISABLE KEYS */;
INSERT INTO `history_logs` VALUES (1,23,'Thanushya Venugoban','admin','Updated leave status to Approved (Leave ID: 19)','Leaves','2026-01-20 22:32:29'),(2,23,'Thanushya Venugoban','admin','Updated admin profile details','Settings','2026-01-20 22:37:24'),(3,23,'Thanushya Venugoban','admin','Added caretaker: pugalanthi','Caretakers','2026-01-20 22:45:06'),(4,23,'Thanushya Venugoban','admin','Added user: vishnuga','Staffs','2026-01-20 22:48:53'),(5,23,'Thanushya Venugoban','admin','Updated user (ID: 23)','Staffs','2026-01-20 22:49:15'),(6,2,'admin','admin','Added user: Thanushya Venugoban','Staffs','2026-01-21 08:47:05'),(7,4,'Thanushya Venugoban','admin','Updated admin profile details','Settings','2026-01-21 08:47:52'),(8,4,'Thanushya Venugoban','admin','Added user: nanduni','Staffs','2026-01-21 09:02:23'),(9,4,'Thanushya Venugoban','admin','Added caretaker: vijay','Caretakers','2026-01-23 07:48:12'),(10,4,'Thanushya Venugoban','admin','Added caretaker: bhavani','Caretakers','2026-01-24 14:31:10'),(11,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-01-24 14:34:13'),(12,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-01-24 14:41:07'),(13,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 7)','Caretakers','2026-01-24 14:43:55'),(14,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 1)','Caretakers','2026-01-24 14:55:58'),(15,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 7)','Caretakers','2026-01-24 14:56:03'),(16,4,'Thanushya Venugoban','admin','Added caretaker: bhavani','Caretakers','2026-01-24 14:56:50'),(17,4,'Thanushya Venugoban','admin','Added caretaker: shanuja venugoban','Caretakers','2026-01-28 07:39:15'),(18,2,'admin','admin','Deleted user (ID: 2)','Staffs','2026-01-29 19:52:22'),(19,4,'Thanushya Venugoban','admin','Deleted user (ID: 1)','Staffs','2026-01-29 19:52:45'),(20,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 3)','Caretakers','2026-01-29 20:21:20'),(21,5,'nanduni','Manager','Updated leave status to Approved (Leave ID: 28)','Leaves','2026-01-29 21:32:53'),(22,5,'nanduni','Manager','Updated leave status to Approved (Leave ID: 6)','Leaves','2026-01-29 21:33:05'),(23,5,'nanduni','admin','Updated user (ID: 5)','Staffs','2026-01-29 22:12:08'),(24,4,'Thanushya Venugoban','admin','Added caretaker: amala','Caretakers','2026-01-30 06:27:53'),(25,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 39)','Caretakers','2026-01-31 09:22:57'),(26,4,'Thanushya Venugoban','admin','Added caretaker: akshara','Caretakers','2026-01-31 15:49:41'),(27,4,'Thanushya Venugoban','admin','Added caretaker: banumathi','Caretakers','2026-01-31 15:55:07'),(28,4,'Thanushya Venugoban','admin','Updated admin profile details','Settings','2026-02-04 11:07:21'),(29,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 8)','Caretakers','2026-02-04 19:48:46'),(30,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 6)','Caretakers','2026-02-04 19:49:00'),(31,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 5)','Caretakers','2026-02-04 19:49:41'),(32,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 4)','Caretakers','2026-02-04 19:50:09'),(33,4,'Thanushya Venugoban','admin','Updated caretaker (ID: 2)','Caretakers','2026-02-04 19:50:24'),(34,4,'Thanushya Venugoban','admin','Added caretaker: nanduni','Caretakers','2026-02-06 07:02:30'),(35,4,'Thanushya Venugoban','admin','Deleted caretaker (ID: 43)','Caretakers','2026-02-06 07:22:10');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
INSERT INTO `leaves` VALUES (2,'Personal','2025-10-18','2025-10-22','09:00:00','17:00:00','hcfjyjnyt','2025-10-22 05:54:32','Approved',4,NULL,NULL,NULL,NULL),(4,'Vacation','2025-10-23','2025-10-17','09:00:00','17:00:00','gxhsgxsj','2025-10-23 08:47:05','Rejected',13,NULL,NULL,NULL,NULL),(5,'Personal','2025-10-23','2025-10-24','09:00:00','17:00:00','headache','2025-10-23 16:55:31','Approved',17,NULL,NULL,NULL,NULL),(6,'Vacation','2025-10-23','2025-10-30','09:00:00','17:00:00','going for a trip','2025-10-23 16:55:50','Approved',17,NULL,NULL,NULL,NULL),(7,'Personal','2025-10-01','2025-10-03','09:00:00','17:00:00','personal reason wedding\r\n','2025-10-23 16:56:20','Pending',17,NULL,NULL,NULL,NULL),(8,'Vacation','2025-10-16','2025-10-23','09:00:00','17:00:00','thanu','2025-10-23 16:57:45','Pending',17,NULL,NULL,NULL,NULL),(9,'Sick Leave','2025-10-10','2025-10-08','09:00:00','17:00:00','thanu1','2025-10-23 17:04:44','Pending',17,NULL,NULL,NULL,NULL),(10,'Vacation','2025-10-03','2025-10-06','09:00:00','17:00:00','tha','2025-10-23 17:19:11','Pending',17,NULL,NULL,NULL,NULL),(13,'Sick Leave','2025-12-23','2025-12-25','09:00:00','17:00:00','xdbdxtrhdtyjr7y','2025-12-24 12:57:49','Approved',35,NULL,NULL,NULL,NULL),(14,'Vacation','2025-12-24','2025-12-26','00:00:00','17:03:00','thrgsilrgh;orthjtohij','2025-12-24 14:55:28','Rejected',35,NULL,NULL,NULL,NULL),(15,'Vacation','2025-12-31','2026-01-01','09:00:00','17:00:00','hthdytdy','2026-01-01 03:36:15','Approved',35,5,'2026-01-28 07:24:51',11,''),(16,'Vacation','2026-01-08','2026-01-14','09:00:00','17:00:00','zgsrzgtrh','2026-01-08 05:22:44','Approved',22,NULL,NULL,NULL,NULL),(17,'Sick Leave','2026-01-09','2026-01-11','09:00:00','17:00:00','hythryj','2026-01-09 09:42:21','Approved',22,NULL,NULL,NULL,NULL),(18,'Personal Leave','2026-01-16','2026-01-23','09:00:00','17:00:00','dsfes','2026-01-09 10:25:28','Approved',22,NULL,NULL,NULL,NULL),(19,'Vacation','2026-01-20','2026-01-21','09:03:00','17:00:00',' fth','2026-01-21 16:58:46','Approved',1,NULL,NULL,NULL,NULL),(20,'Personal Leave','2026-01-16','2026-01-23','09:00:00','17:00:00','efrgrthtyyjuu8otwewqs','2026-01-22 01:46:57','Pending',1,NULL,NULL,NULL,NULL),(21,'Personal Leave','2026-01-16','2026-01-23','09:00:00','17:00:00','efrgrthtyyjuu8otwewqs','2026-01-22 01:48:58','Pending',1,NULL,NULL,NULL,NULL),(22,'Vacation','2026-01-22','2026-01-29','09:00:00','17:00:00','bdhthth','2026-01-22 02:31:36','Pending',1,NULL,NULL,NULL,NULL),(23,'Sick Leave','2026-02-02','2026-02-06','09:00:00','17:00:00','im very sick i cant be able to do the work','2026-01-29 02:11:46','Approved',39,5,'2026-01-28 07:45:53',9,'ok you must attend your client by 02/06 please confirm it'),(24,'Maternity Leave','2026-01-29','2026-01-31','09:00:00','17:00:00','im pregnant and i want a leave for my checkup','2026-01-29 02:28:00','Approved',17,5,'2026-01-28 08:25:36',NULL,'ok you can take your leave'),(25,'Personal Leave','2026-02-06','2026-02-10','09:00:00','17:00:00','i have some work in that particular days consider my leave request','2026-01-29 02:57:06','Approved',39,5,'2026-01-28 08:28:31',9,'ok you can take your leave'),(27,'Vacation','2026-02-04','2026-02-18','09:00:00','17:00:00','i want a vaction leave','2026-01-29 04:46:10','Approved',39,5,'2026-01-28 10:26:58',NULL,''),(28,'Personal Leave','2026-03-03','2026-03-17','09:00:00','17:00:00','no','2026-01-29 05:00:30','Approved',39,NULL,NULL,NULL,NULL),(29,'Sick Leave','2026-04-08','2026-04-28','09:00:00','17:00:00','okokok','2026-01-31 03:05:15','Approved',39,5,'2026-01-31 11:52:47',NULL,''),(30,'Vacation','2026-02-02','2026-02-05','09:00:00','17:00:00','i need a trip related vacation','2026-02-01 06:25:19','Approved',39,5,'2026-03-02 08:29:47',NULL,'okay enjoy'),(31,'Personal Leave','2026-02-09','2026-02-12','09:00:00','17:00:00','im not well ','2026-02-01 10:21:28','Approved',41,5,'2026-01-31 15:56:11',42,'ok you have to be there at work by february 6'),(32,'Vacation','2026-03-02','2026-03-03','09:00:00','17:00:00','ffgfhh','2026-03-03 02:20:04','Approved',39,5,'2026-03-02 08:27:14',NULL,'ok you can take your leave'),(33,'Vacation','2026-03-18','2026-03-20','09:00:00','17:00:00','fdxgthyh','2026-03-03 03:24:53','Approved',39,5,'2026-03-02 09:00:07',NULL,'ghjukijtgf'),(34,'Maternity Leave','2026-03-30','2026-04-04','09:00:00','17:00:00','ryty','2026-03-03 03:28:38','Pending',39,NULL,NULL,NULL,NULL),(35,'Personal Leave','2026-03-03','2026-03-05','09:00:00','17:00:00','sdfdgehth','2026-03-03 03:57:55','Pending',39,NULL,NULL,NULL,NULL),(36,'Personal Leave','2026-03-03','2026-03-05','09:00:00','17:00:00','sdfdgehth','2026-03-03 03:57:55','Pending',39,NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,2,'client','Complaint Status Update','Your complaint #41 status changed to \'In Progress\'','http://localhost/CMA/public/index.php?url=Complaint/myComplaints',1,'2026-01-09 17:18:23'),(2,2,'client','Complaint Status Update','Your complaint #43 status changed to \'In Progress\'','http://localhost/CMA/public/index.php?url=Complaint/myComplaints',1,'2026-01-11 03:10:00'),(3,20,'admin','New Leave Request','A new leave request has been submitted by caretaker shanujah.','http://localhost/CMA/LeaveCRUD/index',0,'2026-01-21 01:48:58'),(4,23,'admin','New Leave Request','A new leave request has been submitted by caretaker shanujah.','http://localhost/CMA/LeaveCRUD/index',1,'2026-01-21 01:48:58'),(5,20,'admin','New Complaint','A new complaint was submitted by piyula xdfsf (Caregiver: nanduni).','http://localhost/CMA/admin/ad_feedback',0,'2026-01-21 02:01:10'),(6,23,'admin','New Complaint','A new complaint was submitted by piyula xdfsf (Caregiver: nanduni).','http://localhost/CMA/admin/ad_feedback',1,'2026-01-21 02:01:10'),(7,20,'admin','New Leave Request','New leave request submitted by caretaker shanujah','http://localhost/CMA/admin/ad_leave',0,'2026-01-21 02:31:36'),(8,23,'admin','New Leave Request','New leave request submitted by caretaker shanujah','http://localhost/CMA/admin/ad_leave',1,'2026-01-21 02:31:36'),(9,20,'admin','New Booking','New booking placed (Booking ID: 4) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 02:43:22'),(10,23,'admin','New Booking','New booking placed (Booking ID: 4) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 02:43:22'),(11,20,'admin','New Booking','New booking placed (Booking ID: 5) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 02:52:16'),(12,23,'admin','New Booking','New booking placed (Booking ID: 5) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 02:52:16'),(13,1,'admin','New Booking','New booking placed (Booking ID: 6) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 03:20:42'),(14,2,'admin','New Booking','New booking placed (Booking ID: 6) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 03:20:42'),(15,4,'admin','New Booking','New booking placed (Booking ID: 6) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-21 03:20:42'),(16,1,'admin','New Booking','New booking placed (Booking ID: 7) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 03:26:46'),(17,2,'admin','New Booking','New booking placed (Booking ID: 7) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-21 03:26:46'),(18,4,'admin','New Booking','New booking placed (Booking ID: 7) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-21 03:26:46'),(19,1,'admin','New Feedback','New feedback received (Booking ID: 7, Rating: 5).','http://localhost/CMA/admin/ad_feedback',0,'2026-01-21 03:26:58'),(20,2,'admin','New Feedback','New feedback received (Booking ID: 7, Rating: 5).','http://localhost/CMA/admin/ad_feedback',0,'2026-01-21 03:26:58'),(21,4,'admin','New Feedback','New feedback received (Booking ID: 7, Rating: 5).','http://localhost/CMA/admin/ad_feedback',1,'2026-01-21 03:26:58'),(22,1,'admin','New Booking','New booking placed (Booking ID: 8) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-23 02:03:54'),(23,2,'admin','New Booking','New booking placed (Booking ID: 8) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-23 02:03:54'),(24,4,'admin','New Booking','New booking placed (Booking ID: 8) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-23 02:03:54'),(25,1,'admin','New Booking','New booking placed (Booking ID: 9) by client ID 3.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-23 02:05:24'),(26,2,'admin','New Booking','New booking placed (Booking ID: 9) by client ID 3.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-23 02:05:24'),(27,4,'admin','New Booking','New booking placed (Booking ID: 9) by client ID 3.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-23 02:05:24'),(28,1,'admin','New Booking','New booking placed (Booking ID: 10) by client ID 4.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-23 02:06:59'),(29,2,'admin','New Booking','New booking placed (Booking ID: 10) by client ID 4.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-23 02:06:59'),(30,4,'admin','New Booking','New booking placed (Booking ID: 10) by client ID 4.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-23 02:06:59'),(31,1,'admin','New Booking','New booking placed (Booking ID: 11) by client ID 5.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-24 08:48:23'),(32,2,'admin','New Booking','New booking placed (Booking ID: 11) by client ID 5.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-24 08:48:23'),(33,4,'admin','New Booking','New booking placed (Booking ID: 11) by client ID 5.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-24 08:48:23'),(34,1,'admin','New Booking','New booking placed (Booking ID: 12) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 05:33:32'),(35,2,'admin','New Booking','New booking placed (Booking ID: 12) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 05:33:32'),(36,4,'admin','New Booking','New booking placed (Booking ID: 12) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 05:33:32'),(37,1,'admin','New Booking','New booking placed (Booking ID: 13) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:17:49'),(38,2,'admin','New Booking','New booking placed (Booking ID: 13) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:17:49'),(39,4,'admin','New Booking','New booking placed (Booking ID: 13) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 06:17:49'),(40,1,'admin','New Booking','New booking placed (Booking ID: 14) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:27:49'),(41,2,'admin','New Booking','New booking placed (Booking ID: 14) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:27:49'),(42,4,'admin','New Booking','New booking placed (Booking ID: 14) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 06:27:49'),(43,1,'admin','New Booking','New booking placed (Booking ID: 15) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:29:16'),(44,2,'admin','New Booking','New booking placed (Booking ID: 15) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:29:16'),(45,4,'admin','New Booking','New booking placed (Booking ID: 15) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 06:29:16'),(46,1,'admin','New Booking','New booking placed (Booking ID: 16) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:31:11'),(47,2,'admin','New Booking','New booking placed (Booking ID: 16) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 06:31:11'),(48,4,'admin','New Booking','New booking placed (Booking ID: 16) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 06:31:11'),(49,1,'admin','New Booking','New booking placed (Booking ID: 17) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 07:29:17'),(50,2,'admin','New Booking','New booking placed (Booking ID: 17) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 07:29:17'),(51,4,'admin','New Booking','New booking placed (Booking ID: 17) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 07:29:17'),(52,1,'admin','New Booking','New booking placed (Booking ID: 18) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 07:37:06'),(53,2,'admin','New Booking','New booking placed (Booking ID: 18) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-25 07:37:06'),(54,4,'admin','New Booking','New booking placed (Booking ID: 18) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-25 07:37:06'),(55,1,'admin','New Booking','New booking placed (Booking ID: 19) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-28 01:16:56'),(56,2,'admin','New Booking','New booking placed (Booking ID: 19) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-28 01:16:56'),(57,4,'admin','New Booking','New booking placed (Booking ID: 19) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-28 01:16:56'),(58,1,'admin','New Booking','New booking placed (Booking ID: 20) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-28 02:10:37'),(59,2,'admin','New Booking','New booking placed (Booking ID: 20) by client ID 2.','http://localhost/CMA/admin/ad_bookings',0,'2026-01-28 02:10:37'),(60,4,'admin','New Booking','New booking placed (Booking ID: 20) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-28 02:10:37'),(61,1,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 02:11:46'),(62,2,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 02:11:46'),(63,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-28 02:11:46'),(64,1,'admin','New Leave Request','New leave request submitted by caretaker Indika Fernando','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 02:28:00'),(65,2,'admin','New Leave Request','New leave request submitted by caretaker Indika Fernando','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 02:28:00'),(66,4,'admin','New Leave Request','New leave request submitted by caretaker Indika Fernando','http://localhost/CMA/admin/ad_leave',1,'2026-01-28 02:28:00'),(67,1,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 02:57:06'),(68,2,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 02:57:06'),(69,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-28 02:57:06'),(70,1,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 03:11:40'),(71,2,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 03:11:40'),(72,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-28 03:11:40'),(73,1,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 04:46:10'),(74,2,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 04:46:10'),(75,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-28 04:46:10'),(76,1,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 05:00:30'),(77,2,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-01-28 05:00:30'),(78,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-28 05:00:30'),(79,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-30 03:05:15'),(80,4,'admin','New Booking','New booking placed (Booking ID: 21) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 03:42:59'),(81,4,'admin','New Booking','New booking placed (Booking ID: 22) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 04:00:02'),(82,4,'admin','New Booking','New booking placed (Booking ID: 23) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 04:18:07'),(83,4,'admin','New Booking','New booking placed (Booking ID: 24) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 05:17:29'),(84,4,'admin','New Booking','New booking placed (Booking ID: 25) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 05:18:33'),(85,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-01-31 06:25:19'),(86,4,'admin','New Booking','New booking placed (Booking ID: 26) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 10:20:40'),(87,4,'admin','New Leave Request','New leave request submitted by caretaker akshara','http://localhost/CMA/admin/ad_leave',1,'2026-01-31 10:21:28'),(88,4,'admin','New Booking','New booking placed (Booking ID: 27) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 10:55:28'),(89,4,'admin','New Booking','New booking placed (Booking ID: 28) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 10:58:35'),(90,2,'client','Booking Approved','Your booking #12 has been approved.\nCustomization fee: LKR 500.00\nTotal payment: LKR 19,700.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:45:55'),(91,3,'client','Booking Approved','Your booking #9 has been approved.\nCustomization fee: LKR 200.00\nTotal payment: LKR 36,200.00','http://localhost/CMA/client/c_upcomingBookings',0,'2026-01-31 11:46:53'),(92,2,'client','Booking Approved','Your booking #27 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 9,000.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:38'),(93,2,'client','Booking Approved','Your booking #26 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 84,000.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:41'),(94,2,'client','Booking Approved','Your booking #25 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 84,000.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:43'),(95,2,'client','Booking Approved','Your booking #24 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 84,000.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:46'),(96,2,'client','Booking Approved','Your booking #23 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 1,200.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:48'),(97,2,'client','Booking Approved','Your booking #22 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 2,000.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:51'),(98,2,'client','Booking Approved','Your booking #21 has been approved.\nCustomization fee: LKR 0.00\nTotal payment: LKR 135,000.00','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 11:48:54'),(99,2,'client','Booking Approved','Your booking #19 was approved. Total: Rs.12,800.00.','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 12:08:43'),(100,4,'admin','New Booking','New booking placed (Booking ID: 29) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 12:12:47'),(101,2,'client','Booking Approved','Your booking #29 was approved. Customization fee: Rs.500.00. New total: Rs.576,500.00.','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 12:13:06'),(102,4,'admin','New Booking','New booking placed (Booking ID: 30) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 12:19:16'),(103,2,'client','Booking Approved','Your booking #30 was approved. Customization fee: Rs.300.00. New total: Rs.9,900.00.','http://localhost/CMA/client/c_upcomingBookings',1,'2026-01-31 12:19:34'),(104,4,'admin','New Booking','New booking placed (Booking ID: 31) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 15:36:00'),(105,4,'admin','New Booking','New booking placed (Booking ID: 32) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 15:52:25'),(106,4,'admin','New Booking','New booking placed (Booking ID: 33) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 15:55:16'),(107,4,'admin','New Booking','New booking placed (Booking ID: 34) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-01-31 16:07:10'),(108,4,'admin','New Booking','New booking placed (Booking ID: 35) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-02-01 03:55:13'),(109,6,'client','Booking Approved','Your booking #35 was approved. Customization fee: Rs.200.00. New total: Rs.50,600.00.','http://localhost/CMA/client/c_upcomingBookings',1,'2026-02-01 04:07:04'),(110,4,'admin','New Booking','New booking placed (Booking ID: 36) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-02-01 04:09:24'),(111,6,'client','Booking Approved','Your booking #36 was approved. Customization fee: Rs.1.00. New total: Rs.1,201.00.','http://localhost/CMA/client/c_upcomingBookings',1,'2026-02-01 04:11:28'),(112,6,'client','Booking Approved','Your booking #34 was approved. Customization fee: Rs.100.00. New total: Rs.126,100.00. Make your payment to confirm the booking.','http://localhost/CMA/client/c_upcomingBookings',1,'2026-02-01 04:32:36'),(113,4,'admin','New Booking','New booking placed (Booking ID: 37) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-02-02 08:06:14'),(114,4,'admin','New Booking','New booking placed (Booking ID: 38) by client ID 6.','http://localhost/CMA/admin/ad_bookings',1,'2026-02-02 08:07:08'),(115,6,'client','Booking Approved','Your booking #37 was approved. Customization fee: Rs.300.00. New total: Rs.12,900.00. Make your payment to confirm the booking.','http://localhost/CMA/client/c_upcomingBookings',0,'2026-02-02 08:08:13'),(116,4,'admin','New Booking','New booking placed (Booking ID: 39) by client ID 2.','http://localhost/CMA/admin/ad_bookings',1,'2026-02-04 02:21:03'),(117,5,'Manager','New Booking Request','Client piyula xdfsf has submitted a new booking request (ID: 1)','http://localhost/CMA/hr/index',1,'2026-02-09 04:56:30'),(118,2,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=1',1,'2026-02-09 05:24:32'),(119,5,'Manager','Advance Payment Received','Advance payment received from client piyula xdfsf (ID: 2) - Rs. 40,000.00 for booking #1.','http://localhost/CMA/hr/pendingPayments',1,'2026-02-09 05:32:08'),(120,5,'Manager','New Booking Request','Client piyula xdfsf has submitted a new booking request (ID: 2)','http://localhost/CMA/hr/index',1,'2026-02-09 06:28:24'),(121,9,'caretaker','Booking Accepted','Booking #1 has been accepted after payment approval. Client: piyula xdfsf. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=1&tab=upcoming',0,'2026-02-09 06:31:17'),(122,2,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=2',1,'2026-02-09 06:31:51'),(123,5,'Manager','New Booking Request','Client piyula xdfsf has submitted a new booking request (ID: 3)','http://localhost/CMA/hr/index',1,'2026-02-09 06:44:58'),(124,2,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=3',1,'2026-02-09 06:45:50'),(125,5,'Manager','Advance Payment Received','Advance payment received from client piyula xdfsf (ID: 2) - Rs. 40,200.00 for booking #3.','http://localhost/CMA/hr/pendingPayments',1,'2026-02-09 06:46:51'),(126,5,'Manager','New Booking Request','Client piyula xdfsf has submitted a new booking request (ID: 4)','http://localhost/CMA/hr/index',1,'2026-02-10 02:41:15'),(127,0,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=4',0,'2026-02-10 03:09:27'),(128,0,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=4',0,'2026-02-10 03:23:34'),(129,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 5)','http://localhost/CMA/hr/index',1,'2026-02-28 03:19:32'),(130,7,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=5',1,'2026-02-28 03:28:33'),(131,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 6)','http://localhost/CMA/hr/index',1,'2026-02-28 03:38:30'),(132,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 7)','http://localhost/CMA/hr/index',1,'2026-02-28 04:04:48'),(133,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 8)','http://localhost/CMA/hr/index',1,'2026-02-28 04:10:25'),(134,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 9)','http://localhost/CMA/hr/index',1,'2026-02-28 04:12:39'),(135,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 40,500.00 for booking #5.','http://localhost/CMA/hr/pendingPayments',1,'2026-02-28 04:45:35'),(136,9,'caretaker','Booking Accepted','Booking #5 has been accepted after payment approval. Client: sulojan rajkumar. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=5&tab=upcoming',0,'2026-02-28 04:46:22'),(137,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 10)','http://localhost/CMA/hr/index',1,'2026-02-28 05:28:09'),(138,7,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=10',1,'2026-02-28 05:28:49'),(139,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 15,000.00 for booking #10.','http://localhost/CMA/hr/pendingPayments',1,'2026-02-28 05:29:58'),(140,30,'caretaker','Booking Accepted','Booking #10 has been accepted after payment approval. Client: sulojan rajkumar. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=10&tab=upcoming',0,'2026-02-28 05:30:44'),(141,5,'Manager','Caretaker Change Request','Client sulojan rajkumar has requested a caregiver change for booking #10. Reason: the caregiver is not supportive','http://localhost/CMA/hr/changeRequests',1,'2026-02-28 05:59:52'),(142,7,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=6',1,'2026-02-28 13:08:21'),(143,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 11)','http://localhost/CMA/hr/index',1,'2026-02-28 14:04:25'),(144,7,'client','Advance Payment Required','Please pay the advance payment to proceed with your booking.','http://localhost/CMA/client/c_makePayment?booking_id=11',1,'2026-02-28 14:05:37'),(145,5,'Manager','Reschedule Request','Client sulojan rajkumar has requested to reschedule booking #6 from 2026-03-04 (Full Time (8am - 5pm)) to 2026-03-06 ().','http://localhost/CMA/hr/rescheduleRequests',1,'2026-02-28 16:18:47'),(146,7,'client','Reschedule Approved','Your booking #6 has been rescheduled to 2026-03-06 (Full Time (8am - 5pm)).','http://localhost/CMA/client/c_upcomingBookings',1,'2026-02-28 16:24:36'),(147,17,'caretaker','Reschedule Approved','Booking #6 assigned to you has been rescheduled to 2026-03-06 (Full Time (8am - 5pm)).','http://localhost/CMA/caretaker/ct_ongoingBookings',0,'2026-02-28 16:24:36'),(148,5,'Manager','Caretaker Change Request','Client sulojan rajkumar has requested a caregiver change for booking #5. Reason: grht','http://localhost/CMA/hr/changeRequests',1,'2026-02-28 16:55:36'),(149,5,'Manager','Caretaker Change Request','Client sulojan rajkumar has requested a caregiver change for booking #10. Reason: gfhtrh','http://localhost/CMA/hr/changeRequests',1,'2026-02-28 17:42:03'),(150,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 9,000.00 for booking #11.','http://localhost/CMA/hr/pendingPayments',1,'2026-03-01 03:34:29'),(151,5,'Manager','Caretaker Change Request','Client sulojan rajkumar has requested a caregiver change for booking #10. Reason: gfhtyjy','http://localhost/CMA/hr/changeRequests',1,'2026-03-01 04:20:47'),(152,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #9 | Service: Babysitter | Date: 2026-03-13 | Time: Morning (8am - 12pm) | Duration: 4 Monthly | Caregiver: Nadeesha Kumari\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=9',1,'2026-03-01 06:39:50'),(153,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #8 | Service: Maid | Date: 2026-03-05 | Time: 10:00 | Duration: 5 Hourly | Caregiver: amala\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=8',1,'2026-03-01 06:47:25'),(154,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #7 | Service: Elder Care | Date: 2026-03-05 | Time: Full Time (8am - 5pm) | Duration: 3 Yearly | Caregiver: Sunil Fernando\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=7',1,'2026-03-01 06:47:28'),(155,5,'Manager','New Booking Request','Client sulojan rajkumar has submitted a new booking request (ID: 12)','http://localhost/CMA/hr/index',1,'2026-03-01 10:30:43'),(156,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #12 | Service: Babysitter | Date: 2026-03-12 | Time: Morning (8am - 12pm) | Duration: 2 Monthly | Caregiver: pugalanthi\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=12',1,'2026-03-01 10:31:20'),(157,5,'Manager','Reschedule Request','Client sulojan rajkumar has requested to reschedule booking #7 from 2026-03-05 (Full Time (8am - 5pm)) to 2026-03-11 ().','http://localhost/CMA/hr/rescheduleRequests',1,'2026-03-01 11:12:47'),(158,5,'Manager','New Booking Request','New booking placed.\nBooking #13 | Babysitter\nClient: sulojan rajkumar (sulojan@gmail.com)\nDate: 2026-05-06 | Time: Morning (8am - 12pm)\nDuration: 6 Daily\nLocation: Colombo, 93\nTotal: LKR 10,800\nCaretaker: Sachini Jay\n','http://localhost/CMA/hr/pendingRequests?booking_id=13',1,'2026-03-02 01:19:22'),(159,4,'admin','New Complaint','A new complaint was submitted by sulojan rajkumar (Caregiver: pugalanthi).','http://localhost/CMA/admin/ad_feedback',0,'2026-03-02 01:22:38'),(160,4,'admin','New Complaint','A new complaint was submitted by sulojan rajkumar (Caregiver: satheeshan).','http://localhost/CMA/admin/ad_feedback',0,'2026-03-02 01:47:14'),(161,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-03-02 02:20:04'),(162,5,'Manager','New Leave Request','Caretaker ID: 39\nType: Vacation\nDates: 2026-03-18 to 2026-03-20\nTime: 09:00 - 17:00\nReason: fdxgthyh','http://localhost/CMA/hr/leaves/view/33',1,'2026-03-02 03:24:53'),(163,4,'admin','New Leave Request','Caretaker ID: 39\nType: Vacation\nDates: 2026-03-18 to 2026-03-20\nTime: 09:00 - 17:00\nReason: fdxgthyh','http://localhost/CMA/hr/leaves/view/33',0,'2026-03-02 03:24:53'),(164,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-03-02 03:24:53'),(165,5,'Manager','New Leave Request','Caretaker ID: 39\nType: Maternity Leave\nDates: 2026-03-30 to 2026-04-04\nTime: 09:00 - 17:00\nReason: ryty','http://localhost/CMA/hr/hr_leave34',1,'2026-03-02 03:28:38'),(166,4,'admin','New Leave Request','Caretaker ID: 39\nType: Maternity Leave\nDates: 2026-03-30 to 2026-04-04\nTime: 09:00 - 17:00\nReason: ryty','http://localhost/CMA/hr/hr_leave34',1,'2026-03-02 03:28:38'),(167,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',1,'2026-03-02 03:28:38'),(168,39,'caretaker','Leave Approved','Your leave request has been approved.\nPeriod: 2026-03-18 to 2026-03-20\nNote: ghjukijtgf','http://localhost/CMA/caretaker/ct_leave',0,'2026-03-02 03:30:07'),(169,5,'Manager','New Leave Request','Caretaker ID: 39\nType: Personal Leave\nDates: 2026-03-03 to 2026-03-05\nTime: 09:00 - 17:00\nReason: sdfdgehth','http://localhost/CMA/hr/hr_leave',1,'2026-03-02 03:57:55'),(170,4,'admin','New Leave Request','Caretaker ID: 39\nType: Personal Leave\nDates: 2026-03-03 to 2026-03-05\nTime: 09:00 - 17:00\nReason: sdfdgehth','http://localhost/CMA/admin/ad_leave',0,'2026-03-02 03:57:55'),(171,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-03-02 03:57:55'),(172,5,'Manager','New Leave Request','Caretaker ID: 39\nType: Personal Leave\nDates: 2026-03-03 to 2026-03-05\nTime: 09:00 - 17:00\nReason: sdfdgehth','http://localhost/CMA/hr/hr_leave',0,'2026-03-02 03:57:55'),(173,4,'admin','New Leave Request','Caretaker ID: 39\nType: Personal Leave\nDates: 2026-03-03 to 2026-03-05\nTime: 09:00 - 17:00\nReason: sdfdgehth','http://localhost/CMA/admin/ad_leave',0,'2026-03-02 03:57:55'),(174,4,'admin','New Leave Request','New leave request submitted by caretaker shanuja venugoban','http://localhost/CMA/admin/ad_leave',0,'2026-03-02 03:57:55'),(175,4,'admin','Complaint Updated','Complaint #47 status updated to \'Open\' by HR Manager.','http://localhost/CMA/admin/ad_feedback',0,'2026-03-02 05:39:05'),(176,4,'admin','Complaint Updated','Complaint #47 status updated to \'Open\' by HR Manager.','http://localhost/CMA/admin/ad_feedback',0,'2026-03-02 05:39:11'),(177,4,'admin','Complaint Updated','Complaint #47 status updated to \'In Progress\' by HR Manager.','http://localhost/CMA/admin/ad_feedback',0,'2026-03-02 05:39:22'),(178,4,'admin','Complaint Updated','Complaint #46 status updated to \'Resolved\' by HR Manager.','http://localhost/CMA/admin/ad_feedback',0,'2026-03-02 05:39:39'),(179,5,'Manager','New Booking Request','New booking placed.\nBooking #14 | Elder Care| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-07 | Time: Morning (8am - 12pm) | \nDuration: 2 Monthly | \nLocation: matara, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 72,000\nCaretaker: evon\n','http://localhost/CMA/hr/hr_pending_request?booking_id=14',0,'2026-03-03 01:18:46'),(180,5,'Manager','New Booking Request','New booking placed.\nBooking #15 | Maid| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-07 | Time: Full Time (8am - 5pm) | \nDuration: 10 Daily | \nLocation: Vavuniya, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 33,000\nCaretaker: Kavitha Raj\n','http://localhost/CMA/hr/hr_pending_request?booking_id=15',0,'2026-03-03 02:32:49'),(181,5,'Manager','New Booking Request','New booking placed.\nBooking #16 | Elder Care| \nClient: sulojan rajkumar (sulojan@gmail.com) |\nDate: 2026-03-07 | Time: Full Time (8am - 5pm) | \nDuration: 1 Monthly | \nLocation: Matara, 303/10,mannar road paddanichoor vavuniya | \nTotal: LKR 63,000\nCaretaker: Kusum Jay\n','http://localhost/CMA/hr/hr_pending_request?booking_id=16',0,'2026-03-03 05:22:29'),(182,5,'Manager','Advance Payment Received','Advance payment received from client sulojan rajkumar (ID: 7) - Rs. 24,000.00 for booking #12.','http://localhost/CMA/hr/pendingPayments',0,'2026-03-03 05:23:44'),(183,5,'caretaker','Booking Accepted','Booking #12 has been accepted after payment approval. Client: sulojan rajkumar. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=12&tab=upcoming',0,'2026-03-03 05:24:25'),(184,20,'caretaker','Booking Accepted','Booking #11 has been accepted after payment approval. Client: sulojan rajkumar. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=11&tab=upcoming',0,'2026-03-03 05:24:34'),(185,39,'caretaker','Booking Accepted','Booking #3 has been accepted after payment approval. Client: piyula xdfsf. You can now view the booking details in your Bookings page.','http://localhost/CMA/caretaker/ct_booking?booking_id=3&tab=upcoming',0,'2026-03-03 05:24:50'),(186,7,'client','Advance Payment Required','Advance payment is required to proceed.\nBooking #14 | Service: Elder Care | Date: 2026-03-07 | Time: Morning (8am - 12pm) | Duration: 2 Monthly | Caregiver: evon\n\nClick to pay now.','http://localhost/CMA/client/c_makePayment?booking_id=14',0,'2026-03-05 05:52:07'),(187,5,'Manager','Reschedule Request','Client sulojan rajkumar has requested to reschedule booking #16 from 2026-03-07 to 2026-03-09.','http://localhost/CMA/hr/rescheduleRequests',0,'2026-03-05 06:58:45'),(188,7,'client','Reschedule Approved','Your booking #16 has been rescheduled to 2026-03-09 at Full Time (8am - 5pm).','http://localhost/CMA/client/c_upcomingBookings',0,'2026-03-05 07:00:03'),(189,12,'caretaker','Reschedule Approved','Booking #16 assigned to you has been rescheduled to 2026-03-09 at Full Time (8am - 5pm).','http://localhost/CMA/caretaker/ct_ongoingBookings',0,'2026-03-05 07:00:03');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,2,9,200000.00,0.00,40000.00,160000.00,'debit_card','advance','approved',NULL,NULL,0,'2026-02-09 05:32:07','2026-02-09 12:01:17'),(2,3,2,39,120600.00,600.00,40200.00,80400.00,'credit_card','advance','approved',NULL,NULL,0,'2026-02-09 06:46:51','2026-03-03 10:54:50'),(3,5,7,9,121500.00,1500.00,40500.00,81000.00,'debit_card','advance','approved',NULL,NULL,0,'2026-02-28 04:45:34','2026-02-28 10:16:22'),(4,10,7,30,15000.00,0.00,15000.00,0.00,'debit_card','advance','approved',NULL,NULL,0,'2026-02-28 05:29:58','2026-02-28 11:00:44'),(5,11,7,20,9000.00,0.00,9000.00,0.00,'credit_card','advance','approved',NULL,NULL,0,'2026-03-01 03:34:29','2026-03-03 10:54:34'),(6,12,7,5,48000.00,0.00,24000.00,24000.00,'debit_card','advance','approved',NULL,NULL,0,'2026-03-03 05:23:44','2026-03-03 10:54:25');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reschedule_requests`
--

LOCK TABLES `reschedule_requests` WRITE;
/*!40000 ALTER TABLE `reschedule_requests` DISABLE KEYS */;
INSERT INTO `reschedule_requests` VALUES (1,16,7,'2026-03-07','2026-03-09','i have works on that particular day','approved','2026-03-05 06:58:45','2026-03-05 12:30:03','ok i will approve your request');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','Manager') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `phone` varchar(20) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'Thanushya Venugoban','thanu28@gmail.com','$2y$10$M85ZBJZ91pFPi3sWwEgLiudhJxFdC.2sGhJwPrMcW0EUVHQKkzOhO','admin','Active','','697045605c41b.jpg','2026-01-21 03:17:05'),(5,'nanduni','nanduni@gmail.com','$2y$10$yTeb45tZN4DneyGv7KeciujsHSsJrR2ZgiGOUpwubZVMF3ni5iCPi','Manager','Active','0773607650','697048f0bb99b.jpg','2026-01-21 03:32:23');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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

-- Dump completed on 2026-03-05 13:44:30

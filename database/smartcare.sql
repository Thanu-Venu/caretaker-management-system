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
  `target_role` enum('admin','HR','staff','caretaker','client','All') DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'system maintenance','there will be a system maintenance from 12pm to 1pm','All',1,'2025-12-23 11:18:59');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caretakers`
--

LOCK TABLES `caretakers` WRITE;
/*!40000 ALTER TABLE `caretakers` DISABLE KEYS */;
INSERT INTO `caretakers` VALUES (10,'nanduni','nanduni@smartcare.com','$2y$10$ab.GJ6mXUiYgQtNNENAhIeDb0k6MTdOVWwimsZpceQXES/8avt8q2','0702248119','Elder Care','Active'),(11,'shaithra','shaithu123@gmail.com','$2y$10$MTfNTuoLiPEWZIT4hS8.H.GuVMKotog67pfr.V6nFEDtnAsxS08aO','0702248119','Maid','Active'),(12,'keerthana','keer@gmail.com','$2y$10$e4ERUF9MeItbouCXP.6C4uKnJBvNSxWSmPOxAWu5gKEE9AOnRp806','0702248119','Elder Care','Inactive'),(14,'shiya','shiya2@gmail.com','$2y$10$lPb3CqZ6fouglel673Zm9OyDELdwp0AKjnF22tR4ZbZO5gpAWSmX2','0702248119','Elder Care','Active'),(18,'sujany','suja123@gmail.com','$2y$10$fMDHbnAALcHYOwhlOMY5x.kmFQ/y3INsHzoSgJgqmJAxSgwpT0fMe','0702248119','Maid','Active'),(21,'shanuja','shanu3@gmail.com','$2y$10$D2v/d9meN.c6aACansmT3OZr1Wy3EGNfqmxbFX0XjyfRqTKme8dcm','0702248119','Elder Care','Active');
/*!40000 ALTER TABLE `caretakers` ENABLE KEYS */;
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
  `password` varchar(255) NOT NULL,
  `role` enum('client') DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Thanushya Venugoban','thanu.venu28@gmail.com','0702248119','$2y$10$rMfcf206RoBO1lE9K2E7MeKZqoms/aIbnZ8yFG1bXX6BAORUxwRMm','client','2025-12-31 04:06:31');
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table storing client complaints about caretakers';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` VALUES (31,'sujany thirualan','satheeshan','Caretaker Behavior','thanuvenu','Open','2025-10-22 15:57:10'),(32,'sujany thirualan','parmi','Service Quality','ghdhdt','Open','2025-10-22 15:58:07'),(33,'Thanushya Venugoban','satheeshan','Service Quality','thanushya','Resolved','2025-10-22 16:27:03'),(35,'Thanushya Venugoban','parmi','Late Arrival','not friendly','Open','2025-10-23 05:53:54');
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
INSERT INTO `leaves` VALUES (2,'Personal','2025-10-18','2025-10-22','09:00:00','17:00:00','hcfjyjnyt','2025-10-22 05:54:32','Pending',4),(4,'Vacation','2025-10-23','2025-10-17','09:00:00','17:00:00','gxhsgxsj','2025-10-23 08:47:05','Pending',13),(5,'Personal','2025-10-23','2025-10-24','09:00:00','17:00:00','headache','2025-10-23 16:55:31','Approved',17),(6,'Vacation','2025-10-23','2025-10-30','09:00:00','17:00:00','going for a trip','2025-10-23 16:55:50','Pending',17),(7,'Personal','2025-10-01','2025-10-03','09:00:00','17:00:00','personal reason wedding\r\n','2025-10-23 16:56:20','Pending',17),(8,'Vacation','2025-10-16','2025-10-23','09:00:00','17:00:00','thanu','2025-10-23 16:57:45','Pending',17),(9,'Sick Leave','2025-10-10','2025-10-08','09:00:00','17:00:00','thanu1','2025-10-23 17:04:44','Pending',17),(10,'Vacation','2025-10-03','2025-10-06','09:00:00','17:00:00','tha','2025-10-23 17:19:11','Pending',17),(13,'Sick Leave','2025-12-23','2025-12-25','09:00:00','17:00:00','xdbdxtrhdtyjr7y','2025-12-24 12:57:49','Approved',35),(14,'Vacation','2025-12-24','2025-12-26','00:00:00','17:03:00','thrgsilrgh;orthjtohij','2025-12-24 14:55:28','Rejected',35),(15,'Vacation','2025-12-31','2026-01-01','09:00:00','17:00:00','hthdytdy','2026-01-01 03:36:15','Pending',35);
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
  `user_type` enum('admin','HR','staff','caretaker','client') NOT NULL,
  `announcement_id` int NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','Manager') NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'satheeshan ','satheeshan@smartcare.com','$2y$10$pHe21CuA3Nad//8eWx4bO.8PvxhX5Q4D/SebTEiQD7RD0W7HW3B3y',NULL,'Manager','Active'),(16,'shaithu','shai345@gmail.com','$2y$10$Pgy7ppmyKIypGISsU9U5XuWDPWt5li1FODB5FMi7SnE.YdaX8J2Eq',NULL,'Manager','Active'),(19,'shaga','shaga@smartcare.com','$2y$10$UDKkZQbVdKjzEWRVGfB8Veddq.ReS1pBaRA.vMxq/R/hcaqb33qn2',NULL,'admin','Active'),(20,'satheeshan','satheeshan@gmail.com','$2y$10$w2JQSo6dKLFeUAhEKfysYOI90X7hAq.5CjY/fsSG6FzgJBN9V4kMu',NULL,'admin','Active'),(21,'nanduni','nanduni@gmail.com','$2y$10$FyHBzhncrZLld1vW6rf5GupRL5eoLLgcpmPjou1bDabhiIi9h04ly',NULL,'Manager','Active');
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

-- Dump completed on 2026-01-07  9:30:59

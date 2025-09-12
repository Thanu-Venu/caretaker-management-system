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
-- Table structure for table `caretakers`
--

DROP TABLE IF EXISTS `caretakers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caretakers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caretakers`
--

LOCK TABLES `caretakers` WRITE;
/*!40000 ALTER TABLE `caretakers` DISABLE KEYS */;
INSERT INTO `caretakers` VALUES (7,'Piyula','janaki23@gmail.com','','0702248119','Maid','Inactive','2025-09-11 12:11:28'),(8,'satheeshan','ambika23@gmail.com','','0702248119','Maid','Inactive','2025-09-11 12:16:11'),(9,'nanduni','venu28@gmail.com','','0702248119','Elder Care','Inactive','2025-09-11 12:17:57'),(10,'parmila','ven28@gmail.com','','0702248119','Elder Care','Active','2025-09-11 12:19:07'),(11,'Shaganjaly','ve28@gmail.com','','0702248119','Elder Care','Active','2025-09-11 12:21:55'),(12,'Vakshika','kala23@gmail.com','','45678','Elder Care','Active','2025-09-11 12:25:16'),(14,'jansika','bhavani@gmail.com','','0773607650','Elder Care','Active','2025-09-11 14:13:32'),(15,'sivameena','siva234@gmail.com','','2345','Elder Care','Inactive','2025-09-11 14:16:24'),(16,'kannmani','priya@gmail.com','','0740315650','Maid','Active','2025-09-11 14:25:11'),(17,'karuna','root3@gmail.com','$2y$10$CPpDH9KjmJyGWJHw3GZu9edr4Zut7878e.8/D.XMMiQLV7HywyIOu','32456','Babysitter','Inactive','2025-09-11 14:34:32'),(19,'Venugoban','venu05@gmail.com','$2y$10$FcWMwmw2yKOzdbNmBdkNu.WeewjTKBNWAi05lJFy8Aqvhrcg3.CoK','0702248119','Maid','Active','2025-09-12 02:16:17');
/*!40000 ALTER TABLE `caretakers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-12 14:55:23

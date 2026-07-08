-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: profitnessgym
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `administradores_backup`
--

DROP TABLE IF EXISTS `administradores_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores_backup` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telefono` (`telefono`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores_backup`
--

LOCK TABLES `administradores_backup` WRITE;
/*!40000 ALTER TABLE `administradores_backup` DISABLE KEYS */;
INSERT INTO `administradores_backup` VALUES (1,'Admin','Principal','7550000000','$2y$10$abcdefghijklmnopqrstuv','2026-05-19 15:46:17'),(2,'Saul de Jesus','San Martin Martinez','7551718660','$2b$12$0Ne6wYl9VpPsTPCN5WjeFuH3Qs0QOqDdHjgg79z8GMLFWzs.uhL0q','2026-05-20 17:04:43');
/*!40000 ALTER TABLE `administradores_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coaches`
--

DROP TABLE IF EXISTS `coaches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coaches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `edad` int NOT NULL,
  `especialidad` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `activo` tinyint DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coaches`
--

LOCK TABLES `coaches` WRITE;
/*!40000 ALTER TABLE `coaches` DISABLE KEYS */;
/*!40000 ALTER TABLE `coaches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comprobantes_pago`
--

DROP TABLE IF EXISTS `comprobantes_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comprobantes_pago` (
  `id` int NOT NULL AUTO_INCREMENT,
  `persona_id` int NOT NULL,
  `folio_cliente` varchar(20) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `status` enum('PENDIENTE','CONFIRMADO','RECHAZADO') DEFAULT 'PENDIENTE',
  `motivo_rechazo` text,
  `observacion` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_subida` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `revisado_por` int DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `persona_id` (`persona_id`),
  KEY `fk_comprobante_usuario` (`revisado_por`),
  CONSTRAINT `comprobantes_pago_ibfk_1` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  CONSTRAINT `fk_comprobante_usuario` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comprobantes_pago`
--

LOCK TABLES `comprobantes_pago` WRITE;
/*!40000 ALTER TABLE `comprobantes_pago` DISABLE KEYS */;
INSERT INTO `comprobantes_pago` VALUES (1,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_098d5981111b807f45c17e67327c8156.png','Mensualidad de Julio','CONFIRMADO',NULL,NULL,'2026-06-25 17:37:11','2026-06-25 17:37:11','2026-06-26 19:53:38',2,NULL),(2,3,'CLI-B6526C','comprobantes/CLI-B6526C/2026/06/CMP_4fc29e12c451b03f0984bd3fa81f21f5.pdf','Mensualidad de Julio','CONFIRMADO',NULL,NULL,'2026-06-25 18:50:02','2026-06-25 18:50:02','2026-06-27 01:07:28',2,NULL),(3,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_fc1f77f084653b7e38e458976307d575.png','Mensualidad julio','CONFIRMADO',NULL,NULL,'2026-06-27 00:54:46','2026-06-27 00:54:46','2026-06-27 01:07:11',2,NULL),(4,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_f9c7028533131a572aba3a3abc614227.png','Mensualidad Junio 26 de Junio a 25 de Julio del 2026','RECHAZADO','Su comprobante no es legible por favor verifique que se vea mejor',NULL,'2026-06-27 01:36:22','2026-06-27 01:36:22','2026-06-27 01:46:46',2,NULL),(5,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_a9549503b4f2963747f61236540dea9b.pdf','Mensualidad Junio 26 de Junio a 25 de Julio del 2026 reenvio','CONFIRMADO',NULL,NULL,'2026-06-27 05:20:19','2026-06-27 05:20:19','2026-06-27 05:20:54',2,NULL),(6,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_016cd27b102010febebcf43028917755.png','prueba','CONFIRMADO',NULL,NULL,'2026-06-27 19:05:38','2026-06-27 19:05:38','2026-06-27 19:06:00',2,NULL),(7,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_4bd8d1d8089531e4a0bc0b8444201752.png','prueba2','RECHAZADO','Imagen borrosa',NULL,'2026-06-27 19:08:35','2026-06-27 19:08:35','2026-06-27 19:08:52',2,NULL),(8,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_ff85abd91f12dbf3bec86a444f7d8594.pdf','prueba3','CONFIRMADO',NULL,NULL,'2026-06-27 19:09:12','2026-06-27 19:09:12','2026-06-27 19:09:25',2,NULL),(9,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_f09a65444f6368769da496706d6c6567.jpg','Mensualidad desde telefono','CONFIRMADO',NULL,NULL,'2026-06-27 19:57:00','2026-06-27 19:57:00','2026-06-27 19:57:33',2,NULL),(10,6,'CLI-DC4E33','comprobantes/CLI-DC4E33/2026/06/CMP_95a7c5f915a36e1da789b52227556bb4.jpeg','','CONFIRMADO',NULL,NULL,'2026-06-27 21:44:02','2026-06-27 21:44:02','2026-06-27 21:46:24',2,NULL),(11,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_0701c2b0eadc870d570096d54fb4bf5f.jpg','','RECHAZADO','Otro',NULL,'2026-06-27 21:44:48','2026-06-27 21:44:48','2026-06-27 21:45:07',2,NULL),(12,2,'CLI-E2EEA7','comprobantes/CLI-E2EEA7/2026/06/CMP_f565fd573a63bfe3b48bf5cc51954451.jpg','','CONFIRMADO',NULL,NULL,'2026-06-27 21:45:36','2026-06-27 21:45:36','2026-06-27 21:45:54',2,NULL),(13,8,'CLI-45F0BE','comprobantes/CLI-45F0BE/2026/06/CMP_29642a68dc68cbf863bb684fdeb5643a.jpg','','CONFIRMADO',NULL,NULL,'2026-06-27 23:36:01','2026-06-27 23:36:01','2026-06-27 23:36:36',2,NULL),(14,5,'CLI-07E809','comprobantes/CLI-07E809/2026/06/CMP_06f21cc40a75e3bea2229d2cb97c2958.pdf','','CONFIRMADO',NULL,NULL,'2026-06-27 23:40:31','2026-06-27 23:40:31','2026-06-27 23:41:01',2,NULL),(15,4,'CLI-8312FA','comprobantes/CLI-8312FA/2026/06/CMP_94d638ccb26da921eae3c7a79dd475c1.pdf','','CONFIRMADO',NULL,NULL,'2026-06-27 23:48:23','2026-06-27 23:48:23','2026-06-27 23:49:10',2,NULL);
/*!40000 ALTER TABLE `comprobantes_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_transferencias`
--

DROP TABLE IF EXISTS `configuracion_transferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion_transferencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `banco` varchar(100) DEFAULT NULL,
  `clabe` varchar(50) DEFAULT NULL,
  `titular` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_transferencias`
--

LOCK TABLES `configuracion_transferencias` WRITE;
/*!40000 ALTER TABLE `configuracion_transferencias` DISABLE KEYS */;
INSERT INTO `configuracion_transferencias` VALUES (1,'BBVA','589674586325963225','PRO FITNESS GYM');
/*!40000 ALTER TABLE `configuracion_transferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresias`
--

DROP TABLE IF EXISTS `membresias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membresias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `promocion` tinyint(1) DEFAULT '0',
  `precio_promocion` decimal(10,2) DEFAULT NULL,
  `dias` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresias`
--

LOCK TABLES `membresias` WRITE;
/*!40000 ALTER TABLE `membresias` DISABLE KEYS */;
INSERT INTO `membresias` VALUES (1,'Semanal','Acceso completo al gimnasio durante 7 días.',200.00,0,NULL,7,1,'2026-06-06 20:27:29'),(2,'Quincenal','Acceso completo al gimnasio durante 15 días.',300.00,0,NULL,15,1,'2026-06-06 20:27:29'),(3,'Mensual','Acceso completo al gimnasio durante 30 días.',450.00,0,NULL,30,1,'2026-06-06 20:27:29'),(7,'Anualidad','Acceso completo al gimnasio por un año',5400.00,1,4000.00,365,1,'2026-06-16 16:04:12'),(11,'Mensualidad para estudiantes','Si eres estudiantes tienes un pequeño descuento',450.00,1,400.00,30,1,'2026-06-26 19:58:18');
/*!40000 ALTER TABLE `membresias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresias_cliente`
--

DROP TABLE IF EXISTS `membresias_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membresias_cliente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `persona_id` int NOT NULL,
  `membresia_id` int NOT NULL,
  `venta_id` int DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `precio_pagado` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mc_persona` (`persona_id`),
  KEY `fk_mc_membresia` (`membresia_id`),
  KEY `fk_mc_venta` (`venta_id`),
  CONSTRAINT `fk_mc_membresia` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`),
  CONSTRAINT `fk_mc_persona` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  CONSTRAINT `fk_mc_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresias_cliente`
--

LOCK TABLES `membresias_cliente` WRITE;
/*!40000 ALTER TABLE `membresias_cliente` DISABLE KEYS */;
INSERT INTO `membresias_cliente` VALUES (1,2,3,6,'2026-06-26','2026-07-25',450.00,'2026-06-26 19:53:38'),(2,2,11,7,'2026-06-27','2026-07-26',450.00,'2026-06-27 01:07:11'),(3,3,2,8,'2026-06-27','2026-07-11',300.00,'2026-06-27 01:07:28'),(4,2,11,12,'2026-06-27','2026-07-26',450.00,'2026-06-27 05:20:54'),(5,2,11,13,'2026-06-27','2026-07-26',450.00,'2026-06-27 19:06:00'),(6,2,3,14,'2026-06-27','2026-07-26',450.00,'2026-06-27 19:09:25'),(7,2,11,18,'2026-06-27','2026-07-26',450.00,'2026-06-27 19:57:33'),(8,2,11,27,'2026-06-27','2026-07-26',450.00,'2026-06-27 21:45:54'),(9,6,3,28,'2026-06-27','2026-07-26',450.00,'2026-06-27 21:46:24'),(10,8,3,31,'2026-06-27','2026-07-26',450.00,'2026-06-27 23:36:36'),(11,5,3,32,'2026-06-27','2026-07-26',450.00,'2026-06-27 23:41:01'),(12,4,3,33,'2026-06-27','2026-07-26',450.00,'2026-06-27 23:49:10');
/*!40000 ALTER TABLE `membresias_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personas`
--

DROP TABLE IF EXISTS `personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `folio` varchar(20) DEFAULT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `membresia_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `fk_persona_membresia` (`membresia_id`),
  CONSTRAINT `fk_persona_membresia` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personas`
--

LOCK TABLES `personas` WRITE;
/*!40000 ALTER TABLE `personas` DISABLE KEYS */;
INSERT INTO `personas` VALUES (2,'Kevin Narciso San Martin Martinez','CLI-E2EEA7','2026-06-27','2026-07-26',1,'2026-06-24 05:08:03',11),(3,'Angel David Velez Martinez','CLI-B6526C','2026-04-27','2026-05-11',1,'2026-06-24 05:08:57',2),(4,'Axel Peñaloza Martinez','CLI-8312FA','2026-06-27','2026-07-26',1,'2026-06-27 19:58:04',3),(5,'Giselle Peñaloza Martinez','CLI-07E809','2026-06-27','2026-07-26',1,'2026-06-27 19:59:51',3),(6,'Cristian Velez Martinez','CLI-DC4E33','2026-06-27','2026-07-26',1,'2026-06-27 20:01:25',3),(7,'Yamileth Villagomez Martinez','CLI-2E024D','2026-04-27','2026-05-27',1,'2026-06-27 20:02:02',3),(8,'Luis Alberto Flores Cebrero','CLI-45F0BE','2026-06-27','2026-07-26',1,'2026-06-27 20:06:55',3),(9,'Israel Gutierrez Molina','CLI-7FB2EB','2026-04-27','2026-05-27',1,'2026-06-27 20:11:46',3);
/*!40000 ALTER TABLE `personas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador'),(2,'Recepcionista');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_rol` (`rol_id`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,'Admin','7550000000','$2y$10$abcdefghijklmnopqrstuv',1,'2026-06-14 23:54:49'),(2,1,'Saul de Jesus','7551718660','$2b$12$0Ne6wYl9VpPsTPCN5WjeFuH3Qs0QOqDdHjgg79z8GMLFWzs.uhL0q',1,'2026-06-14 23:54:49');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo` enum('MEMBRESIA','VISITA','PRODUCTO','TOALLA') NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `referencia_id` int DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_venta_usuario` (`usuario_id`),
  CONSTRAINT `fk_venta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,2,'MEMBRESIA','Mensual',1,450.00,'2026-06-23 22:34:16','2026-06-24 04:34:16'),(2,2,'MEMBRESIA','Mensual',2,450.00,'2026-06-23 23:08:03','2026-06-24 05:08:03'),(3,2,'MEMBRESIA','Mensual',3,450.00,'2026-06-23 23:08:57','2026-06-24 05:08:57'),(4,2,'VISITA','Visita de hoja',1,50.00,'2026-06-26 13:51:59','2026-06-26 19:51:59'),(5,2,'TOALLA','Renta de toalla',NULL,25.00,'2026-06-26 13:52:03','2026-06-26 19:52:03'),(6,2,'MEMBRESIA','Renovación por transferencia - Mensual',2,450.00,'2026-06-26 13:53:38','2026-06-26 19:53:38'),(7,2,'MEMBRESIA','Renovación por transferencia - Mensualidad para estudiantes',2,450.00,'2026-06-26 19:07:11','2026-06-27 01:07:11'),(8,2,'MEMBRESIA','Renovación por transferencia - Quincenal',3,300.00,'2026-06-26 19:07:28','2026-06-27 01:07:28'),(9,2,'VISITA','Visita de Rafa',2,50.00,'2026-06-26 19:07:36','2026-06-27 01:07:36'),(10,2,'TOALLA','Renta de toalla',NULL,25.00,'2026-06-26 19:07:39','2026-06-27 01:07:39'),(11,2,'TOALLA','Renta de toalla',NULL,25.00,'2026-06-26 19:07:42','2026-06-27 01:07:42'),(12,2,'MEMBRESIA','Renovación por transferencia - Mensualidad para estudiantes',2,450.00,'2026-06-26 23:20:54','2026-06-27 05:20:54'),(13,2,'MEMBRESIA','Renovación por transferencia - Mensualidad para estudiantes',2,450.00,'2026-06-27 13:06:00','2026-06-27 19:06:00'),(14,2,'MEMBRESIA','Renovación por transferencia - Mensual',2,450.00,'2026-06-27 13:09:25','2026-06-27 19:09:25'),(15,2,'TOALLA','Renta de toalla',NULL,25.00,'2026-06-27 13:09:30','2026-06-27 19:09:30'),(16,2,'VISITA','Visita de Mariana',3,50.00,'2026-06-27 13:09:49','2026-06-27 19:09:49'),(17,2,'VISITA','Visita de Kevin',4,50.00,'2026-06-27 13:09:56','2026-06-27 19:09:56'),(18,2,'MEMBRESIA','Renovación por transferencia - Mensualidad para estudiantes',2,450.00,'2026-06-27 13:57:33','2026-06-27 19:57:33'),(19,2,'MEMBRESIA','Mensual',4,450.00,'2026-06-27 13:58:04','2026-06-27 19:58:04'),(20,2,'MEMBRESIA','Mensual',5,450.00,'2026-06-27 13:59:51','2026-06-27 19:59:51'),(21,2,'VISITA','Visita de hoja',5,50.00,'2026-06-27 14:00:59','2026-06-27 20:00:59'),(22,2,'VISITA','Visita de Rafa',6,50.00,'2026-06-27 14:01:01','2026-06-27 20:01:01'),(23,2,'MEMBRESIA','Mensual',6,450.00,'2026-06-27 14:01:25','2026-06-27 20:01:25'),(24,2,'MEMBRESIA','Mensual',7,450.00,'2026-06-27 14:02:02','2026-06-27 20:02:02'),(25,2,'MEMBRESIA','Mensual',8,450.00,'2026-06-27 14:06:55','2026-06-27 20:06:55'),(26,2,'MEMBRESIA','Mensual',9,450.00,'2026-06-27 14:11:46','2026-06-27 20:11:46'),(27,2,'MEMBRESIA','Renovación por transferencia - Mensualidad para estudiantes',2,450.00,'2026-06-27 15:45:54','2026-06-27 21:45:54'),(28,2,'MEMBRESIA','Renovación por transferencia - Mensual',6,450.00,'2026-06-27 15:46:24','2026-06-27 21:46:24'),(29,2,'TOALLA','Renta de toalla',NULL,25.00,'2026-06-27 15:46:56','2026-06-27 21:46:56'),(30,2,'VISITA','Visita de cris',7,50.00,'2026-06-27 15:47:06','2026-06-27 21:47:06'),(31,2,'MEMBRESIA','Renovación por transferencia - Mensual',8,450.00,'2026-06-27 17:36:36','2026-06-27 23:36:36'),(32,2,'MEMBRESIA','Renovación por transferencia - Mensual',5,450.00,'2026-06-27 17:41:01','2026-06-27 23:41:01'),(33,2,'MEMBRESIA','Renovación por transferencia - Mensual',4,450.00,'2026-06-27 17:49:10','2026-06-27 23:49:10');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitantes`
--

DROP TABLE IF EXISTS `visitantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitantes`
--

LOCK TABLES `visitantes` WRITE;
/*!40000 ALTER TABLE `visitantes` DISABLE KEYS */;
INSERT INTO `visitantes` VALUES (1,'hoja','2026-06-26 19:51:59'),(2,'Rafa','2026-06-27 01:07:36'),(3,'Mariana','2026-06-27 19:09:49'),(4,'Kevin','2026-06-27 19:09:56'),(5,'cris','2026-06-27 21:47:06');
/*!40000 ALTER TABLE `visitantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitas`
--

DROP TABLE IF EXISTS `visitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `visitante_id` int NOT NULL,
  `fecha_visita` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `visitante_id` (`visitante_id`),
  CONSTRAINT `visitas_ibfk_1` FOREIGN KEY (`visitante_id`) REFERENCES `visitantes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitas`
--

LOCK TABLES `visitas` WRITE;
/*!40000 ALTER TABLE `visitas` DISABLE KEYS */;
INSERT INTO `visitas` VALUES (1,1,'2026-06-26 19:51:59'),(2,2,'2026-06-27 01:07:36'),(3,3,'2026-06-27 19:09:49'),(4,4,'2026-06-27 19:09:56'),(5,1,'2026-06-27 20:00:59'),(6,2,'2026-06-27 20:01:01'),(7,5,'2026-06-27 21:47:06');
/*!40000 ALTER TABLE `visitas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-02 11:34:43

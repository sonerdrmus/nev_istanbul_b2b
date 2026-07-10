-- MySQL dump 10.13  Distrib 8.0.44, for Linux (aarch64)
--
-- Host: localhost    Database: new_istanbul_b2b
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_holder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TRY',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_accounts`
--

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner_slides`
--

DROP TABLE IF EXISTS `banner_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_slides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Üst etiket, örn: Yeni Sezon',
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ana başlık',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Açıklama metni',
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_align` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'left' COMMENT 'left, center, right',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner_slides`
--

LOCK TABLES `banner_slides` WRITE;
/*!40000 ALTER TABLE `banner_slides` DISABLE KEYS */;
INSERT INTO `banner_slides` VALUES (1,'banner_slides/01KR2992EXG1G89YC73NV0KJFY.jpeg',NULL,NULL,NULL,NULL,NULL,'left',0,0,'2026-03-09 19:33:56','2026-05-07 22:46:04'),(2,'banner_slides/01KR29VMXCSGNAWQESP77CNH9V.jpeg',NULL,NULL,NULL,NULL,NULL,'right',1,1,'2026-03-09 19:33:56','2026-05-07 22:45:12'),(3,NULL,'B2B Çözümler','Şirketinize Özel Fiyatlar ve Toplu Sipariş','Giriş yaparak fiyatları görüntüleyin, sipariş verin. Müşteri paneli ile takip edin.','Müşteri Girişi','/panel/login','center',2,1,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `banner_slides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('b2b-app-cache-356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1778328140),('b2b-app-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1778328140;',1778328140),('b2b-app-cache-livewire-rate-limiter:0e8367d0c394ea8e8d16fc7f5084e4eddb908906','i:1;',1778835010),('b2b-app-cache-livewire-rate-limiter:0e8367d0c394ea8e8d16fc7f5084e4eddb908906:timer','i:1778835010;',1778835010),('b2b-app-cache-livewire-rate-limiter:5b5e86c7b451e5528739380cdc97d0344b1eb460','i:1;',1777400793),('b2b-app-cache-livewire-rate-limiter:5b5e86c7b451e5528739380cdc97d0344b1eb460:timer','i:1777400793;',1777400793),('b2b-app-cache-tcmb_spot_rates_usd_eur','a:2:{s:3:\"USD\";d:45.3482;s:3:\"EUR\";d:53.0971;}',1778843660);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `show_in_top_menu` tinyint(1) NOT NULL DEFAULT '0',
  `top_menu_sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,NULL,'Tişört','tisort',NULL,1,1,1,0,'2026-03-09 19:33:56','2026-05-07 22:08:38'),(3,NULL,'Bags','bags',NULL,2,1,1,0,'2026-03-09 19:33:56','2026-05-07 22:47:19'),(4,NULL,'Towels','towels',NULL,3,1,1,0,'2026-03-09 19:33:56','2026-05-07 22:47:49'),(5,NULL,'Hats','hats',NULL,4,1,1,0,'2026-03-09 19:33:56','2026-05-07 22:48:27'),(10,NULL,'Socks','socks',NULL,5,1,1,0,'2026-03-09 19:33:56','2026-05-07 22:48:33'),(21,NULL,'Aprons','aprons',NULL,6,1,1,0,'2026-03-12 17:59:06','2026-05-07 22:48:38'),(22,NULL,'Çantalar','cantalar',NULL,10,1,0,0,'2026-04-28 18:35:14','2026-05-07 22:49:50'),(23,NULL,'Şapkalar','sapkalar',NULL,11,1,0,0,'2026-04-28 18:35:14','2026-04-28 18:35:14'),(24,NULL,'İş Giyim','is-giyim',NULL,12,1,0,0,'2026-04-28 18:35:14','2026-04-28 18:35:14'),(25,NULL,'Ev Tekstili','ev-tekstili',NULL,13,1,0,0,'2026-04-28 18:35:14','2026-04-28 18:35:14'),(26,NULL,'Promosyon','promosyon',NULL,14,1,0,0,'2026-04-28 18:35:14','2026-04-28 18:35:14'),(27,NULL,'Tekstil Aksesuarları','tekstil-aksesuarlari',NULL,15,1,0,0,'2026-04-28 18:35:14','2026-04-28 18:35:14');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `profit_margin_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Nev İstanbul','DEMO',1,0.00,'2026-03-09 19:33:55','2026-04-14 17:35:00');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `decimal_places` tinyint unsigned NOT NULL DEFAULT '2',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'TRY','Türk Lirası','₺',1.0000,2,1,1,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(2,'USD','Amerikan Doları','$',34.0000,2,0,2,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(3,'EUR','Euro','€',37.0000,2,0,3,1,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_groups`
--

DROP TABLE IF EXISTS `customer_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_groups`
--

LOCK TABLES `customer_groups` WRITE;
/*!40000 ALTER TABLE `customer_groups` DISABLE KEYS */;
INSERT INTO `customer_groups` VALUES (1,'Varsayılan',0,'2026-03-09 19:31:55','2026-03-09 19:31:55');
/*!40000 ALTER TABLE `customer_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dealer_requests`
--

DROP TABLE IF EXISTS `dealer_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealer_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tc_no` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_phone` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limited_company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_reg_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_profile` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest_areas` json DEFAULT NULL,
  `how_heard_about_us` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `document_pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_jpeg_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_company_id` bigint unsigned DEFAULT NULL,
  `created_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dealer_requests_email_unique` (`email`),
  KEY `dealer_requests_approved_by_foreign` (`approved_by`),
  KEY `dealer_requests_created_company_id_foreign` (`created_company_id`),
  KEY `dealer_requests_created_user_id_foreign` (`created_user_id`),
  KEY `dealer_requests_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `dealer_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dealer_requests_created_company_id_foreign` FOREIGN KEY (`created_company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dealer_requests_created_user_id_foreign` FOREIGN KEY (`created_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dealer_requests`
--

LOCK TABLES `dealer_requests` WRITE;
/*!40000 ALTER TABLE `dealer_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `dealer_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_menu_groups`
--

DROP TABLE IF EXISTS `footer_menu_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_menu_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menu',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_menu_groups`
--

LOCK TABLES `footer_menu_groups` WRITE;
/*!40000 ALTER TABLE `footer_menu_groups` DISABLE KEYS */;
INSERT INTO `footer_menu_groups` VALUES (1,'Kategoriler','categories',5,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(2,'Müşteri Hizmetleri','menu',10,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(3,'Teslimat & Ödeme','menu',20,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(4,'Sözleşmeler','menu',30,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(5,'Şirket','menu',40,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(6,'Banka Bilgileri','bank_info',50,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `footer_menu_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_menu_items`
--

DROP TABLE IF EXISTS `footer_menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `footer_menu_group_id` bigint unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `open_in_new_tab` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_menu_items_footer_menu_group_id_foreign` (`footer_menu_group_id`),
  CONSTRAINT `footer_menu_items_footer_menu_group_id_foreign` FOREIGN KEY (`footer_menu_group_id`) REFERENCES `footer_menu_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_menu_items`
--

LOCK TABLES `footer_menu_items` WRITE;
/*!40000 ALTER TABLE `footer_menu_items` DISABLE KEYS */;
INSERT INTO `footer_menu_items` VALUES (1,2,'Tüm Ürünler','http://localhost:8010',0,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(2,2,'Sepet','http://localhost:8010/sepet',0,2,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(3,2,'Hesabım','http://localhost:8010/panel',0,3,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(4,3,'Delivery information & costs','#',0,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(5,3,'Paperless billing','#',0,2,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(6,3,'Returns','#',0,3,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(7,3,'Ralawise worldwide distribution','#',0,4,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(8,4,'Kullanım Koşulları','#',0,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(9,4,'Gizlilik Politikası','#',0,2,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(10,4,'Mesafeli Satış Sözleşmesi','#',0,3,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(11,4,'Çerez Politikası','#',0,4,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(12,5,'İletişim','http://localhost:8010#iletisim',0,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(13,5,'Gizlilik','#',0,2,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(14,5,'Kullanım Koşulları','#',0,3,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `footer_menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_settings`
--

DROP TABLE IF EXISTS `footer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `footer_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `columns` tinyint unsigned NOT NULL DEFAULT '4' COMMENT 'Footer sütun sayısı',
  `show_brand` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_settings`
--

LOCK TABLES `footer_settings` WRITE;
/*!40000 ALTER TABLE `footer_settings` DISABLE KEYS */;
INSERT INTO `footer_settings` VALUES (1,4,1,'2026-03-09 19:31:57','2026-03-09 19:31:57');
/*!40000 ALTER TABLE `footer_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `home_sections`
--

DROP TABLE IF EXISTS `home_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `home_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Arka plan / görsel',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Üst etiket, örn: Kampanya',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ana başlık, örn: Üst Giyim',
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Alt metin, örn: Tişört, gömlek...',
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Buton metni, örn: İncele',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tıklanınca gidilecek URL',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `home_sections`
--

LOCK TABLES `home_sections` WRITE;
/*!40000 ALTER TABLE `home_sections` DISABLE KEYS */;
INSERT INTO `home_sections` VALUES (1,NULL,'Kampanya','Üst Giyim','Tişört, gömlek ve sweatshirt modelleri','İncele','/?category=tisort',0,1,'2026-03-09 19:33:56','2026-03-09 19:33:56'),(2,NULL,'Koleksiyon','Alt Giyim','Pantolon, şort ve etek','İncele','/?category=pantolon',1,1,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `home_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interface_color_variations`
--

DROP TABLE IF EXISTS `interface_color_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interface_color_variations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `interface_fabric_type_variation_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interface_color_variations`
--

LOCK TABLES `interface_color_variations` WRITE;
/*!40000 ALTER TABLE `interface_color_variations` DISABLE KEYS */;
INSERT INTO `interface_color_variations` VALUES (29,1,'Beyaz','interface_color_variations/hex-ffffff.png',100,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(30,2,'Kırık Beyaz','interface_color_variations/hex-f2efe6.png',101,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(31,1,'Açık Gri','interface_color_variations/hex-f2f2f2.png',102,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(32,5,'Krema','interface_color_variations/hex-f3ead3.png',103,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(33,1,'Vanilya','interface_color_variations/hex-f5e6c8.png',104,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(34,2,'Kum','interface_color_variations/hex-f6c9b2.png',105,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(35,7,'Bej','interface_color_variations/hex-e6d7b8.png',106,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(36,7,'Kahverengi','interface_color_variations/hex-6b3e26.png',107,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(37,5,'Açık Kahve','interface_color_variations/hex-cbb89c.png',108,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(38,1,'Sarı','interface_color_variations/hex-ffb800.png',109,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(39,4,'Bronz','interface_color_variations/hex-d08b45.png',110,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(40,7,'Bebek Pembesi','interface_color_variations/hex-ffc1e3.png',111,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(41,6,'Tarçın','interface_color_variations/hex-a56b3f.png',112,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(42,7,'Pembe','interface_color_variations/hex-ff69b4.png',113,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(43,7,'Koyu Kahve','interface_color_variations/hex-7a4f2e.png',114,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(44,5,'Kırmızı','interface_color_variations/hex-e60026.png',115,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(45,8,'Espresso','interface_color_variations/hex-4a2e1e.png',116,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(46,5,'Turuncu','interface_color_variations/hex-ff6a00.png',117,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(47,5,'Zeytin Yeşili','interface_color_variations/hex-6b8e23.png',118,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(48,8,'Mor','interface_color_variations/hex-6a3d9a.png',119,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(49,4,'Açık Yeşil','interface_color_variations/hex-b2cda0.png',120,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(50,1,'Orman Yeşili','interface_color_variations/hex-1b5e20.png',121,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(51,5,'Nane Yeşili','interface_color_variations/hex-a8e6cf.png',122,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(52,3,'Teal','interface_color_variations/hex-008080.png',123,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(53,4,'Turkuaz','interface_color_variations/hex-8fd6d1.png',124,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(54,7,'Koyu Yeşil','interface_color_variations/hex-1f3b2d.png',125,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(55,8,'Açık Mavi','interface_color_variations/hex-add8e6.png',126,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(56,5,'Gök Mavisi','interface_color_variations/hex-87ceeb.png',127,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(57,7,'Koyu Mavi','interface_color_variations/hex-0d47a1.png',128,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(58,1,'Kraliyet Mavisi','interface_color_variations/hex-0033a0.png',129,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(59,8,'Lacivert','interface_color_variations/hex-0d1b3d.png',130,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(60,8,'Gece Mavisi','interface_color_variations/hex-001f3f.png',131,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(61,4,'Petrol Mavisi','interface_color_variations/hex-1c3d4d.png',132,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(62,6,'Gece Laciverti','interface_color_variations/hex-001a33.png',133,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(63,5,'Çelik Mavisi','interface_color_variations/hex-3c5a6e.png',134,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(64,1,'Doğal','interface_color_variations/hex-eadbc8.png',135,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(65,5,'Pastel Pembe','interface_color_variations/hex-ffd1dc.png',136,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(66,7,'Gri','interface_color_variations/hex-b0b0b0.png',137,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(67,8,'Açık Pembe','interface_color_variations/hex-ffc0cb.png',138,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(68,5,'Orta Gri','interface_color_variations/hex-808080.png',139,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(69,5,'Canlı Pembe','interface_color_variations/hex-ff77b4.png',140,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(70,2,'Koyu Gri','interface_color_variations/hex-4a4a4a.png',141,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(71,8,'Fuşya','interface_color_variations/hex-ff1493.png',142,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(72,4,'Siyah','interface_color_variations/hex-000000.png',143,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(73,6,'Lavanta','interface_color_variations/hex-e6e6fa.png',144,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(74,7,'Eflatun','interface_color_variations/hex-800080.png',145,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(75,2,'İndigo','interface_color_variations/hex-4b0082.png',146,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(76,6,'Bordo','interface_color_variations/hex-722f37.png',147,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(77,4,'Parlak Kırmızı','interface_color_variations/hex-ff0000.png',148,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(78,4,'Altın Sarı','interface_color_variations/hex-f8c000.png',149,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(79,3,'Altın','interface_color_variations/hex-d4a017.png',150,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(80,5,'Sarı Altın','interface_color_variations/hex-ffd700.png',151,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(81,5,'Haki','interface_color_variations/hex-c3b091.png',152,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(82,3,'Zeytin','interface_color_variations/hex-808000.png',153,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(83,1,'Koyu Zeytin','interface_color_variations/hex-556b2f.png',154,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(84,5,'Zümrüt Yeşili','interface_color_variations/hex-009b77.png',155,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(85,7,'Gümüş Gri','interface_color_variations/hex-d3d3d3.png',156,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(86,4,'Kül Rengi','interface_color_variations/hex-a9a9a9.png',157,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(87,8,'Kömür Gri','interface_color_variations/hex-36454f.png',158,1,'2026-05-15 08:51:54','2026-05-15 08:58:15'),(88,2,'Deniz Laciverti','interface_color_variations/hex-000080.png',159,1,'2026-05-15 08:51:54','2026-05-15 08:58:15');
/*!40000 ALTER TABLE `interface_color_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interface_fabric_type_variations`
--

DROP TABLE IF EXISTS `interface_fabric_type_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interface_fabric_type_variations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interface_fabric_type_variations`
--

LOCK TABLES `interface_fabric_type_variations` WRITE;
/*!40000 ALTER TABLE `interface_fabric_type_variations` DISABLE KEYS */;
INSERT INTO `interface_fabric_type_variations` VALUES (1,'Denim','interface_fabric_type_variations/01KR28V50PYJKPA5FSVF83F4BN.png',0,1,'2026-05-07 22:27:27','2026-05-07 22:27:27'),(2,'30/1 Compact Penye Süprem — 155-160 gr/m²',NULL,10,1,'2026-05-09 11:09:51','2026-05-09 11:09:51'),(3,'16/1 Compact Penye Süprem — 230 gr/m²',NULL,20,1,'2026-05-09 11:09:51','2026-05-09 11:09:51'),(4,'24/1 Compact Penye Süprem — 175-185 gr/m²',NULL,30,1,'2026-05-09 11:09:51','2026-05-09 11:09:51'),(5,'18/1 Compact Penye Süprem — 200-210 gr/m²',NULL,40,1,'2026-05-09 11:09:51','2026-05-09 11:09:51'),(6,'30/2 Compact Penye Süprem — 190-195 gr/m²',NULL,50,1,'2026-05-09 11:09:51','2026-05-09 11:09:51'),(7,'36/1 Compact Penye Full Lyc Süprem — 160-165 gr/m²',NULL,60,1,'2026-05-09 11:09:51','2026-05-09 11:09:51'),(8,'28/1 Compact Penye Lyc Süprem — 200-210 gr/m²',NULL,70,1,'2026-05-09 11:09:51','2026-05-09 11:09:51');
/*!40000 ALTER TABLE `interface_fabric_type_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_20_180622_create_companies_table',1),(5,'2026_01_28_000001_add_columns_to_companies_table',1),(6,'2026_01_28_000002_add_b2b_fields_to_users_table',1),(7,'2026_01_28_100000_create_products_table',1),(8,'2026_01_28_120000_create_orders_table',1),(9,'2026_01_28_130000_add_product_variations_and_order_item_variation_data',1),(10,'2026_01_28_150000_add_dependent_variations_to_product_variations',1),(11,'2026_01_28_160000_create_categories_table',1),(12,'2026_01_28_160001_add_category_id_to_products_table',1),(13,'2026_01_28_170000_create_currencies_table',1),(14,'2026_01_28_170001_add_currency_id_to_products_table',1),(15,'2026_01_28_170002_add_exchange_rate_to_currencies_table',1),(16,'2026_01_29_000001_create_dealer_requests_table',1),(17,'2026_01_29_000002_create_product_variation_option_prices_table',1),(18,'2026_01_29_000003_add_variation_price_fields_to_order_items_table',1),(19,'2026_01_29_000004_add_profit_margin_to_companies_table',1),(20,'2026_01_29_100000_create_banner_slides_table',1),(21,'2026_01_29_100001_create_home_sections_table',1),(22,'2026_01_29_200000_add_option_meta_to_product_variations',1),(23,'2026_01_29_300000_add_stock_quantity_to_products_table',1),(24,'2026_01_29_300001_add_stock_quantity_to_product_variation_option_prices_table',1),(25,'2026_01_29_400000_add_meta_tags_to_products_table',1),(26,'2026_01_29_500000_create_customer_groups_table',1),(27,'2026_01_29_500001_create_product_discounts_table',1),(28,'2026_01_29_600000_add_image_to_product_variations',1),(29,'2026_01_29_700000_make_products_stock_quantity_nullable',1),(30,'2026_01_29_800000_create_shipping_methods_table',1),(31,'2026_01_29_800001_add_shipping_to_orders_table',1),(32,'2026_01_29_900000_add_minimum_order_quantity_to_products_table',1),(33,'2026_01_29_900001_seed_minimum_order_quantity_examples',1),(34,'2026_01_29_950000_add_status_to_products_table',1),(35,'2026_01_29_950001_seed_product_status_examples',1),(36,'2026_01_29_960000_create_footer_menu_groups_table',1),(37,'2026_01_29_960001_create_footer_menu_items_table',1),(38,'2026_01_29_970000_create_footer_settings_table',1),(39,'2026_01_29_970001_create_bank_accounts_table',1),(40,'2026_01_29_970002_add_type_to_footer_menu_groups_table',1),(41,'2026_01_29_980000_create_tax_classes_table',1),(42,'2026_01_29_980001_create_tax_rates_table',1),(43,'2026_01_29_980002_add_tax_class_id_to_products_table',1),(44,'2026_01_29_990000_add_bank_account_id_to_orders_table',1),(45,'2026_01_29_990100_add_visible_currency_ids_to_users_table',1),(46,'2026_03_09_100000_ensure_sessions_table_exists',1),(47,'2026_03_10_120000_remove_product_variations_system',2),(48,'2026_03_11_100000_create_product_variations_and_options_tables',3),(49,'2026_03_11_150000_add_parent_option_ids_to_product_variation_options',4),(50,'2026_03_12_100000_create_product_images_table',5),(51,'2026_03_12_120000_add_size_table_trigger_columns_to_products',6),(52,'2026_03_12_140000_add_option_image_size_to_product_variation_options',6),(53,'2026_03_12_160000_simplify_size_table_trigger_to_single_variation',7),(54,'2026_03_13_100000_create_size_tables',8),(55,'2026_03_13_110000_remove_size_table_trigger_from_products',8),(56,'2026_03_14_100000_add_size_table_trigger_variation_to_products',9),(57,'2026_04_21_120000_add_image_path_to_categories_table',10),(58,'2026_04_21_140000_add_home_showcase_to_products_table',11),(59,'2026_04_21_160000_add_price_multiplier_to_size_table_columns',12),(60,'2026_05_06_000001_add_top_menu_fields_to_categories_table',13),(61,'2026_05_07_000001_expand_dealer_requests_for_application_form',14),(62,'2026_05_07_000001_add_home_showcase_image_to_products_table',15),(63,'2026_05_07_180000_create_interface_color_variations_table',16),(64,'2026_05_07_190000_add_interface_color_variation_id_to_product_variation_options',17),(65,'2026_05_07_211000_seed_r_basic_interface_color_variations',18),(66,'2026_05_08_120000_create_interface_fabric_type_variations_table',19),(67,'2026_05_08_130000_add_interface_fabric_type_variation_id_to_product_variation_options',20),(68,'2026_05_07_210000_add_replace_main_gallery_image_to_product_variations',21),(69,'2026_05_09_120000_add_allows_multiple_to_product_variations',22),(70,'2026_05_09_140000_add_solo_selection_to_product_variation_options',23),(71,'2026_05_09_150000_move_solo_option_to_product_variations',24),(72,'2026_05_09_160000_seed_interface_fabric_type_variations_compact_penye',25),(73,'2026_05_09_200000_add_interface_fabric_type_variation_id_to_interface_color_variations',26),(74,'2026_05_15_120000_seed_unassigned_interface_color_variations',27),(75,'2026_05_15_130000_remove_legacy_interface_color_variations',28),(76,'2026_05_15_140000_rename_unassigned_interface_color_variations',29),(80,'2026_05_15_150000_randomly_assign_interface_color_variations_to_fabric_types',30);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `subtotal` decimal(12,2) NOT NULL,
  `variation_data` json DEFAULT NULL,
  `variation_price_delta_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `variation_price_breakdown` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'havale',
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `shipping_method_id` bigint unsigned DEFAULT NULL,
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'TL cinsinden kargo tutarı',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_shipping_method_id_foreign` (`shipping_method_id`),
  KEY `orders_bank_account_id_foreign` (`bank_account_id`),
  CONSTRAINT `orders_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_method_id_foreign` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_discounts`
--

DROP TABLE IF EXISTS `product_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `customer_group_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1' COMMENT 'Minimum adet',
  `priority` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'Öncelik (küçük = önce)',
  `price` decimal(12,4) NOT NULL COMMENT 'İndirimli birim fiyat',
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_discounts_product_id_foreign` (`product_id`),
  KEY `product_discounts_customer_group_id_foreign` (`customer_group_id`),
  CONSTRAINT `product_discounts_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_discounts`
--

LOCK TABLES `product_discounts` WRITE;
/*!40000 ALTER TABLE `product_discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (6,6,'products/01KR1PJ73NSYHW9J94ZGPS0FEM.png',0,'2026-05-07 17:08:00','2026-05-07 17:08:00'),(7,6,'products/01KR1PJ73SYNQEE8B01FW90154.png',0,'2026-05-07 17:08:00','2026-05-07 17:08:00'),(8,6,'products/01KR1PJ73WGGW9J1X74P47WQY8.png',0,'2026-05-07 17:08:00','2026-05-07 17:08:00'),(9,9,'products/01KR1QGRMAVSEW5SBZP4T3WQAZ.png',0,'2026-05-07 17:24:41','2026-05-07 17:24:41'),(10,9,'products/01KR1QGRMPE65KG99TYPNWXKJC.png',0,'2026-05-07 17:24:41','2026-05-07 17:24:41'),(11,9,'products/01KR1QGRN3CA6QE68F38B3RJDW.png',0,'2026-05-07 17:24:41','2026-05-07 17:24:41'),(12,23,'products/01KR1QN7KKNSCA8RQS8CE4VVFC.png',0,'2026-05-07 17:27:08','2026-05-07 17:27:08'),(13,23,'products/01KR1QN7KRAMJPR843DQ83Q5EK.png',0,'2026-05-07 17:27:08','2026-05-07 17:27:08'),(14,7,'products/01KR1QS7ECHB1AWPXGMH0X4YAB.png',0,'2026-05-07 17:29:19','2026-05-07 17:29:19'),(15,7,'products/01KR1QS7ENQB65SN5CNYN4V1QX.png',0,'2026-05-07 17:29:19','2026-05-07 17:29:19'),(16,7,'products/01KR1QS7EXDGMDMZAM7R6H4Y7B.png',0,'2026-05-07 17:29:19','2026-05-07 17:29:19');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variation_options`
--

DROP TABLE IF EXISTS `product_variation_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variation_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_variation_id` bigint unsigned NOT NULL,
  `option_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interface_color_variation_id` bigint unsigned DEFAULT NULL,
  `interface_fabric_type_variation_id` bigint unsigned DEFAULT NULL,
  `option_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_image_size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `price_delta` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int unsigned DEFAULT NULL,
  `parent_option_id` bigint unsigned DEFAULT NULL,
  `parent_option_ids` json DEFAULT NULL COMMENT 'Birden fazla üst seçenek id (array)',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_variation_options_product_variation_id_foreign` (`product_variation_id`),
  KEY `product_variation_options_parent_option_id_foreign` (`parent_option_id`),
  KEY `product_variation_options_interface_color_variation_id_foreign` (`interface_color_variation_id`),
  CONSTRAINT `product_variation_options_interface_color_variation_id_foreign` FOREIGN KEY (`interface_color_variation_id`) REFERENCES `interface_color_variations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_variation_options_parent_option_id_foreign` FOREIGN KEY (`parent_option_id`) REFERENCES `product_variation_options` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_variation_options_product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=593 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variation_options`
--

LOCK TABLES `product_variation_options` WRITE;
/*!40000 ALTER TABLE `product_variation_options` DISABLE KEYS */;
INSERT INTO `product_variation_options` VALUES (8,4,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[\"11\", \"9\", \"7\", \"5\", \"10\", \"6\"]',0,'2026-03-10 22:27:45','2026-04-21 20:29:02'),(12,4,'OVERSIZE',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[\"11\", \"9\", \"7\", \"5\", \"10\", \"6\"]',0,'2026-03-10 22:29:40','2026-04-21 20:29:02'),(13,4,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[\"11\", \"9\", \"7\", \"5\", \"10\", \"6\"]',0,'2026-03-10 22:29:40','2026-04-21 20:29:02'),(14,5,'Kadın',NULL,NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:38:58','2026-05-09 11:01:18'),(15,5,'Çoçuk',NULL,NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:38:58','2026-05-09 11:01:18'),(16,5,'Erkek',NULL,NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:38:58','2026-05-09 11:01:18'),(30,8,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',2.00,NULL,NULL,'[]',0,'2026-03-12 20:23:08','2026-05-08 22:49:34'),(31,8,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',3.97,NULL,NULL,'[]',0,'2026-03-12 20:23:08','2026-05-08 22:49:34'),(32,8,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',6.00,NULL,NULL,'[]',0,'2026-03-12 20:23:08','2026-05-08 22:49:34'),(33,9,'O Yaka',NULL,NULL,NULL,'variation_options/01KR2BV7KDSQQBJVWQTSJGQEFM.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 17:54:23','2026-05-07 23:19:56'),(34,9,'V Yaka',NULL,NULL,NULL,'variation_options/01KR2BV7KRPTF9N66997GNEZAM.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 17:54:23','2026-05-07 23:19:56'),(35,10,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(36,10,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(37,10,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(38,11,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(39,11,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(40,11,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(41,12,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(42,12,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(43,12,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(44,12,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(45,12,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(54,14,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(55,14,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(56,14,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(57,15,'Ribana Yaka',NULL,NULL,NULL,'variation_options/01KKA51NYMZ3F33K0HPWS8DNSB.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:03:29'),(58,15,'Gömlek Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:03:29'),(59,16,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(60,16,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(61,16,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(62,17,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(63,17,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(64,17,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(65,18,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(66,18,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(67,18,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(68,18,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(69,18,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(78,20,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(79,20,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(80,20,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(81,21,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(82,21,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(83,22,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(84,22,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(85,22,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(86,23,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(87,23,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(88,23,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(89,24,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(90,24,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(91,24,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(92,24,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(93,24,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(102,26,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(103,26,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(104,26,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(105,27,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(106,27,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(107,28,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(108,28,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(109,28,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(110,29,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(111,29,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(112,29,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(113,30,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(114,30,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(115,30,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(116,30,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(117,30,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(126,32,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(127,32,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(128,32,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(129,33,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(130,33,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(131,34,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(132,34,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(133,34,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(134,35,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(135,35,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(136,35,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(137,36,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(138,36,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(139,36,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(140,36,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(141,36,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(150,38,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(151,38,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(152,38,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(153,39,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(154,39,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(158,8,'İstemiyorum',NULL,NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-05-08 23:16:05','2026-05-08 23:16:05'),(169,41,'Denim',NULL,NULL,1,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:51'),(170,41,'30/1 Compact Penye Süprem — 155-160 gr/m²',NULL,NULL,2,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(171,41,'16/1 Compact Penye Süprem — 230 gr/m²',NULL,NULL,3,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(172,41,'24/1 Compact Penye Süprem — 175-185 gr/m²',NULL,NULL,4,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(173,41,'18/1 Compact Penye Süprem — 200-210 gr/m²',NULL,NULL,5,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(174,41,'30/2 Compact Penye Süprem — 190-195 gr/m²',NULL,NULL,6,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(175,41,'36/1 Compact Penye Full Lyc Süprem — 160-165 gr/m²',NULL,NULL,7,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(176,41,'28/1 Compact Penye Lyc Süprem — 200-210 gr/m²',NULL,NULL,8,NULL,NULL,0.00,NULL,NULL,'[]',0,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(232,5,'Unisex',NULL,NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-05-09 12:06:35','2026-05-09 12:06:35'),(233,43,'Beyaz',NULL,29,NULL,'interface_color_variations/hex-ffffff.png','medium',0.00,NULL,NULL,'[]',100,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(234,43,'Açık Gri',NULL,31,NULL,'interface_color_variations/hex-f2f2f2.png','medium',0.00,NULL,NULL,'[]',102,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(235,43,'Vanilya',NULL,33,NULL,'interface_color_variations/hex-f5e6c8.png','medium',0.00,NULL,NULL,'[]',104,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(236,43,'Sarı',NULL,38,NULL,'interface_color_variations/hex-ffb800.png','medium',0.00,NULL,NULL,'[]',109,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(237,43,'Orman Yeşili',NULL,50,NULL,'interface_color_variations/hex-1b5e20.png','medium',0.00,NULL,NULL,'[]',121,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(238,43,'Kraliyet Mavisi',NULL,58,NULL,'interface_color_variations/hex-0033a0.png','medium',0.00,NULL,NULL,'[]',129,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(239,43,'Doğal',NULL,64,NULL,'interface_color_variations/hex-eadbc8.png','medium',0.00,NULL,NULL,'[]',135,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(240,43,'Koyu Zeytin',NULL,83,NULL,'interface_color_variations/hex-556b2f.png','medium',0.00,NULL,NULL,'[]',154,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(241,43,'Kırık Beyaz',NULL,30,NULL,'interface_color_variations/hex-f2efe6.png','medium',0.00,NULL,NULL,'[]',101,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(242,43,'Kum',NULL,34,NULL,'interface_color_variations/hex-f6c9b2.png','medium',0.00,NULL,NULL,'[]',105,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(243,43,'Koyu Gri',NULL,70,NULL,'interface_color_variations/hex-4a4a4a.png','medium',0.00,NULL,NULL,'[]',141,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(244,43,'İndigo',NULL,75,NULL,'interface_color_variations/hex-4b0082.png','medium',0.00,NULL,NULL,'[]',146,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(245,43,'Deniz Laciverti',NULL,88,NULL,'interface_color_variations/hex-000080.png','medium',0.00,NULL,NULL,'[]',159,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(246,43,'Teal',NULL,52,NULL,'interface_color_variations/hex-008080.png','medium',0.00,NULL,NULL,'[]',123,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(247,43,'Altın',NULL,79,NULL,'interface_color_variations/hex-d4a017.png','medium',0.00,NULL,NULL,'[]',150,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(248,43,'Zeytin',NULL,82,NULL,'interface_color_variations/hex-808000.png','medium',0.00,NULL,NULL,'[]',153,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(249,43,'Bronz',NULL,39,NULL,'interface_color_variations/hex-d08b45.png','medium',0.00,NULL,NULL,'[]',110,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(250,43,'Açık Yeşil',NULL,49,NULL,'interface_color_variations/hex-b2cda0.png','medium',0.00,NULL,NULL,'[]',120,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(251,43,'Turkuaz',NULL,53,NULL,'interface_color_variations/hex-8fd6d1.png','medium',0.00,NULL,NULL,'[]',124,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(252,43,'Petrol Mavisi',NULL,61,NULL,'interface_color_variations/hex-1c3d4d.png','medium',0.00,NULL,NULL,'[]',132,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(253,43,'Siyah',NULL,72,NULL,'interface_color_variations/hex-000000.png','medium',0.00,NULL,NULL,'[]',143,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(254,43,'Parlak Kırmızı',NULL,77,NULL,'interface_color_variations/hex-ff0000.png','medium',0.00,NULL,NULL,'[]',148,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(255,43,'Altın Sarı',NULL,78,NULL,'interface_color_variations/hex-f8c000.png','medium',0.00,NULL,NULL,'[]',149,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(256,43,'Kül Rengi',NULL,86,NULL,'interface_color_variations/hex-a9a9a9.png','medium',0.00,NULL,NULL,'[]',157,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(257,43,'Krema',NULL,32,NULL,'interface_color_variations/hex-f3ead3.png','medium',0.00,NULL,NULL,'[]',103,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(258,43,'Açık Kahve',NULL,37,NULL,'interface_color_variations/hex-cbb89c.png','medium',0.00,NULL,NULL,'[]',108,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(259,43,'Kırmızı',NULL,44,NULL,'interface_color_variations/hex-e60026.png','medium',0.00,NULL,NULL,'[]',115,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(260,43,'Turuncu',NULL,46,NULL,'interface_color_variations/hex-ff6a00.png','medium',0.00,NULL,NULL,'[]',117,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(261,43,'Zeytin Yeşili',NULL,47,NULL,'interface_color_variations/hex-6b8e23.png','medium',0.00,NULL,NULL,'[]',118,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(262,43,'Nane Yeşili',NULL,51,NULL,'interface_color_variations/hex-a8e6cf.png','medium',0.00,NULL,NULL,'[]',122,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(263,43,'Gök Mavisi',NULL,56,NULL,'interface_color_variations/hex-87ceeb.png','medium',0.00,NULL,NULL,'[]',127,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(264,43,'Çelik Mavisi',NULL,63,NULL,'interface_color_variations/hex-3c5a6e.png','medium',0.00,NULL,NULL,'[]',134,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(265,43,'Pastel Pembe',NULL,65,NULL,'interface_color_variations/hex-ffd1dc.png','medium',0.00,NULL,NULL,'[]',136,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(266,43,'Orta Gri',NULL,68,NULL,'interface_color_variations/hex-808080.png','medium',0.00,NULL,NULL,'[]',139,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(267,43,'Canlı Pembe',NULL,69,NULL,'interface_color_variations/hex-ff77b4.png','medium',0.00,NULL,NULL,'[]',140,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(268,43,'Sarı Altın',NULL,80,NULL,'interface_color_variations/hex-ffd700.png','medium',0.00,NULL,NULL,'[]',151,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(269,43,'Haki',NULL,81,NULL,'interface_color_variations/hex-c3b091.png','medium',0.00,NULL,NULL,'[]',152,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(270,43,'Zümrüt Yeşili',NULL,84,NULL,'interface_color_variations/hex-009b77.png','medium',0.00,NULL,NULL,'[]',155,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(271,43,'Tarçın',NULL,41,NULL,'interface_color_variations/hex-a56b3f.png','medium',0.00,NULL,NULL,'[]',112,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(272,43,'Gece Laciverti',NULL,62,NULL,'interface_color_variations/hex-001a33.png','medium',0.00,NULL,NULL,'[]',133,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(273,43,'Lavanta',NULL,73,NULL,'interface_color_variations/hex-e6e6fa.png','medium',0.00,NULL,NULL,'[]',144,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(274,43,'Bordo',NULL,76,NULL,'interface_color_variations/hex-722f37.png','medium',0.00,NULL,NULL,'[]',147,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(275,43,'Bej',NULL,35,NULL,'interface_color_variations/hex-e6d7b8.png','medium',0.00,NULL,NULL,'[]',106,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(276,43,'Kahverengi',NULL,36,NULL,'interface_color_variations/hex-6b3e26.png','medium',0.00,NULL,NULL,'[]',107,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(277,43,'Bebek Pembesi',NULL,40,NULL,'interface_color_variations/hex-ffc1e3.png','medium',0.00,NULL,NULL,'[]',111,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(278,43,'Pembe',NULL,42,NULL,'interface_color_variations/hex-ff69b4.png','medium',0.00,NULL,NULL,'[]',113,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(279,43,'Koyu Kahve',NULL,43,NULL,'interface_color_variations/hex-7a4f2e.png','medium',0.00,NULL,NULL,'[]',114,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(280,43,'Koyu Yeşil',NULL,54,NULL,'interface_color_variations/hex-1f3b2d.png','medium',0.00,NULL,NULL,'[]',125,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(281,43,'Koyu Mavi',NULL,57,NULL,'interface_color_variations/hex-0d47a1.png','medium',0.00,NULL,NULL,'[]',128,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(282,43,'Gri',NULL,66,NULL,'interface_color_variations/hex-b0b0b0.png','medium',0.00,NULL,NULL,'[]',137,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(283,43,'Eflatun',NULL,74,NULL,'interface_color_variations/hex-800080.png','medium',0.00,NULL,NULL,'[]',145,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(284,43,'Gümüş Gri',NULL,85,NULL,'interface_color_variations/hex-d3d3d3.png','medium',0.00,NULL,NULL,'[]',156,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(285,43,'Espresso',NULL,45,NULL,'interface_color_variations/hex-4a2e1e.png','medium',0.00,NULL,NULL,'[]',116,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(286,43,'Mor',NULL,48,NULL,'interface_color_variations/hex-6a3d9a.png','medium',0.00,NULL,NULL,'[]',119,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(287,43,'Açık Mavi',NULL,55,NULL,'interface_color_variations/hex-add8e6.png','medium',0.00,NULL,NULL,'[]',126,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(288,43,'Lacivert',NULL,59,NULL,'interface_color_variations/hex-0d1b3d.png','medium',0.00,NULL,NULL,'[]',130,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(289,43,'Gece Mavisi',NULL,60,NULL,'interface_color_variations/hex-001f3f.png','medium',0.00,NULL,NULL,'[]',131,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(290,43,'Açık Pembe',NULL,67,NULL,'interface_color_variations/hex-ffc0cb.png','medium',0.00,NULL,NULL,'[]',138,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(291,43,'Fuşya',NULL,71,NULL,'interface_color_variations/hex-ff1493.png','medium',0.00,NULL,NULL,'[]',142,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(292,43,'Kömür Gri',NULL,87,NULL,'interface_color_variations/hex-36454f.png','medium',0.00,NULL,NULL,'[]',158,'2026-05-15 10:53:51','2026-05-15 10:54:14'),(293,13,'Beyaz',NULL,29,NULL,'interface_color_variations/hex-ffffff.png','medium',0.00,NULL,NULL,NULL,100,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(294,13,'Açık Gri',NULL,31,NULL,'interface_color_variations/hex-f2f2f2.png','medium',0.00,NULL,NULL,NULL,102,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(295,13,'Vanilya',NULL,33,NULL,'interface_color_variations/hex-f5e6c8.png','medium',0.00,NULL,NULL,NULL,104,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(296,13,'Sarı',NULL,38,NULL,'interface_color_variations/hex-ffb800.png','medium',0.00,NULL,NULL,NULL,109,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(297,13,'Orman Yeşili',NULL,50,NULL,'interface_color_variations/hex-1b5e20.png','medium',0.00,NULL,NULL,NULL,121,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(298,13,'Kraliyet Mavisi',NULL,58,NULL,'interface_color_variations/hex-0033a0.png','medium',0.00,NULL,NULL,NULL,129,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(299,13,'Doğal',NULL,64,NULL,'interface_color_variations/hex-eadbc8.png','medium',0.00,NULL,NULL,NULL,135,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(300,13,'Koyu Zeytin',NULL,83,NULL,'interface_color_variations/hex-556b2f.png','medium',0.00,NULL,NULL,NULL,154,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(301,13,'Kırık Beyaz',NULL,30,NULL,'interface_color_variations/hex-f2efe6.png','medium',0.00,NULL,NULL,NULL,101,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(302,13,'Kum',NULL,34,NULL,'interface_color_variations/hex-f6c9b2.png','medium',0.00,NULL,NULL,NULL,105,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(303,13,'Koyu Gri',NULL,70,NULL,'interface_color_variations/hex-4a4a4a.png','medium',0.00,NULL,NULL,NULL,141,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(304,13,'İndigo',NULL,75,NULL,'interface_color_variations/hex-4b0082.png','medium',0.00,NULL,NULL,NULL,146,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(305,13,'Deniz Laciverti',NULL,88,NULL,'interface_color_variations/hex-000080.png','medium',0.00,NULL,NULL,NULL,159,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(306,13,'Teal',NULL,52,NULL,'interface_color_variations/hex-008080.png','medium',0.00,NULL,NULL,NULL,123,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(307,13,'Altın',NULL,79,NULL,'interface_color_variations/hex-d4a017.png','medium',0.00,NULL,NULL,NULL,150,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(308,13,'Zeytin',NULL,82,NULL,'interface_color_variations/hex-808000.png','medium',0.00,NULL,NULL,NULL,153,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(309,13,'Bronz',NULL,39,NULL,'interface_color_variations/hex-d08b45.png','medium',0.00,NULL,NULL,NULL,110,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(310,13,'Açık Yeşil',NULL,49,NULL,'interface_color_variations/hex-b2cda0.png','medium',0.00,NULL,NULL,NULL,120,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(311,13,'Turkuaz',NULL,53,NULL,'interface_color_variations/hex-8fd6d1.png','medium',0.00,NULL,NULL,NULL,124,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(312,13,'Petrol Mavisi',NULL,61,NULL,'interface_color_variations/hex-1c3d4d.png','medium',0.00,NULL,NULL,NULL,132,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(313,13,'Siyah',NULL,72,NULL,'interface_color_variations/hex-000000.png','medium',0.00,NULL,NULL,NULL,143,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(314,13,'Parlak Kırmızı',NULL,77,NULL,'interface_color_variations/hex-ff0000.png','medium',0.00,NULL,NULL,NULL,148,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(315,13,'Altın Sarı',NULL,78,NULL,'interface_color_variations/hex-f8c000.png','medium',0.00,NULL,NULL,NULL,149,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(316,13,'Kül Rengi',NULL,86,NULL,'interface_color_variations/hex-a9a9a9.png','medium',0.00,NULL,NULL,NULL,157,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(317,13,'Krema',NULL,32,NULL,'interface_color_variations/hex-f3ead3.png','medium',0.00,NULL,NULL,NULL,103,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(318,13,'Açık Kahve',NULL,37,NULL,'interface_color_variations/hex-cbb89c.png','medium',0.00,NULL,NULL,NULL,108,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(319,13,'Kırmızı',NULL,44,NULL,'interface_color_variations/hex-e60026.png','medium',0.00,NULL,NULL,NULL,115,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(320,13,'Turuncu',NULL,46,NULL,'interface_color_variations/hex-ff6a00.png','medium',0.00,NULL,NULL,NULL,117,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(321,13,'Zeytin Yeşili',NULL,47,NULL,'interface_color_variations/hex-6b8e23.png','medium',0.00,NULL,NULL,NULL,118,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(322,13,'Nane Yeşili',NULL,51,NULL,'interface_color_variations/hex-a8e6cf.png','medium',0.00,NULL,NULL,NULL,122,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(323,13,'Gök Mavisi',NULL,56,NULL,'interface_color_variations/hex-87ceeb.png','medium',0.00,NULL,NULL,NULL,127,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(324,13,'Çelik Mavisi',NULL,63,NULL,'interface_color_variations/hex-3c5a6e.png','medium',0.00,NULL,NULL,NULL,134,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(325,13,'Pastel Pembe',NULL,65,NULL,'interface_color_variations/hex-ffd1dc.png','medium',0.00,NULL,NULL,NULL,136,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(326,13,'Orta Gri',NULL,68,NULL,'interface_color_variations/hex-808080.png','medium',0.00,NULL,NULL,NULL,139,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(327,13,'Canlı Pembe',NULL,69,NULL,'interface_color_variations/hex-ff77b4.png','medium',0.00,NULL,NULL,NULL,140,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(328,13,'Sarı Altın',NULL,80,NULL,'interface_color_variations/hex-ffd700.png','medium',0.00,NULL,NULL,NULL,151,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(329,13,'Haki',NULL,81,NULL,'interface_color_variations/hex-c3b091.png','medium',0.00,NULL,NULL,NULL,152,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(330,13,'Zümrüt Yeşili',NULL,84,NULL,'interface_color_variations/hex-009b77.png','medium',0.00,NULL,NULL,NULL,155,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(331,13,'Tarçın',NULL,41,NULL,'interface_color_variations/hex-a56b3f.png','medium',0.00,NULL,NULL,NULL,112,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(332,13,'Gece Laciverti',NULL,62,NULL,'interface_color_variations/hex-001a33.png','medium',0.00,NULL,NULL,NULL,133,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(333,13,'Lavanta',NULL,73,NULL,'interface_color_variations/hex-e6e6fa.png','medium',0.00,NULL,NULL,NULL,144,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(334,13,'Bordo',NULL,76,NULL,'interface_color_variations/hex-722f37.png','medium',0.00,NULL,NULL,NULL,147,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(335,13,'Bej',NULL,35,NULL,'interface_color_variations/hex-e6d7b8.png','medium',0.00,NULL,NULL,NULL,106,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(336,13,'Kahverengi',NULL,36,NULL,'interface_color_variations/hex-6b3e26.png','medium',0.00,NULL,NULL,NULL,107,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(337,13,'Bebek Pembesi',NULL,40,NULL,'interface_color_variations/hex-ffc1e3.png','medium',0.00,NULL,NULL,NULL,111,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(338,13,'Pembe',NULL,42,NULL,'interface_color_variations/hex-ff69b4.png','medium',0.00,NULL,NULL,NULL,113,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(339,13,'Koyu Kahve',NULL,43,NULL,'interface_color_variations/hex-7a4f2e.png','medium',0.00,NULL,NULL,NULL,114,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(340,13,'Koyu Yeşil',NULL,54,NULL,'interface_color_variations/hex-1f3b2d.png','medium',0.00,NULL,NULL,NULL,125,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(341,13,'Koyu Mavi',NULL,57,NULL,'interface_color_variations/hex-0d47a1.png','medium',0.00,NULL,NULL,NULL,128,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(342,13,'Gri',NULL,66,NULL,'interface_color_variations/hex-b0b0b0.png','medium',0.00,NULL,NULL,NULL,137,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(343,13,'Eflatun',NULL,74,NULL,'interface_color_variations/hex-800080.png','medium',0.00,NULL,NULL,NULL,145,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(344,13,'Gümüş Gri',NULL,85,NULL,'interface_color_variations/hex-d3d3d3.png','medium',0.00,NULL,NULL,NULL,156,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(345,13,'Espresso',NULL,45,NULL,'interface_color_variations/hex-4a2e1e.png','medium',0.00,NULL,NULL,NULL,116,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(346,13,'Mor',NULL,48,NULL,'interface_color_variations/hex-6a3d9a.png','medium',0.00,NULL,NULL,NULL,119,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(347,13,'Açık Mavi',NULL,55,NULL,'interface_color_variations/hex-add8e6.png','medium',0.00,NULL,NULL,NULL,126,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(348,13,'Lacivert',NULL,59,NULL,'interface_color_variations/hex-0d1b3d.png','medium',0.00,NULL,NULL,NULL,130,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(349,13,'Gece Mavisi',NULL,60,NULL,'interface_color_variations/hex-001f3f.png','medium',0.00,NULL,NULL,NULL,131,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(350,13,'Açık Pembe',NULL,67,NULL,'interface_color_variations/hex-ffc0cb.png','medium',0.00,NULL,NULL,NULL,138,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(351,13,'Fuşya',NULL,71,NULL,'interface_color_variations/hex-ff1493.png','medium',0.00,NULL,NULL,NULL,142,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(352,13,'Kömür Gri',NULL,87,NULL,'interface_color_variations/hex-36454f.png','medium',0.00,NULL,NULL,NULL,158,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(353,19,'Beyaz',NULL,29,NULL,'interface_color_variations/hex-ffffff.png','medium',0.00,NULL,NULL,NULL,100,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(354,19,'Açık Gri',NULL,31,NULL,'interface_color_variations/hex-f2f2f2.png','medium',0.00,NULL,NULL,NULL,102,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(355,19,'Vanilya',NULL,33,NULL,'interface_color_variations/hex-f5e6c8.png','medium',0.00,NULL,NULL,NULL,104,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(356,19,'Sarı',NULL,38,NULL,'interface_color_variations/hex-ffb800.png','medium',0.00,NULL,NULL,NULL,109,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(357,19,'Orman Yeşili',NULL,50,NULL,'interface_color_variations/hex-1b5e20.png','medium',0.00,NULL,NULL,NULL,121,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(358,19,'Kraliyet Mavisi',NULL,58,NULL,'interface_color_variations/hex-0033a0.png','medium',0.00,NULL,NULL,NULL,129,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(359,19,'Doğal',NULL,64,NULL,'interface_color_variations/hex-eadbc8.png','medium',0.00,NULL,NULL,NULL,135,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(360,19,'Koyu Zeytin',NULL,83,NULL,'interface_color_variations/hex-556b2f.png','medium',0.00,NULL,NULL,NULL,154,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(361,19,'Kırık Beyaz',NULL,30,NULL,'interface_color_variations/hex-f2efe6.png','medium',0.00,NULL,NULL,NULL,101,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(362,19,'Kum',NULL,34,NULL,'interface_color_variations/hex-f6c9b2.png','medium',0.00,NULL,NULL,NULL,105,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(363,19,'Koyu Gri',NULL,70,NULL,'interface_color_variations/hex-4a4a4a.png','medium',0.00,NULL,NULL,NULL,141,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(364,19,'İndigo',NULL,75,NULL,'interface_color_variations/hex-4b0082.png','medium',0.00,NULL,NULL,NULL,146,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(365,19,'Deniz Laciverti',NULL,88,NULL,'interface_color_variations/hex-000080.png','medium',0.00,NULL,NULL,NULL,159,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(366,19,'Teal',NULL,52,NULL,'interface_color_variations/hex-008080.png','medium',0.00,NULL,NULL,NULL,123,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(367,19,'Altın',NULL,79,NULL,'interface_color_variations/hex-d4a017.png','medium',0.00,NULL,NULL,NULL,150,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(368,19,'Zeytin',NULL,82,NULL,'interface_color_variations/hex-808000.png','medium',0.00,NULL,NULL,NULL,153,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(369,19,'Bronz',NULL,39,NULL,'interface_color_variations/hex-d08b45.png','medium',0.00,NULL,NULL,NULL,110,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(370,19,'Açık Yeşil',NULL,49,NULL,'interface_color_variations/hex-b2cda0.png','medium',0.00,NULL,NULL,NULL,120,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(371,19,'Turkuaz',NULL,53,NULL,'interface_color_variations/hex-8fd6d1.png','medium',0.00,NULL,NULL,NULL,124,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(372,19,'Petrol Mavisi',NULL,61,NULL,'interface_color_variations/hex-1c3d4d.png','medium',0.00,NULL,NULL,NULL,132,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(373,19,'Siyah',NULL,72,NULL,'interface_color_variations/hex-000000.png','medium',0.00,NULL,NULL,NULL,143,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(374,19,'Parlak Kırmızı',NULL,77,NULL,'interface_color_variations/hex-ff0000.png','medium',0.00,NULL,NULL,NULL,148,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(375,19,'Altın Sarı',NULL,78,NULL,'interface_color_variations/hex-f8c000.png','medium',0.00,NULL,NULL,NULL,149,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(376,19,'Kül Rengi',NULL,86,NULL,'interface_color_variations/hex-a9a9a9.png','medium',0.00,NULL,NULL,NULL,157,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(377,19,'Krema',NULL,32,NULL,'interface_color_variations/hex-f3ead3.png','medium',0.00,NULL,NULL,NULL,103,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(378,19,'Açık Kahve',NULL,37,NULL,'interface_color_variations/hex-cbb89c.png','medium',0.00,NULL,NULL,NULL,108,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(379,19,'Kırmızı',NULL,44,NULL,'interface_color_variations/hex-e60026.png','medium',0.00,NULL,NULL,NULL,115,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(380,19,'Turuncu',NULL,46,NULL,'interface_color_variations/hex-ff6a00.png','medium',0.00,NULL,NULL,NULL,117,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(381,19,'Zeytin Yeşili',NULL,47,NULL,'interface_color_variations/hex-6b8e23.png','medium',0.00,NULL,NULL,NULL,118,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(382,19,'Nane Yeşili',NULL,51,NULL,'interface_color_variations/hex-a8e6cf.png','medium',0.00,NULL,NULL,NULL,122,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(383,19,'Gök Mavisi',NULL,56,NULL,'interface_color_variations/hex-87ceeb.png','medium',0.00,NULL,NULL,NULL,127,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(384,19,'Çelik Mavisi',NULL,63,NULL,'interface_color_variations/hex-3c5a6e.png','medium',0.00,NULL,NULL,NULL,134,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(385,19,'Pastel Pembe',NULL,65,NULL,'interface_color_variations/hex-ffd1dc.png','medium',0.00,NULL,NULL,NULL,136,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(386,19,'Orta Gri',NULL,68,NULL,'interface_color_variations/hex-808080.png','medium',0.00,NULL,NULL,NULL,139,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(387,19,'Canlı Pembe',NULL,69,NULL,'interface_color_variations/hex-ff77b4.png','medium',0.00,NULL,NULL,NULL,140,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(388,19,'Sarı Altın',NULL,80,NULL,'interface_color_variations/hex-ffd700.png','medium',0.00,NULL,NULL,NULL,151,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(389,19,'Haki',NULL,81,NULL,'interface_color_variations/hex-c3b091.png','medium',0.00,NULL,NULL,NULL,152,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(390,19,'Zümrüt Yeşili',NULL,84,NULL,'interface_color_variations/hex-009b77.png','medium',0.00,NULL,NULL,NULL,155,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(391,19,'Tarçın',NULL,41,NULL,'interface_color_variations/hex-a56b3f.png','medium',0.00,NULL,NULL,NULL,112,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(392,19,'Gece Laciverti',NULL,62,NULL,'interface_color_variations/hex-001a33.png','medium',0.00,NULL,NULL,NULL,133,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(393,19,'Lavanta',NULL,73,NULL,'interface_color_variations/hex-e6e6fa.png','medium',0.00,NULL,NULL,NULL,144,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(394,19,'Bordo',NULL,76,NULL,'interface_color_variations/hex-722f37.png','medium',0.00,NULL,NULL,NULL,147,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(395,19,'Bej',NULL,35,NULL,'interface_color_variations/hex-e6d7b8.png','medium',0.00,NULL,NULL,NULL,106,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(396,19,'Kahverengi',NULL,36,NULL,'interface_color_variations/hex-6b3e26.png','medium',0.00,NULL,NULL,NULL,107,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(397,19,'Bebek Pembesi',NULL,40,NULL,'interface_color_variations/hex-ffc1e3.png','medium',0.00,NULL,NULL,NULL,111,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(398,19,'Pembe',NULL,42,NULL,'interface_color_variations/hex-ff69b4.png','medium',0.00,NULL,NULL,NULL,113,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(399,19,'Koyu Kahve',NULL,43,NULL,'interface_color_variations/hex-7a4f2e.png','medium',0.00,NULL,NULL,NULL,114,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(400,19,'Koyu Yeşil',NULL,54,NULL,'interface_color_variations/hex-1f3b2d.png','medium',0.00,NULL,NULL,NULL,125,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(401,19,'Koyu Mavi',NULL,57,NULL,'interface_color_variations/hex-0d47a1.png','medium',0.00,NULL,NULL,NULL,128,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(402,19,'Gri',NULL,66,NULL,'interface_color_variations/hex-b0b0b0.png','medium',0.00,NULL,NULL,NULL,137,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(403,19,'Eflatun',NULL,74,NULL,'interface_color_variations/hex-800080.png','medium',0.00,NULL,NULL,NULL,145,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(404,19,'Gümüş Gri',NULL,85,NULL,'interface_color_variations/hex-d3d3d3.png','medium',0.00,NULL,NULL,NULL,156,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(405,19,'Espresso',NULL,45,NULL,'interface_color_variations/hex-4a2e1e.png','medium',0.00,NULL,NULL,NULL,116,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(406,19,'Mor',NULL,48,NULL,'interface_color_variations/hex-6a3d9a.png','medium',0.00,NULL,NULL,NULL,119,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(407,19,'Açık Mavi',NULL,55,NULL,'interface_color_variations/hex-add8e6.png','medium',0.00,NULL,NULL,NULL,126,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(408,19,'Lacivert',NULL,59,NULL,'interface_color_variations/hex-0d1b3d.png','medium',0.00,NULL,NULL,NULL,130,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(409,19,'Gece Mavisi',NULL,60,NULL,'interface_color_variations/hex-001f3f.png','medium',0.00,NULL,NULL,NULL,131,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(410,19,'Açık Pembe',NULL,67,NULL,'interface_color_variations/hex-ffc0cb.png','medium',0.00,NULL,NULL,NULL,138,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(411,19,'Fuşya',NULL,71,NULL,'interface_color_variations/hex-ff1493.png','medium',0.00,NULL,NULL,NULL,142,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(412,19,'Kömür Gri',NULL,87,NULL,'interface_color_variations/hex-36454f.png','medium',0.00,NULL,NULL,NULL,158,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(413,25,'Beyaz',NULL,29,NULL,'interface_color_variations/hex-ffffff.png','medium',0.00,NULL,NULL,NULL,100,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(414,25,'Açık Gri',NULL,31,NULL,'interface_color_variations/hex-f2f2f2.png','medium',0.00,NULL,NULL,NULL,102,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(415,25,'Vanilya',NULL,33,NULL,'interface_color_variations/hex-f5e6c8.png','medium',0.00,NULL,NULL,NULL,104,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(416,25,'Sarı',NULL,38,NULL,'interface_color_variations/hex-ffb800.png','medium',0.00,NULL,NULL,NULL,109,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(417,25,'Orman Yeşili',NULL,50,NULL,'interface_color_variations/hex-1b5e20.png','medium',0.00,NULL,NULL,NULL,121,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(418,25,'Kraliyet Mavisi',NULL,58,NULL,'interface_color_variations/hex-0033a0.png','medium',0.00,NULL,NULL,NULL,129,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(419,25,'Doğal',NULL,64,NULL,'interface_color_variations/hex-eadbc8.png','medium',0.00,NULL,NULL,NULL,135,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(420,25,'Koyu Zeytin',NULL,83,NULL,'interface_color_variations/hex-556b2f.png','medium',0.00,NULL,NULL,NULL,154,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(421,25,'Kırık Beyaz',NULL,30,NULL,'interface_color_variations/hex-f2efe6.png','medium',0.00,NULL,NULL,NULL,101,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(422,25,'Kum',NULL,34,NULL,'interface_color_variations/hex-f6c9b2.png','medium',0.00,NULL,NULL,NULL,105,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(423,25,'Koyu Gri',NULL,70,NULL,'interface_color_variations/hex-4a4a4a.png','medium',0.00,NULL,NULL,NULL,141,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(424,25,'İndigo',NULL,75,NULL,'interface_color_variations/hex-4b0082.png','medium',0.00,NULL,NULL,NULL,146,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(425,25,'Deniz Laciverti',NULL,88,NULL,'interface_color_variations/hex-000080.png','medium',0.00,NULL,NULL,NULL,159,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(426,25,'Teal',NULL,52,NULL,'interface_color_variations/hex-008080.png','medium',0.00,NULL,NULL,NULL,123,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(427,25,'Altın',NULL,79,NULL,'interface_color_variations/hex-d4a017.png','medium',0.00,NULL,NULL,NULL,150,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(428,25,'Zeytin',NULL,82,NULL,'interface_color_variations/hex-808000.png','medium',0.00,NULL,NULL,NULL,153,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(429,25,'Bronz',NULL,39,NULL,'interface_color_variations/hex-d08b45.png','medium',0.00,NULL,NULL,NULL,110,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(430,25,'Açık Yeşil',NULL,49,NULL,'interface_color_variations/hex-b2cda0.png','medium',0.00,NULL,NULL,NULL,120,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(431,25,'Turkuaz',NULL,53,NULL,'interface_color_variations/hex-8fd6d1.png','medium',0.00,NULL,NULL,NULL,124,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(432,25,'Petrol Mavisi',NULL,61,NULL,'interface_color_variations/hex-1c3d4d.png','medium',0.00,NULL,NULL,NULL,132,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(433,25,'Siyah',NULL,72,NULL,'interface_color_variations/hex-000000.png','medium',0.00,NULL,NULL,NULL,143,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(434,25,'Parlak Kırmızı',NULL,77,NULL,'interface_color_variations/hex-ff0000.png','medium',0.00,NULL,NULL,NULL,148,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(435,25,'Altın Sarı',NULL,78,NULL,'interface_color_variations/hex-f8c000.png','medium',0.00,NULL,NULL,NULL,149,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(436,25,'Kül Rengi',NULL,86,NULL,'interface_color_variations/hex-a9a9a9.png','medium',0.00,NULL,NULL,NULL,157,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(437,25,'Krema',NULL,32,NULL,'interface_color_variations/hex-f3ead3.png','medium',0.00,NULL,NULL,NULL,103,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(438,25,'Açık Kahve',NULL,37,NULL,'interface_color_variations/hex-cbb89c.png','medium',0.00,NULL,NULL,NULL,108,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(439,25,'Kırmızı',NULL,44,NULL,'interface_color_variations/hex-e60026.png','medium',0.00,NULL,NULL,NULL,115,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(440,25,'Turuncu',NULL,46,NULL,'interface_color_variations/hex-ff6a00.png','medium',0.00,NULL,NULL,NULL,117,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(441,25,'Zeytin Yeşili',NULL,47,NULL,'interface_color_variations/hex-6b8e23.png','medium',0.00,NULL,NULL,NULL,118,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(442,25,'Nane Yeşili',NULL,51,NULL,'interface_color_variations/hex-a8e6cf.png','medium',0.00,NULL,NULL,NULL,122,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(443,25,'Gök Mavisi',NULL,56,NULL,'interface_color_variations/hex-87ceeb.png','medium',0.00,NULL,NULL,NULL,127,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(444,25,'Çelik Mavisi',NULL,63,NULL,'interface_color_variations/hex-3c5a6e.png','medium',0.00,NULL,NULL,NULL,134,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(445,25,'Pastel Pembe',NULL,65,NULL,'interface_color_variations/hex-ffd1dc.png','medium',0.00,NULL,NULL,NULL,136,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(446,25,'Orta Gri',NULL,68,NULL,'interface_color_variations/hex-808080.png','medium',0.00,NULL,NULL,NULL,139,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(447,25,'Canlı Pembe',NULL,69,NULL,'interface_color_variations/hex-ff77b4.png','medium',0.00,NULL,NULL,NULL,140,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(448,25,'Sarı Altın',NULL,80,NULL,'interface_color_variations/hex-ffd700.png','medium',0.00,NULL,NULL,NULL,151,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(449,25,'Haki',NULL,81,NULL,'interface_color_variations/hex-c3b091.png','medium',0.00,NULL,NULL,NULL,152,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(450,25,'Zümrüt Yeşili',NULL,84,NULL,'interface_color_variations/hex-009b77.png','medium',0.00,NULL,NULL,NULL,155,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(451,25,'Tarçın',NULL,41,NULL,'interface_color_variations/hex-a56b3f.png','medium',0.00,NULL,NULL,NULL,112,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(452,25,'Gece Laciverti',NULL,62,NULL,'interface_color_variations/hex-001a33.png','medium',0.00,NULL,NULL,NULL,133,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(453,25,'Lavanta',NULL,73,NULL,'interface_color_variations/hex-e6e6fa.png','medium',0.00,NULL,NULL,NULL,144,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(454,25,'Bordo',NULL,76,NULL,'interface_color_variations/hex-722f37.png','medium',0.00,NULL,NULL,NULL,147,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(455,25,'Bej',NULL,35,NULL,'interface_color_variations/hex-e6d7b8.png','medium',0.00,NULL,NULL,NULL,106,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(456,25,'Kahverengi',NULL,36,NULL,'interface_color_variations/hex-6b3e26.png','medium',0.00,NULL,NULL,NULL,107,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(457,25,'Bebek Pembesi',NULL,40,NULL,'interface_color_variations/hex-ffc1e3.png','medium',0.00,NULL,NULL,NULL,111,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(458,25,'Pembe',NULL,42,NULL,'interface_color_variations/hex-ff69b4.png','medium',0.00,NULL,NULL,NULL,113,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(459,25,'Koyu Kahve',NULL,43,NULL,'interface_color_variations/hex-7a4f2e.png','medium',0.00,NULL,NULL,NULL,114,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(460,25,'Koyu Yeşil',NULL,54,NULL,'interface_color_variations/hex-1f3b2d.png','medium',0.00,NULL,NULL,NULL,125,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(461,25,'Koyu Mavi',NULL,57,NULL,'interface_color_variations/hex-0d47a1.png','medium',0.00,NULL,NULL,NULL,128,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(462,25,'Gri',NULL,66,NULL,'interface_color_variations/hex-b0b0b0.png','medium',0.00,NULL,NULL,NULL,137,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(463,25,'Eflatun',NULL,74,NULL,'interface_color_variations/hex-800080.png','medium',0.00,NULL,NULL,NULL,145,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(464,25,'Gümüş Gri',NULL,85,NULL,'interface_color_variations/hex-d3d3d3.png','medium',0.00,NULL,NULL,NULL,156,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(465,25,'Espresso',NULL,45,NULL,'interface_color_variations/hex-4a2e1e.png','medium',0.00,NULL,NULL,NULL,116,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(466,25,'Mor',NULL,48,NULL,'interface_color_variations/hex-6a3d9a.png','medium',0.00,NULL,NULL,NULL,119,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(467,25,'Açık Mavi',NULL,55,NULL,'interface_color_variations/hex-add8e6.png','medium',0.00,NULL,NULL,NULL,126,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(468,25,'Lacivert',NULL,59,NULL,'interface_color_variations/hex-0d1b3d.png','medium',0.00,NULL,NULL,NULL,130,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(469,25,'Gece Mavisi',NULL,60,NULL,'interface_color_variations/hex-001f3f.png','medium',0.00,NULL,NULL,NULL,131,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(470,25,'Açık Pembe',NULL,67,NULL,'interface_color_variations/hex-ffc0cb.png','medium',0.00,NULL,NULL,NULL,138,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(471,25,'Fuşya',NULL,71,NULL,'interface_color_variations/hex-ff1493.png','medium',0.00,NULL,NULL,NULL,142,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(472,25,'Kömür Gri',NULL,87,NULL,'interface_color_variations/hex-36454f.png','medium',0.00,NULL,NULL,NULL,158,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(473,31,'Beyaz',NULL,29,NULL,'interface_color_variations/hex-ffffff.png','medium',0.00,NULL,NULL,NULL,100,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(474,31,'Açık Gri',NULL,31,NULL,'interface_color_variations/hex-f2f2f2.png','medium',0.00,NULL,NULL,NULL,102,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(475,31,'Vanilya',NULL,33,NULL,'interface_color_variations/hex-f5e6c8.png','medium',0.00,NULL,NULL,NULL,104,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(476,31,'Sarı',NULL,38,NULL,'interface_color_variations/hex-ffb800.png','medium',0.00,NULL,NULL,NULL,109,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(477,31,'Orman Yeşili',NULL,50,NULL,'interface_color_variations/hex-1b5e20.png','medium',0.00,NULL,NULL,NULL,121,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(478,31,'Kraliyet Mavisi',NULL,58,NULL,'interface_color_variations/hex-0033a0.png','medium',0.00,NULL,NULL,NULL,129,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(479,31,'Doğal',NULL,64,NULL,'interface_color_variations/hex-eadbc8.png','medium',0.00,NULL,NULL,NULL,135,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(480,31,'Koyu Zeytin',NULL,83,NULL,'interface_color_variations/hex-556b2f.png','medium',0.00,NULL,NULL,NULL,154,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(481,31,'Kırık Beyaz',NULL,30,NULL,'interface_color_variations/hex-f2efe6.png','medium',0.00,NULL,NULL,NULL,101,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(482,31,'Kum',NULL,34,NULL,'interface_color_variations/hex-f6c9b2.png','medium',0.00,NULL,NULL,NULL,105,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(483,31,'Koyu Gri',NULL,70,NULL,'interface_color_variations/hex-4a4a4a.png','medium',0.00,NULL,NULL,NULL,141,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(484,31,'İndigo',NULL,75,NULL,'interface_color_variations/hex-4b0082.png','medium',0.00,NULL,NULL,NULL,146,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(485,31,'Deniz Laciverti',NULL,88,NULL,'interface_color_variations/hex-000080.png','medium',0.00,NULL,NULL,NULL,159,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(486,31,'Teal',NULL,52,NULL,'interface_color_variations/hex-008080.png','medium',0.00,NULL,NULL,NULL,123,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(487,31,'Altın',NULL,79,NULL,'interface_color_variations/hex-d4a017.png','medium',0.00,NULL,NULL,NULL,150,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(488,31,'Zeytin',NULL,82,NULL,'interface_color_variations/hex-808000.png','medium',0.00,NULL,NULL,NULL,153,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(489,31,'Bronz',NULL,39,NULL,'interface_color_variations/hex-d08b45.png','medium',0.00,NULL,NULL,NULL,110,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(490,31,'Açık Yeşil',NULL,49,NULL,'interface_color_variations/hex-b2cda0.png','medium',0.00,NULL,NULL,NULL,120,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(491,31,'Turkuaz',NULL,53,NULL,'interface_color_variations/hex-8fd6d1.png','medium',0.00,NULL,NULL,NULL,124,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(492,31,'Petrol Mavisi',NULL,61,NULL,'interface_color_variations/hex-1c3d4d.png','medium',0.00,NULL,NULL,NULL,132,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(493,31,'Siyah',NULL,72,NULL,'interface_color_variations/hex-000000.png','medium',0.00,NULL,NULL,NULL,143,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(494,31,'Parlak Kırmızı',NULL,77,NULL,'interface_color_variations/hex-ff0000.png','medium',0.00,NULL,NULL,NULL,148,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(495,31,'Altın Sarı',NULL,78,NULL,'interface_color_variations/hex-f8c000.png','medium',0.00,NULL,NULL,NULL,149,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(496,31,'Kül Rengi',NULL,86,NULL,'interface_color_variations/hex-a9a9a9.png','medium',0.00,NULL,NULL,NULL,157,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(497,31,'Krema',NULL,32,NULL,'interface_color_variations/hex-f3ead3.png','medium',0.00,NULL,NULL,NULL,103,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(498,31,'Açık Kahve',NULL,37,NULL,'interface_color_variations/hex-cbb89c.png','medium',0.00,NULL,NULL,NULL,108,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(499,31,'Kırmızı',NULL,44,NULL,'interface_color_variations/hex-e60026.png','medium',0.00,NULL,NULL,NULL,115,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(500,31,'Turuncu',NULL,46,NULL,'interface_color_variations/hex-ff6a00.png','medium',0.00,NULL,NULL,NULL,117,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(501,31,'Zeytin Yeşili',NULL,47,NULL,'interface_color_variations/hex-6b8e23.png','medium',0.00,NULL,NULL,NULL,118,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(502,31,'Nane Yeşili',NULL,51,NULL,'interface_color_variations/hex-a8e6cf.png','medium',0.00,NULL,NULL,NULL,122,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(503,31,'Gök Mavisi',NULL,56,NULL,'interface_color_variations/hex-87ceeb.png','medium',0.00,NULL,NULL,NULL,127,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(504,31,'Çelik Mavisi',NULL,63,NULL,'interface_color_variations/hex-3c5a6e.png','medium',0.00,NULL,NULL,NULL,134,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(505,31,'Pastel Pembe',NULL,65,NULL,'interface_color_variations/hex-ffd1dc.png','medium',0.00,NULL,NULL,NULL,136,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(506,31,'Orta Gri',NULL,68,NULL,'interface_color_variations/hex-808080.png','medium',0.00,NULL,NULL,NULL,139,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(507,31,'Canlı Pembe',NULL,69,NULL,'interface_color_variations/hex-ff77b4.png','medium',0.00,NULL,NULL,NULL,140,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(508,31,'Sarı Altın',NULL,80,NULL,'interface_color_variations/hex-ffd700.png','medium',0.00,NULL,NULL,NULL,151,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(509,31,'Haki',NULL,81,NULL,'interface_color_variations/hex-c3b091.png','medium',0.00,NULL,NULL,NULL,152,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(510,31,'Zümrüt Yeşili',NULL,84,NULL,'interface_color_variations/hex-009b77.png','medium',0.00,NULL,NULL,NULL,155,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(511,31,'Tarçın',NULL,41,NULL,'interface_color_variations/hex-a56b3f.png','medium',0.00,NULL,NULL,NULL,112,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(512,31,'Gece Laciverti',NULL,62,NULL,'interface_color_variations/hex-001a33.png','medium',0.00,NULL,NULL,NULL,133,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(513,31,'Lavanta',NULL,73,NULL,'interface_color_variations/hex-e6e6fa.png','medium',0.00,NULL,NULL,NULL,144,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(514,31,'Bordo',NULL,76,NULL,'interface_color_variations/hex-722f37.png','medium',0.00,NULL,NULL,NULL,147,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(515,31,'Bej',NULL,35,NULL,'interface_color_variations/hex-e6d7b8.png','medium',0.00,NULL,NULL,NULL,106,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(516,31,'Kahverengi',NULL,36,NULL,'interface_color_variations/hex-6b3e26.png','medium',0.00,NULL,NULL,NULL,107,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(517,31,'Bebek Pembesi',NULL,40,NULL,'interface_color_variations/hex-ffc1e3.png','medium',0.00,NULL,NULL,NULL,111,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(518,31,'Pembe',NULL,42,NULL,'interface_color_variations/hex-ff69b4.png','medium',0.00,NULL,NULL,NULL,113,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(519,31,'Koyu Kahve',NULL,43,NULL,'interface_color_variations/hex-7a4f2e.png','medium',0.00,NULL,NULL,NULL,114,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(520,31,'Koyu Yeşil',NULL,54,NULL,'interface_color_variations/hex-1f3b2d.png','medium',0.00,NULL,NULL,NULL,125,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(521,31,'Koyu Mavi',NULL,57,NULL,'interface_color_variations/hex-0d47a1.png','medium',0.00,NULL,NULL,NULL,128,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(522,31,'Gri',NULL,66,NULL,'interface_color_variations/hex-b0b0b0.png','medium',0.00,NULL,NULL,NULL,137,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(523,31,'Eflatun',NULL,74,NULL,'interface_color_variations/hex-800080.png','medium',0.00,NULL,NULL,NULL,145,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(524,31,'Gümüş Gri',NULL,85,NULL,'interface_color_variations/hex-d3d3d3.png','medium',0.00,NULL,NULL,NULL,156,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(525,31,'Espresso',NULL,45,NULL,'interface_color_variations/hex-4a2e1e.png','medium',0.00,NULL,NULL,NULL,116,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(526,31,'Mor',NULL,48,NULL,'interface_color_variations/hex-6a3d9a.png','medium',0.00,NULL,NULL,NULL,119,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(527,31,'Açık Mavi',NULL,55,NULL,'interface_color_variations/hex-add8e6.png','medium',0.00,NULL,NULL,NULL,126,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(528,31,'Lacivert',NULL,59,NULL,'interface_color_variations/hex-0d1b3d.png','medium',0.00,NULL,NULL,NULL,130,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(529,31,'Gece Mavisi',NULL,60,NULL,'interface_color_variations/hex-001f3f.png','medium',0.00,NULL,NULL,NULL,131,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(530,31,'Açık Pembe',NULL,67,NULL,'interface_color_variations/hex-ffc0cb.png','medium',0.00,NULL,NULL,NULL,138,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(531,31,'Fuşya',NULL,71,NULL,'interface_color_variations/hex-ff1493.png','medium',0.00,NULL,NULL,NULL,142,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(532,31,'Kömür Gri',NULL,87,NULL,'interface_color_variations/hex-36454f.png','medium',0.00,NULL,NULL,NULL,158,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(533,37,'Beyaz',NULL,29,NULL,'interface_color_variations/hex-ffffff.png','medium',0.00,NULL,NULL,NULL,100,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(534,37,'Açık Gri',NULL,31,NULL,'interface_color_variations/hex-f2f2f2.png','medium',0.00,NULL,NULL,NULL,102,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(535,37,'Vanilya',NULL,33,NULL,'interface_color_variations/hex-f5e6c8.png','medium',0.00,NULL,NULL,NULL,104,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(536,37,'Sarı',NULL,38,NULL,'interface_color_variations/hex-ffb800.png','medium',0.00,NULL,NULL,NULL,109,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(537,37,'Orman Yeşili',NULL,50,NULL,'interface_color_variations/hex-1b5e20.png','medium',0.00,NULL,NULL,NULL,121,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(538,37,'Kraliyet Mavisi',NULL,58,NULL,'interface_color_variations/hex-0033a0.png','medium',0.00,NULL,NULL,NULL,129,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(539,37,'Doğal',NULL,64,NULL,'interface_color_variations/hex-eadbc8.png','medium',0.00,NULL,NULL,NULL,135,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(540,37,'Koyu Zeytin',NULL,83,NULL,'interface_color_variations/hex-556b2f.png','medium',0.00,NULL,NULL,NULL,154,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(541,37,'Kırık Beyaz',NULL,30,NULL,'interface_color_variations/hex-f2efe6.png','medium',0.00,NULL,NULL,NULL,101,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(542,37,'Kum',NULL,34,NULL,'interface_color_variations/hex-f6c9b2.png','medium',0.00,NULL,NULL,NULL,105,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(543,37,'Koyu Gri',NULL,70,NULL,'interface_color_variations/hex-4a4a4a.png','medium',0.00,NULL,NULL,NULL,141,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(544,37,'İndigo',NULL,75,NULL,'interface_color_variations/hex-4b0082.png','medium',0.00,NULL,NULL,NULL,146,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(545,37,'Deniz Laciverti',NULL,88,NULL,'interface_color_variations/hex-000080.png','medium',0.00,NULL,NULL,NULL,159,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(546,37,'Teal',NULL,52,NULL,'interface_color_variations/hex-008080.png','medium',0.00,NULL,NULL,NULL,123,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(547,37,'Altın',NULL,79,NULL,'interface_color_variations/hex-d4a017.png','medium',0.00,NULL,NULL,NULL,150,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(548,37,'Zeytin',NULL,82,NULL,'interface_color_variations/hex-808000.png','medium',0.00,NULL,NULL,NULL,153,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(549,37,'Bronz',NULL,39,NULL,'interface_color_variations/hex-d08b45.png','medium',0.00,NULL,NULL,NULL,110,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(550,37,'Açık Yeşil',NULL,49,NULL,'interface_color_variations/hex-b2cda0.png','medium',0.00,NULL,NULL,NULL,120,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(551,37,'Turkuaz',NULL,53,NULL,'interface_color_variations/hex-8fd6d1.png','medium',0.00,NULL,NULL,NULL,124,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(552,37,'Petrol Mavisi',NULL,61,NULL,'interface_color_variations/hex-1c3d4d.png','medium',0.00,NULL,NULL,NULL,132,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(553,37,'Siyah',NULL,72,NULL,'interface_color_variations/hex-000000.png','medium',0.00,NULL,NULL,NULL,143,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(554,37,'Parlak Kırmızı',NULL,77,NULL,'interface_color_variations/hex-ff0000.png','medium',0.00,NULL,NULL,NULL,148,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(555,37,'Altın Sarı',NULL,78,NULL,'interface_color_variations/hex-f8c000.png','medium',0.00,NULL,NULL,NULL,149,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(556,37,'Kül Rengi',NULL,86,NULL,'interface_color_variations/hex-a9a9a9.png','medium',0.00,NULL,NULL,NULL,157,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(557,37,'Krema',NULL,32,NULL,'interface_color_variations/hex-f3ead3.png','medium',0.00,NULL,NULL,NULL,103,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(558,37,'Açık Kahve',NULL,37,NULL,'interface_color_variations/hex-cbb89c.png','medium',0.00,NULL,NULL,NULL,108,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(559,37,'Kırmızı',NULL,44,NULL,'interface_color_variations/hex-e60026.png','medium',0.00,NULL,NULL,NULL,115,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(560,37,'Turuncu',NULL,46,NULL,'interface_color_variations/hex-ff6a00.png','medium',0.00,NULL,NULL,NULL,117,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(561,37,'Zeytin Yeşili',NULL,47,NULL,'interface_color_variations/hex-6b8e23.png','medium',0.00,NULL,NULL,NULL,118,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(562,37,'Nane Yeşili',NULL,51,NULL,'interface_color_variations/hex-a8e6cf.png','medium',0.00,NULL,NULL,NULL,122,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(563,37,'Gök Mavisi',NULL,56,NULL,'interface_color_variations/hex-87ceeb.png','medium',0.00,NULL,NULL,NULL,127,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(564,37,'Çelik Mavisi',NULL,63,NULL,'interface_color_variations/hex-3c5a6e.png','medium',0.00,NULL,NULL,NULL,134,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(565,37,'Pastel Pembe',NULL,65,NULL,'interface_color_variations/hex-ffd1dc.png','medium',0.00,NULL,NULL,NULL,136,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(566,37,'Orta Gri',NULL,68,NULL,'interface_color_variations/hex-808080.png','medium',0.00,NULL,NULL,NULL,139,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(567,37,'Canlı Pembe',NULL,69,NULL,'interface_color_variations/hex-ff77b4.png','medium',0.00,NULL,NULL,NULL,140,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(568,37,'Sarı Altın',NULL,80,NULL,'interface_color_variations/hex-ffd700.png','medium',0.00,NULL,NULL,NULL,151,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(569,37,'Haki',NULL,81,NULL,'interface_color_variations/hex-c3b091.png','medium',0.00,NULL,NULL,NULL,152,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(570,37,'Zümrüt Yeşili',NULL,84,NULL,'interface_color_variations/hex-009b77.png','medium',0.00,NULL,NULL,NULL,155,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(571,37,'Tarçın',NULL,41,NULL,'interface_color_variations/hex-a56b3f.png','medium',0.00,NULL,NULL,NULL,112,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(572,37,'Gece Laciverti',NULL,62,NULL,'interface_color_variations/hex-001a33.png','medium',0.00,NULL,NULL,NULL,133,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(573,37,'Lavanta',NULL,73,NULL,'interface_color_variations/hex-e6e6fa.png','medium',0.00,NULL,NULL,NULL,144,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(574,37,'Bordo',NULL,76,NULL,'interface_color_variations/hex-722f37.png','medium',0.00,NULL,NULL,NULL,147,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(575,37,'Bej',NULL,35,NULL,'interface_color_variations/hex-e6d7b8.png','medium',0.00,NULL,NULL,NULL,106,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(576,37,'Kahverengi',NULL,36,NULL,'interface_color_variations/hex-6b3e26.png','medium',0.00,NULL,NULL,NULL,107,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(577,37,'Bebek Pembesi',NULL,40,NULL,'interface_color_variations/hex-ffc1e3.png','medium',0.00,NULL,NULL,NULL,111,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(578,37,'Pembe',NULL,42,NULL,'interface_color_variations/hex-ff69b4.png','medium',0.00,NULL,NULL,NULL,113,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(579,37,'Koyu Kahve',NULL,43,NULL,'interface_color_variations/hex-7a4f2e.png','medium',0.00,NULL,NULL,NULL,114,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(580,37,'Koyu Yeşil',NULL,54,NULL,'interface_color_variations/hex-1f3b2d.png','medium',0.00,NULL,NULL,NULL,125,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(581,37,'Koyu Mavi',NULL,57,NULL,'interface_color_variations/hex-0d47a1.png','medium',0.00,NULL,NULL,NULL,128,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(582,37,'Gri',NULL,66,NULL,'interface_color_variations/hex-b0b0b0.png','medium',0.00,NULL,NULL,NULL,137,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(583,37,'Eflatun',NULL,74,NULL,'interface_color_variations/hex-800080.png','medium',0.00,NULL,NULL,NULL,145,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(584,37,'Gümüş Gri',NULL,85,NULL,'interface_color_variations/hex-d3d3d3.png','medium',0.00,NULL,NULL,NULL,156,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(585,37,'Espresso',NULL,45,NULL,'interface_color_variations/hex-4a2e1e.png','medium',0.00,NULL,NULL,NULL,116,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(586,37,'Mor',NULL,48,NULL,'interface_color_variations/hex-6a3d9a.png','medium',0.00,NULL,NULL,NULL,119,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(587,37,'Açık Mavi',NULL,55,NULL,'interface_color_variations/hex-add8e6.png','medium',0.00,NULL,NULL,NULL,126,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(588,37,'Lacivert',NULL,59,NULL,'interface_color_variations/hex-0d1b3d.png','medium',0.00,NULL,NULL,NULL,130,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(589,37,'Gece Mavisi',NULL,60,NULL,'interface_color_variations/hex-001f3f.png','medium',0.00,NULL,NULL,NULL,131,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(590,37,'Açık Pembe',NULL,67,NULL,'interface_color_variations/hex-ffc0cb.png','medium',0.00,NULL,NULL,NULL,138,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(591,37,'Fuşya',NULL,71,NULL,'interface_color_variations/hex-ff1493.png','medium',0.00,NULL,NULL,NULL,142,'2026-05-15 10:53:51','2026-05-15 10:53:51'),(592,37,'Kömür Gri',NULL,87,NULL,'interface_color_variations/hex-36454f.png','medium',0.00,NULL,NULL,NULL,158,'2026-05-15 10:53:51','2026-05-15 10:53:51');
/*!40000 ALTER TABLE `product_variation_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variations`
--

DROP TABLE IF EXISTS `product_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'select',
  `depends_on` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `replace_main_gallery_image` tinyint(1) NOT NULL DEFAULT '0',
  `allows_multiple` tinyint(1) NOT NULL DEFAULT '0',
  `solo_option_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_variations_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
INSERT INTO `product_variations` VALUES (4,6,'Kalıp Seçiniz','image','Model Seçiniz',0,0,0,NULL,'2026-03-10 22:27:45','2026-05-09 10:59:18'),(5,6,'Cinsiyet Seçiniz','select','Kalıp Seçiniz',0,0,0,NULL,'2026-03-10 22:38:58','2026-05-09 11:01:18'),(8,6,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,1,'İstemiyorum','2026-03-12 20:23:08','2026-05-08 23:08:31'),(9,6,'Model Seçiniz','image',NULL,0,1,0,NULL,'2026-04-14 17:54:23','2026-05-07 23:12:43'),(10,7,'Beden Seçiniz','image','Model Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(11,7,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(12,7,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(13,7,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(14,7,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(15,7,'Model Seçiniz','image',NULL,0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(16,8,'Beden Seçiniz','image','Model Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(17,8,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(18,8,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(19,8,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(20,8,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(21,8,'Model Seçiniz','image',NULL,0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(22,13,'Beden Seçiniz','image','Model Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(23,13,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(24,13,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(25,13,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(26,13,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(27,13,'Model Seçiniz','image',NULL,0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(28,22,'Beden Seçiniz','image','Model Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(29,22,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(30,22,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(31,22,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(32,22,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(33,22,'Model Seçiniz','image',NULL,0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(34,23,'Beden Seçiniz','image','Model Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(35,23,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(36,23,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(37,23,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(38,23,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(39,23,'Model Seçiniz','image',NULL,0,0,0,NULL,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(41,6,'Kumaş Seçiniz','fabric','Cinsiyet Seçiniz',0,0,0,NULL,'2026-05-09 11:25:29','2026-05-09 11:25:29'),(43,6,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,0,NULL,'2026-05-09 12:02:10','2026-05-09 12:02:38');
/*!40000 ALTER TABLE `product_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `tax_class_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int unsigned DEFAULT NULL,
  `minimum_order_quantity` int unsigned DEFAULT NULL COMMENT 'Sipariş edilebilir minimum miktar. Boş = 1 adet.',
  `currency_id` bigint unsigned DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'satista' COMMENT 'satista = Satışta, stokta_yok = Stokta yok, yakinda_gelecek = Yakında gelecek',
  `sort_order` int NOT NULL DEFAULT '0',
  `show_on_home` tinyint(1) NOT NULL DEFAULT '0',
  `home_showcase_order` int unsigned NOT NULL DEFAULT '0',
  `home_showcase_image` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_table_trigger_variation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_company_id_slug_unique` (`company_id`,`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_currency_id_foreign` (`currency_id`),
  KEY `products_tax_class_id_foreign` (`tax_class_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_tax_class_id_foreign` FOREIGN KEY (`tax_class_id`) REFERENCES `tax_classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (6,1,2,NULL,'Basic Tişört','klasik-tisort','Günlük kullanım için pamuklu tişört. Yaka ve cinsiyete göre renk seçenekleri.',NULL,NULL,NULL,140.00,100,50,1,'products/01KR1PJ739YSK3NC3809EBBA88.png',1,'satista',1,1,1,'products/home-showcase/01KR1Q82KSHSZVVVXXRN6Q88H9.png','Teknik Detaylar ve Aksesuarlar','2026-03-09 19:33:56','2026-05-09 12:19:22'),(7,1,2,NULL,'Polo Yaka Tişört','polo-yaka-tisort','Polo yaka veya düz yaka seçeneği. Cinsiyete göre beden.',NULL,NULL,NULL,189.99,80,NULL,1,'products/01KR1QS7E537SJBGD7HG9DZ25C.png',1,'satista',2,1,4,'products/home-showcase/01KR1QRFGV6HS0F0Y1XJFYXB4F.png',NULL,'2026-03-09 19:33:56','2026-05-07 17:29:19'),(8,1,2,NULL,'Tanktop','oxford-gomlek','Resmi ve yarı resmi kullanım için gömlek. Yaka ve cinsiyete göre renk.',NULL,NULL,NULL,299.99,45,NULL,1,'products/samples/oxford-gomlek.svg',1,'satista',7,0,1000,NULL,NULL,'2026-03-09 19:33:56','2026-05-07 17:03:51'),(9,1,4,NULL,'Sweatshirt','sweatshirt','Kapüşonlu veya kapüşonsuz sweatshirt. Cinsiyete göre renk paleti.',NULL,NULL,NULL,249.99,60,NULL,1,'products/01KR1QGRM0RSEYV23QPBBHAWX5.png',1,'satista',4,1,2,'products/home-showcase/01KR1QGRKV7XP417XK3FPJF5TG.png',NULL,'2026-03-09 19:33:56','2026-05-07 17:24:41'),(11,1,10,NULL,'Klasik Pantolon','klasik-pantolon','Ofis ve günlük pantolon. Tip ve cinsiyete göre beden.',NULL,NULL,NULL,349.99,70,NULL,1,'products/samples/klasik-pantolon.svg',1,'satista',6,0,1000,NULL,NULL,'2026-03-09 19:33:56','2026-05-07 17:04:09'),(13,1,2,NULL,'Spor Forma','spor-forma','<p>Spor forma ürünü incele...</p>',NULL,NULL,NULL,279.99,55,NULL,1,'products/samples/spor-forma.svg',1,'satista',5,1,1000,'products/home-showcase/01KR27PZ21BMTNB48Q33K8CZBD.png',NULL,'2026-03-09 19:33:56','2026-05-07 22:07:59'),(22,1,2,1,'Sweat','deneme123','<p>dadssada</p>',NULL,NULL,NULL,12323.00,1111,1,1,'products/samples/deneme123.svg',1,'satista',3,0,1000,NULL,NULL,'2026-03-09 21:39:17','2026-05-07 17:04:46'),(23,1,2,NULL,'Hoodie','secenek-gorsel-testi','2. Seçenekler görsel testi: Bu üründe \"Örnek\" seçeneğine görsel eklenmiştir. Kaydet\'e basınca görselin kaybolup kaybolmadığını test edin.',NULL,NULL,NULL,99.99,10,NULL,1,'products/01KR1QN7KDKPEYKB6V6YZHKK8A.png',1,'satista',4,1,3,'products/home-showcase/01KR27DYZ3447033DM7A3BKZ0J.png',NULL,'2026-03-10 20:55:48','2026-05-07 22:02:47'),(24,1,22,1,'Bez Çantalar','bez-cantalar','Bez Çantalar için örnek ürün kaydı.',NULL,NULL,NULL,100.00,100,1,1,'products/samples/bez-cantalar.svg',1,'satista',300,1,9,'products/home-showcase/01KR27KWYZQWBT9HQRQRPEEWBV.png',NULL,'2026-04-28 18:35:14','2026-05-07 22:06:01'),(25,1,22,1,'İmperteks Çantalar','imperteks-cantalar','İmperteks Çantalar için örnek ürün kaydı.',NULL,NULL,NULL,115.00,100,1,1,'products/samples/imperteks-cantalar.svg',1,'satista',301,1,10,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(26,1,23,1,'Beyzball Şapkalar','beyzball-sapkalar','Beyzball Şapkalar için örnek ürün kaydı.',NULL,NULL,NULL,130.00,100,1,1,'products/samples/beyzball-sapkalar.svg',1,'satista',302,1,11,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(27,1,23,1,'Bucket Hats','bucket-hats','Bucket Hats için örnek ürün kaydı.',NULL,NULL,NULL,145.00,100,1,1,'products/samples/bucket-hats.svg',1,'satista',303,1,12,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(28,1,23,1,'Hiphop Hats','hiphop-hats','Hiphop Hats için örnek ürün kaydı.',NULL,NULL,NULL,160.00,100,1,1,'products/samples/hiphop-hats.svg',1,'satista',304,1,13,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(29,1,23,1,'Bere','bere','Bere için örnek ürün kaydı.',NULL,NULL,NULL,175.00,100,1,1,'products/samples/bere.svg',1,'satista',305,1,17,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(30,1,27,1,'Çorap','corap','Çorap için örnek ürün kaydı.',NULL,NULL,NULL,190.00,100,1,1,'products/samples/corap.svg',1,'satista',306,1,21,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(31,1,24,1,'İş Önlüğü','is-onlugu','İş Önlüğü için örnek ürün kaydı.',NULL,NULL,NULL,205.00,100,1,1,'products/samples/is-onlugu.svg',1,'satista',307,1,25,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(32,1,24,1,'Gömleklek','gomleklek','Gömleklek için örnek ürün kaydı.',NULL,NULL,NULL,220.00,100,1,1,'products/samples/gomleklek.svg',1,'satista',308,1,14,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(33,1,24,1,'Forma','forma','Forma için örnek ürün kaydı.',NULL,NULL,NULL,235.00,100,1,1,'products/samples/forma.svg',1,'satista',309,1,18,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(34,1,24,1,'Yelek','yelek','Yelek için örnek ürün kaydı.',NULL,NULL,NULL,250.00,100,1,1,'products/samples/yelek.svg',1,'satista',310,1,22,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(35,1,24,1,'Bahçıvan','bahcivan','Bahçıvan için örnek ürün kaydı.',NULL,NULL,NULL,265.00,100,1,1,'products/samples/bahcivan.svg',1,'satista',311,1,26,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(36,1,24,1,'Triko Kazak','triko-kazak','Triko Kazak için örnek ürün kaydı.',NULL,NULL,NULL,280.00,100,1,1,'products/samples/triko-kazak.svg',1,'satista',312,1,15,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(37,1,27,1,'Havlu','havlu','Havlu için örnek ürün kaydı.',NULL,NULL,NULL,295.00,100,1,1,'products/samples/havlu.svg',1,'satista',313,1,19,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(38,1,24,1,'Mont','mont','Mont için örnek ürün kaydı.',NULL,NULL,NULL,310.00,100,1,1,'products/samples/mont.svg',1,'satista',314,1,23,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(39,1,24,1,'İş Pantolon','is-pantolon','İş Pantolon için örnek ürün kaydı.',NULL,NULL,NULL,325.00,100,1,1,'products/samples/is-pantolon.svg',1,'satista',315,1,27,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(40,1,25,1,'Masa Örtüsü','masa-ortusu','Masa Örtüsü için örnek ürün kaydı.',NULL,NULL,NULL,340.00,100,1,1,'products/samples/masa-ortusu.svg',1,'satista',316,1,29,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(41,1,25,1,'Kırlent','kirlent','Kırlent için örnek ürün kaydı.',NULL,NULL,NULL,355.00,100,1,1,'products/samples/kirlent.svg',1,'satista',317,1,33,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(42,1,27,1,'Panço','panco','Panço için örnek ürün kaydı.',NULL,NULL,NULL,370.00,100,1,1,'products/samples/panco.svg',1,'satista',318,0,37,NULL,NULL,'2026-04-28 18:35:14','2026-05-07 17:03:21'),(43,1,25,1,'Runner','runner','Runner için örnek ürün kaydı.',NULL,NULL,NULL,385.00,100,1,1,'products/samples/runner.svg',1,'satista',319,1,30,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(44,1,23,1,'Buff','buff','Buff için örnek ürün kaydı.',NULL,NULL,NULL,400.00,100,1,1,'products/samples/buff.svg',1,'satista',320,1,34,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(45,1,25,1,'Peçete','pecete','Peçete için örnek ürün kaydı.',NULL,NULL,NULL,415.00,100,1,1,'products/samples/pecete.svg',1,'satista',321,1,31,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(46,1,23,1,'Bandana','bandana','Bandana için örnek ürün kaydı.',NULL,NULL,NULL,430.00,100,1,1,'products/samples/bandana.svg',1,'satista',322,1,35,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(47,1,27,1,'Triko Atkı','triko-atki','Triko Atkı için örnek ürün kaydı.',NULL,NULL,NULL,445.00,100,1,1,'products/samples/triko-atki.svg',1,'satista',323,1,16,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(48,1,24,1,'Önlük','onluk','Önlük için örnek ürün kaydı.',NULL,NULL,NULL,460.00,100,1,1,'products/samples/onluk.svg',1,'satista',324,1,20,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(49,1,24,1,'Tulum','tulum','Tulum için örnek ürün kaydı.',NULL,NULL,NULL,475.00,100,1,1,'products/samples/tulum.svg',1,'satista',325,1,24,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(50,1,26,1,'Kupa','kupa','Kupa için örnek ürün kaydı.',NULL,NULL,NULL,490.00,100,1,1,'products/samples/kupa.svg',1,'satista',326,1,28,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(51,1,25,1,'Amerikan Servis','amerikan-servis','Amerikan Servis için örnek ürün kaydı.',NULL,NULL,NULL,505.00,100,1,1,'products/samples/amerikan-servis.svg',1,'satista',327,1,32,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(52,1,27,1,'Bornoz','bornoz','Bornoz için örnek ürün kaydı.',NULL,NULL,NULL,520.00,100,1,1,'products/samples/bornoz.svg',1,'satista',328,1,36,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('4wugqIQTUgf8UC8X7CVONpR03huSkn4QjlzHmgKQ',1,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo4OntzOjY6Il90b2tlbiI7czo0MDoiSEd1R05Xa21QWWFJY3kzQXpYb2J3eDhxWGRBNXRvWUpVcTZuMWdzMCI7czoxNDoic3RvcmVfY3VycmVuY3kiO3M6MzoiVFJZIjtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNToiaHR0cDovL2xvY2FsaG9zdC9hcGkvZXhjaGFuZ2UtcmF0ZXMiO3M6NToicm91dGUiO3M6MTg6ImFwaS5leGNoYW5nZS1yYXRlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlOWYyY2FjZTUyYzM3NTRmMDQyZTg5NTkzMTAxYzlmMDVkNDQ0ZWRjYTUxYjg1OWJiNWQyNGUyNzk2ODJhMmZhIjtzOjg6ImZpbGFtZW50IjthOjA6e319',1778843615);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_methods`
--

DROP TABLE IF EXISTS `shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'TL cinsinden kargo ücreti',
  `free_shipping_min_amount` decimal(12,2) DEFAULT NULL COMMENT 'Bu tutar ve üzeri siparişlerde ücretsiz kargo (TL)',
  `estimated_days` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Örn: 2-4 iş günü',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_methods`
--

LOCK TABLES `shipping_methods` WRITE;
/*!40000 ALTER TABLE `shipping_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `size_table_columns`
--

DROP TABLE IF EXISTS `size_table_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `size_table_columns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `size_table_id` bigint unsigned NOT NULL,
  `size_value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `price_multiplier` decimal(10,4) NOT NULL DEFAULT '1.0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `size_table_columns_size_table_id_foreign` (`size_table_id`),
  CONSTRAINT `size_table_columns_size_table_id_foreign` FOREIGN KEY (`size_table_id`) REFERENCES `size_tables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `size_table_columns`
--

LOCK TABLES `size_table_columns` WRITE;
/*!40000 ALTER TABLE `size_table_columns` DISABLE KEYS */;
INSERT INTO `size_table_columns` VALUES (106,1,'XS',0,0.9400,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(107,1,'S',1,0.9700,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(108,1,'M',2,1.0000,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(109,1,'L',3,1.0700,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(110,1,'XL',4,1.1000,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(111,1,'2XL',5,1.1500,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(112,1,'3XL',6,1.2500,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(113,1,'4XL',7,1.3500,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(114,1,'5XL',8,1.4000,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(115,1,'6XL',9,1.5000,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(116,1,'7XL',10,1.6000,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(117,1,'8XL',11,1.7000,'2026-03-12 23:54:17','2026-05-09 12:28:17'),(118,2,'XS',0,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(119,2,'S',1,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(120,2,'M',2,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(121,2,'L',3,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(122,2,'XL',4,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(123,2,'2XL',5,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(124,2,'3XL',6,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(125,2,'4XL',7,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(126,2,'5XL',8,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(127,2,'6XL',9,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(128,2,'7XL',10,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(129,2,'8XL',11,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(130,3,'98',0,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(131,3,'104',1,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(132,3,'110',2,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(133,3,'116',3,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(134,3,'122',4,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(135,3,'128',5,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(136,3,'134',6,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(137,3,'140',7,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(138,3,'152',8,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(139,3,'158',9,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(140,3,'164',10,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17');
/*!40000 ALTER TABLE `size_table_columns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `size_tables`
--

DROP TABLE IF EXISTS `size_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `size_tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trigger_variation_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trigger_option_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `size_tables_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `size_tables`
--

LOCK TABLES `size_tables` WRITE;
/*!40000 ALTER TABLE `size_tables` DISABLE KEYS */;
INSERT INTO `size_tables` VALUES (1,'Erkek','erkek','BEDEN TABLOSU (ERKEK)','Cinsiyet','Erkek',0,'2026-03-12 22:39:27','2026-03-12 23:54:17'),(2,'Kadın','kadin','Kadın Beden Seçiniz','Cinsiyet','Kadın',1,'2026-03-12 22:39:27','2026-03-12 23:54:17'),(3,'Çocuk','cocuk','BEDEN - ÇOCUK','Cinsiyet','Çocuk',2,'2026-03-12 22:39:27','2026-03-12 22:39:27');
/*!40000 ALTER TABLE `size_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_classes`
--

DROP TABLE IF EXISTS `tax_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_classes`
--

LOCK TABLES `tax_classes` WRITE;
/*!40000 ALTER TABLE `tax_classes` DISABLE KEYS */;
INSERT INTO `tax_classes` VALUES (1,'Vergiye tabi ürünler',0,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `tax_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rates`
--

DROP TABLE IF EXISTS `tax_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tax_class_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `geo_zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bölge / vergi alanı adı',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_rates_tax_class_id_foreign` (`tax_class_id`),
  CONSTRAINT `tax_rates_tax_class_id_foreign` FOREIGN KEY (`tax_class_id`) REFERENCES `tax_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_rates`
--

LOCK TABLES `tax_rates` WRITE;
/*!40000 ALTER TABLE `tax_rates` DISABLE KEYS */;
INSERT INTO `tax_rates` VALUES (1,1,'KDV %18',18.0000,'percentage','Türkiye',0,1,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `tax_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `visible_currency_ids` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Admin','admin@admin.com',NULL,'$2y$12$krk5ykHpDzx/zmWP53owTOvIj7fKhK1tuUo6/zQWMn4tT201/iLc2',1,NULL,NULL,'2026-03-09 19:33:55','2026-03-09 19:33:55'),(2,1,'Demo Müşteri','customer@demo.com',NULL,'$2y$12$24OBGCS1y1jxkmgEj8jcQu8aw4zCYq6qWadJ1qBEwrNgq3vThpum.',0,NULL,NULL,'2026-03-09 19:33:56','2026-03-09 19:33:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'new_istanbul_b2b'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-15 11:14:31

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
INSERT INTO `cache` VALUES ('b2b-app-cache-356a192b7913b04c54574d18c28d46e6395428ab','i:2;',1778196044),('b2b-app-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1778196044;',1778196044),('b2b-app-cache-livewire-rate-limiter:0e8367d0c394ea8e8d16fc7f5084e4eddb908906','i:1;',1778173424),('b2b-app-cache-livewire-rate-limiter:0e8367d0c394ea8e8d16fc7f5084e4eddb908906:timer','i:1778173424;',1778173424),('b2b-app-cache-livewire-rate-limiter:5b5e86c7b451e5528739380cdc97d0344b1eb460','i:1;',1777400793),('b2b-app-cache-livewire-rate-limiter:5b5e86c7b451e5528739380cdc97d0344b1eb460:timer','i:1777400793;',1777400793),('b2b-app-cache-tcmb_spot_rates_usd_eur','a:2:{s:3:\"USD\";d:45.1566;s:3:\"EUR\";d:53.1305;}',1778197113);
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interface_color_variations`
--

LOCK TABLES `interface_color_variations` WRITE;
/*!40000 ALTER TABLE `interface_color_variations` DISABLE KEYS */;
INSERT INTO `interface_color_variations` VALUES (1,'Beyaz','interface_color_variations/01KR1RRSQNASQRXTFGZW2KAJW5.png',0,1,'2026-05-07 17:46:33','2026-05-07 17:46:33'),(2,'WHITE','interface_color_variations/01-white.png',1,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(3,'OFF WHITE','interface_color_variations/02-off-white.png',2,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(4,'CREAM','interface_color_variations/03-cream.png',3,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(5,'SAND','interface_color_variations/04-sand.png',4,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(6,'BROWN','interface_color_variations/05-brown.png',5,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(7,'YELLOW','interface_color_variations/06-yellow.png',6,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(8,'BABY PINK','interface_color_variations/07-baby-pink.png',7,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(9,'PINK','interface_color_variations/08-pink.png',8,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(10,'BURGUNDY','interface_color_variations/09-burgundy.png',9,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(11,'RED','interface_color_variations/10-red.png',10,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(12,'ORANGE','interface_color_variations/11-orange.png',11,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(13,'PURPLE','interface_color_variations/12-purple.png',12,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(14,'FOREST GREEN','interface_color_variations/13-forest-green.png',13,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(15,'TEAL','interface_color_variations/14-teal.png',14,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(16,'DARK GREEN','interface_color_variations/15-dark-green.png',15,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(17,'OLIVE GREEN','interface_color_variations/16-olive-green.png',16,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(18,'SKY BLUE','interface_color_variations/17-sky-blue.png',17,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(19,'ROYAL BLUE','interface_color_variations/18-royal-blue.png',18,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(20,'BLUE','interface_color_variations/19-blue.png',19,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(21,'NAVY','interface_color_variations/20-navy.png',20,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(22,'DEEP NAVY','interface_color_variations/21-deep-navy.png',21,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(23,'NATURAL','interface_color_variations/22-natural.png',22,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(24,'HEATHER GRAY','interface_color_variations/23-heather-gray.png',23,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(25,'GREY','interface_color_variations/24-grey.png',24,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(26,'CHARCOAL','interface_color_variations/25-charcoal.png',25,1,'2026-05-07 18:02:13','2026-05-07 18:02:13'),(27,'BLACK','interface_color_variations/26-black.png',26,1,'2026-05-07 18:02:13','2026-05-07 18:02:13');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interface_fabric_type_variations`
--

LOCK TABLES `interface_fabric_type_variations` WRITE;
/*!40000 ALTER TABLE `interface_fabric_type_variations` DISABLE KEYS */;
INSERT INTO `interface_fabric_type_variations` VALUES (1,'Denim','interface_fabric_type_variations/01KR28V50PYJKPA5FSVF83F4BN.png',0,1,'2026-05-07 22:27:27','2026-05-07 22:27:27');
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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_20_180622_create_companies_table',1),(5,'2026_01_28_000001_add_columns_to_companies_table',1),(6,'2026_01_28_000002_add_b2b_fields_to_users_table',1),(7,'2026_01_28_100000_create_products_table',1),(8,'2026_01_28_120000_create_orders_table',1),(9,'2026_01_28_130000_add_product_variations_and_order_item_variation_data',1),(10,'2026_01_28_150000_add_dependent_variations_to_product_variations',1),(11,'2026_01_28_160000_create_categories_table',1),(12,'2026_01_28_160001_add_category_id_to_products_table',1),(13,'2026_01_28_170000_create_currencies_table',1),(14,'2026_01_28_170001_add_currency_id_to_products_table',1),(15,'2026_01_28_170002_add_exchange_rate_to_currencies_table',1),(16,'2026_01_29_000001_create_dealer_requests_table',1),(17,'2026_01_29_000002_create_product_variation_option_prices_table',1),(18,'2026_01_29_000003_add_variation_price_fields_to_order_items_table',1),(19,'2026_01_29_000004_add_profit_margin_to_companies_table',1),(20,'2026_01_29_100000_create_banner_slides_table',1),(21,'2026_01_29_100001_create_home_sections_table',1),(22,'2026_01_29_200000_add_option_meta_to_product_variations',1),(23,'2026_01_29_300000_add_stock_quantity_to_products_table',1),(24,'2026_01_29_300001_add_stock_quantity_to_product_variation_option_prices_table',1),(25,'2026_01_29_400000_add_meta_tags_to_products_table',1),(26,'2026_01_29_500000_create_customer_groups_table',1),(27,'2026_01_29_500001_create_product_discounts_table',1),(28,'2026_01_29_600000_add_image_to_product_variations',1),(29,'2026_01_29_700000_make_products_stock_quantity_nullable',1),(30,'2026_01_29_800000_create_shipping_methods_table',1),(31,'2026_01_29_800001_add_shipping_to_orders_table',1),(32,'2026_01_29_900000_add_minimum_order_quantity_to_products_table',1),(33,'2026_01_29_900001_seed_minimum_order_quantity_examples',1),(34,'2026_01_29_950000_add_status_to_products_table',1),(35,'2026_01_29_950001_seed_product_status_examples',1),(36,'2026_01_29_960000_create_footer_menu_groups_table',1),(37,'2026_01_29_960001_create_footer_menu_items_table',1),(38,'2026_01_29_970000_create_footer_settings_table',1),(39,'2026_01_29_970001_create_bank_accounts_table',1),(40,'2026_01_29_970002_add_type_to_footer_menu_groups_table',1),(41,'2026_01_29_980000_create_tax_classes_table',1),(42,'2026_01_29_980001_create_tax_rates_table',1),(43,'2026_01_29_980002_add_tax_class_id_to_products_table',1),(44,'2026_01_29_990000_add_bank_account_id_to_orders_table',1),(45,'2026_01_29_990100_add_visible_currency_ids_to_users_table',1),(46,'2026_03_09_100000_ensure_sessions_table_exists',1),(47,'2026_03_10_120000_remove_product_variations_system',2),(48,'2026_03_11_100000_create_product_variations_and_options_tables',3),(49,'2026_03_11_150000_add_parent_option_ids_to_product_variation_options',4),(50,'2026_03_12_100000_create_product_images_table',5),(51,'2026_03_12_120000_add_size_table_trigger_columns_to_products',6),(52,'2026_03_12_140000_add_option_image_size_to_product_variation_options',6),(53,'2026_03_12_160000_simplify_size_table_trigger_to_single_variation',7),(54,'2026_03_13_100000_create_size_tables',8),(55,'2026_03_13_110000_remove_size_table_trigger_from_products',8),(56,'2026_03_14_100000_add_size_table_trigger_variation_to_products',9),(57,'2026_04_21_120000_add_image_path_to_categories_table',10),(58,'2026_04_21_140000_add_home_showcase_to_products_table',11),(59,'2026_04_21_160000_add_price_multiplier_to_size_table_columns',12),(60,'2026_05_06_000001_add_top_menu_fields_to_categories_table',13),(61,'2026_05_07_000001_expand_dealer_requests_for_application_form',14),(62,'2026_05_07_000001_add_home_showcase_image_to_products_table',15),(63,'2026_05_07_180000_create_interface_color_variations_table',16),(64,'2026_05_07_190000_add_interface_color_variation_id_to_product_variation_options',17),(65,'2026_05_07_211000_seed_r_basic_interface_color_variations',18),(66,'2026_05_08_120000_create_interface_fabric_type_variations_table',19),(67,'2026_05_08_130000_add_interface_fabric_type_variation_id_to_product_variation_options',20),(68,'2026_05_07_210000_add_replace_main_gallery_image_to_product_variations',21);
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
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variation_options`
--

LOCK TABLES `product_variation_options` WRITE;
/*!40000 ALTER TABLE `product_variation_options` DISABLE KEYS */;
INSERT INTO `product_variation_options` VALUES (8,4,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[\"11\", \"9\", \"7\", \"5\", \"10\", \"6\"]',0,'2026-03-10 22:27:45','2026-04-21 20:29:02'),(12,4,'OVERSIZE',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[\"11\", \"9\", \"7\", \"5\", \"10\", \"6\"]',0,'2026-03-10 22:29:40','2026-04-21 20:29:02'),(13,4,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[\"11\", \"9\", \"7\", \"5\", \"10\", \"6\"]',0,'2026-03-10 22:29:40','2026-04-21 20:29:02'),(14,5,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:38:58','2026-04-21 20:29:02'),(15,5,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:38:58','2026-03-10 23:17:23'),(16,5,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:38:58','2026-03-10 23:17:23'),(17,6,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,1,'interface_fabric_type_variations/01KR28V50PYJKPA5FSVF83F4BN.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:43:26','2026-05-07 22:27:58'),(18,6,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:43:26','2026-03-10 23:17:23'),(19,6,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:43:26','2026-03-10 23:17:23'),(20,6,'Denim',NULL,NULL,1,'interface_fabric_type_variations/01KR28V50PYJKPA5FSVF83F4BN.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:43:26','2026-05-07 22:31:20'),(21,6,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-10 22:43:26','2026-03-10 23:17:23'),(30,8,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-12 20:23:08','2026-03-12 20:23:08'),(31,8,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-12 20:23:08','2026-03-12 20:30:41'),(32,8,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-03-12 20:23:08','2026-04-21 20:29:02'),(33,9,'O Yaka',NULL,NULL,NULL,'variation_options/01KR2BV7KDSQQBJVWQTSJGQEFM.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 17:54:23','2026-05-07 23:19:56'),(34,9,'V Yaka',NULL,NULL,NULL,'variation_options/01KR2BV7KRPTF9N66997GNEZAM.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 17:54:23','2026-05-07 23:19:56'),(35,10,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(36,10,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(37,10,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(38,11,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(39,11,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(40,11,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(41,12,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(42,12,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(43,12,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(44,12,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(45,12,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(46,13,'Beyaz','#FFF',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(47,13,'Siyah','#000000',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(48,13,'Lacivert','#243412',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(49,13,'Açık Gri','#849234',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(50,13,'Füme','#123654',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(51,13,'Kırmızı','#987434',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(52,13,'Bordo','#120938',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(53,13,'Koyu Yeşil','#565456',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(54,14,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(55,14,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(56,14,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:02:54'),(57,15,'Ribana Yaka',NULL,NULL,NULL,'variation_options/01KKA51NYMZ3F33K0HPWS8DNSB.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:03:29'),(58,15,'Gömlek Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:03:29'),(59,16,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(60,16,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(61,16,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(62,17,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(63,17,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(64,17,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(65,18,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(66,18,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(67,18,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(68,18,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(69,18,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(70,19,'Beyaz','#FFF',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(71,19,'Siyah','#000000',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(72,19,'Lacivert','#243412',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(73,19,'Açık Gri','#849234',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(74,19,'Füme','#123654',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(75,19,'Kırmızı','#987434',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(76,19,'Bordo','#120938',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(77,19,'Koyu Yeşil','#565456',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(78,20,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(79,20,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(80,20,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(81,21,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(82,21,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:08:07'),(83,22,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(84,22,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(85,22,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(86,23,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(87,23,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(88,23,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(89,24,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(90,24,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(91,24,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(92,24,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(93,24,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(94,25,'Beyaz','#FFF',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(95,25,'Siyah','#000000',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(96,25,'Lacivert','#243412',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(97,25,'Açık Gri','#849234',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(98,25,'Füme','#123654',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(99,25,'Kırmızı','#987434',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(100,25,'Bordo','#120938',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(101,25,'Koyu Yeşil','#565456',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(102,26,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(103,26,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(104,26,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(105,27,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(106,27,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:07:11'),(107,28,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(108,28,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(109,28,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(110,29,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(111,29,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(112,29,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(113,30,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(114,30,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(115,30,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(116,30,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(117,30,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(118,31,'Beyaz','#FFF',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(119,31,'Siyah','#000000',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(120,31,'Lacivert','#243412',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(121,31,'Açık Gri','#849234',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(122,31,'Füme','#123654',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(123,31,'Kırmızı','#987434',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(124,31,'Bordo','#120938',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(125,31,'Koyu Yeşil','#565456',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(126,32,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(127,32,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(128,32,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(129,33,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(130,33,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:05:45'),(131,34,'SLIM',NULL,NULL,NULL,'variation_options/01KKCYB9AMBNTN2HDRJ8YTZEK5.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(132,34,'FIT',NULL,NULL,NULL,'variation_options/01KKCYB9AQV19XA31Z8HRHY3R2.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(133,34,'REGULAR',NULL,NULL,NULL,'variation_options/01KKCYB9AW0E3REXSABH93GRWC.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(134,35,'Kadın',NULL,NULL,NULL,'variation_options/01KKCZQ7XRR61W793CDXB25AQN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(135,35,'Çoçuk',NULL,NULL,NULL,'variation_options/01KKCZQ7XSQVWH52XP5Q0XZA9G.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(136,35,'Erkek',NULL,NULL,NULL,'variation_options/01KKCZQ7XVPJRFW1WNAZ7XDWYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(137,36,'%100 cotton single jersey 145-155 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZE0Q7J059TT8ESQG2TJ9B.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(138,36,'%100 cotton single jersey 190-210 gr/m2',NULL,NULL,NULL,'variation_options/01KKCZDGCY421680JYVBSB8D82.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(139,36,'%95 cotton %5 elestan single jersey 200-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD1BB0N5NH228YCXDS7.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(140,36,'%100 mikro polyester 135 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD3XKY6E73QPRT6053Z.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(141,36,'%100 cotton interlock 190-210 gr/m2​',NULL,NULL,NULL,'variation_options/01KKCZDGD5T5A0N5Z2RS8TZQYV.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(142,37,'Beyaz','#FFF',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(143,37,'Siyah','#000000',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(144,37,'Lacivert','#243412',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(145,37,'Açık Gri','#849234',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(146,37,'Füme','#123654',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(147,37,'Kırmızı','#987434',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(148,37,'Bordo','#120938',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(149,37,'Koyu Yeşil','#565456',NULL,NULL,NULL,'medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(150,38,'Ense Biyesi  ​',NULL,NULL,NULL,'variation_options/01KKHVF8DET23F85SSEZQ6A3ZT.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(151,38,'Kol Ribanası  ​',NULL,NULL,NULL,'variation_options/01KKHVX35J6T42MDCC1ZQQ6NNN.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(152,38,'Ense Ay Parçası ​',NULL,NULL,NULL,'variation_options/01KKHVYTTP82AFM1NCG48Z4MH4.png','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(153,39,'O Yaka',NULL,NULL,NULL,'variation_options/01KKA3QZA4J769YVCKWWC7EZ42.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(154,39,'V Yaka',NULL,NULL,NULL,'variation_options/01KKA3X8ZTSG8M6D1ZDDHZAHB2.jpg','medium',0.00,NULL,NULL,'[]',0,'2026-04-14 18:02:25','2026-04-14 18:06:37'),(155,7,'PINK',NULL,9,NULL,'interface_color_variations/08-pink.png','medium',0.00,NULL,NULL,'[]',0,'2026-05-07 17:42:36','2026-05-07 22:31:07'),(156,7,'Krem',NULL,1,NULL,'interface_color_variations/01KR1RRSQNASQRXTFGZW2KAJW5.png','medium',0.00,NULL,NULL,'[]',0,'2026-05-07 17:42:36','2026-05-07 17:49:44');
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_variations_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
INSERT INTO `product_variations` VALUES (4,6,'Beden Seçiniz','image','Model Seçiniz',0,0,'2026-03-10 22:27:45','2026-04-14 17:54:50'),(5,6,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,'2026-03-10 22:38:58','2026-03-10 23:18:05'),(6,6,'Kumaş Seçiniz','fabric','Cinsiyet Seçiniz',0,0,'2026-03-10 22:43:26','2026-05-07 22:27:59'),(7,6,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,'2026-03-10 22:49:07','2026-05-07 17:49:44'),(8,6,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,'2026-03-12 20:23:08','2026-03-12 20:23:58'),(9,6,'Model Seçiniz','image',NULL,0,1,'2026-04-14 17:54:23','2026-05-07 23:12:43'),(10,7,'Beden Seçiniz','image','Model Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(11,7,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(12,7,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(13,7,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(14,7,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(15,7,'Model Seçiniz','image',NULL,0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(16,8,'Beden Seçiniz','image','Model Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(17,8,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(18,8,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(19,8,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(20,8,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(21,8,'Model Seçiniz','image',NULL,0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(22,13,'Beden Seçiniz','image','Model Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(23,13,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(24,13,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(25,13,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(26,13,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(27,13,'Model Seçiniz','image',NULL,0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(28,22,'Beden Seçiniz','image','Model Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(29,22,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(30,22,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(31,22,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(32,22,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(33,22,'Model Seçiniz','image',NULL,0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(34,23,'Beden Seçiniz','image','Model Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(35,23,'Cinsiyet Seçiniz','image','Beden Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(36,23,'Kumaş Seçiniz','image','Cinsiyet Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(37,23,'Renk Seçiniz','color','Kumaş Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(38,23,'Teknik Detaylar ve Aksesuarlar','image','Renk Seçiniz',0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25'),(39,23,'Model Seçiniz','image',NULL,0,0,'2026-04-14 18:02:25','2026-04-14 18:02:25');
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
INSERT INTO `products` VALUES (6,1,2,NULL,'Basic Tişört','klasik-tisort','Günlük kullanım için pamuklu tişört. Yaka ve cinsiyete göre renk seçenekleri.',NULL,NULL,NULL,140.00,100,50,1,'products/01KR1PJ739YSK3NC3809EBBA88.png',1,'satista',1,1,1,'products/home-showcase/01KR1Q82KSHSZVVVXXRN6Q88H9.png',NULL,'2026-03-09 19:33:56','2026-05-07 17:19:57'),(7,1,2,NULL,'Polo Yaka Tişört','polo-yaka-tisort','Polo yaka veya düz yaka seçeneği. Cinsiyete göre beden.',NULL,NULL,NULL,189.99,80,NULL,1,'products/01KR1QS7E537SJBGD7HG9DZ25C.png',1,'satista',2,1,4,'products/home-showcase/01KR1QRFGV6HS0F0Y1XJFYXB4F.png',NULL,'2026-03-09 19:33:56','2026-05-07 17:29:19'),(8,1,2,NULL,'Tanktop','oxford-gomlek','Resmi ve yarı resmi kullanım için gömlek. Yaka ve cinsiyete göre renk.',NULL,NULL,NULL,299.99,45,NULL,1,'products/samples/oxford-gomlek.svg',1,'satista',7,0,1000,NULL,NULL,'2026-03-09 19:33:56','2026-05-07 17:03:51'),(9,1,4,NULL,'Sweatshirt','sweatshirt','Kapüşonlu veya kapüşonsuz sweatshirt. Cinsiyete göre renk paleti.',NULL,NULL,NULL,249.99,60,NULL,1,'products/01KR1QGRM0RSEYV23QPBBHAWX5.png',1,'satista',4,1,2,'products/home-showcase/01KR1QGRKV7XP417XK3FPJF5TG.png',NULL,'2026-03-09 19:33:56','2026-05-07 17:24:41'),(11,1,10,NULL,'Klasik Pantolon','klasik-pantolon','Ofis ve günlük pantolon. Tip ve cinsiyete göre beden.',NULL,NULL,NULL,349.99,70,NULL,1,'products/samples/klasik-pantolon.svg',1,'satista',6,0,1000,NULL,NULL,'2026-03-09 19:33:56','2026-05-07 17:04:09'),(13,1,2,NULL,'Spor Forma','spor-forma','<p>Spor forma ürünü incele...</p>',NULL,NULL,NULL,279.99,55,NULL,1,'products/samples/spor-forma.svg',1,'satista',5,1,1000,'products/home-showcase/01KR27PZ21BMTNB48Q33K8CZBD.png',NULL,'2026-03-09 19:33:56','2026-05-07 22:07:59'),(22,1,2,1,'Sweat','deneme123','<p>dadssada</p>',NULL,NULL,NULL,12323.00,1111,1,1,'products/samples/deneme123.svg',1,'satista',3,0,1000,NULL,NULL,'2026-03-09 21:39:17','2026-05-07 17:04:46'),(23,1,2,NULL,'Hoodie','secenek-gorsel-testi','2. Seçenekler görsel testi: Bu üründe \"Örnek\" seçeneğine görsel eklenmiştir. Kaydet\'e basınca görselin kaybolup kaybolmadığını test edin.',NULL,NULL,NULL,99.99,10,NULL,1,'products/01KR1QN7KDKPEYKB6V6YZHKK8A.png',1,'satista',4,1,3,'products/home-showcase/01KR27DYZ3447033DM7A3BKZ0J.png',NULL,'2026-03-10 20:55:48','2026-05-07 22:02:47'),(24,1,22,1,'Bez Çantalar','bez-cantalar','Bez Çantalar için örnek ürün kaydı.',NULL,NULL,NULL,100.00,100,1,1,'products/samples/bez-cantalar.svg',1,'satista',300,1,9,'products/home-showcase/01KR27KWYZQWBT9HQRQRPEEWBV.png',NULL,'2026-04-28 18:35:14','2026-05-07 22:06:01'),(25,1,22,1,'İmperteks Çantalar','imperteks-cantalar','İmperteks Çantalar için örnek ürün kaydı.',NULL,NULL,NULL,115.00,100,1,1,'products/samples/imperteks-cantalar.svg',1,'satista',301,1,10,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(26,1,23,1,'Beyzball Şapkalar','beyzball-sapkalar','Beyzball Şapkalar için örnek ürün kaydı.',NULL,NULL,NULL,130.00,100,1,1,'products/samples/beyzball-sapkalar.svg',1,'satista',302,1,11,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(27,1,23,1,'Bucket Hats','bucket-hats','Bucket Hats için örnek ürün kaydı.',NULL,NULL,NULL,145.00,100,1,1,'products/samples/bucket-hats.svg',1,'satista',303,1,12,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(28,1,23,1,'Hiphop Hats','hiphop-hats','Hiphop Hats için örnek ürün kaydı.',NULL,NULL,NULL,160.00,100,1,1,'products/samples/hiphop-hats.svg',1,'satista',304,1,13,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(29,1,23,1,'Bere','bere','Bere için örnek ürün kaydı.',NULL,NULL,NULL,175.00,100,1,1,'products/samples/bere.svg',1,'satista',305,1,17,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(30,1,27,1,'Çorap','corap','Çorap için örnek ürün kaydı.',NULL,NULL,NULL,190.00,100,1,1,'products/samples/corap.svg',1,'satista',306,1,21,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(31,1,24,1,'İş Önlüğü','is-onlugu','İş Önlüğü için örnek ürün kaydı.',NULL,NULL,NULL,205.00,100,1,1,'products/samples/is-onlugu.svg',1,'satista',307,1,25,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(32,1,24,1,'Gömleklek','gomleklek','Gömleklek için örnek ürün kaydı.',NULL,NULL,NULL,220.00,100,1,1,'products/samples/gomleklek.svg',1,'satista',308,1,14,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(33,1,24,1,'Forma','forma','Forma için örnek ürün kaydı.',NULL,NULL,NULL,235.00,100,1,1,'products/samples/forma.svg',1,'satista',309,1,18,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(34,1,24,1,'Yelek','yelek','Yelek için örnek ürün kaydı.',NULL,NULL,NULL,250.00,100,1,1,'products/samples/yelek.svg',1,'satista',310,1,22,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(35,1,24,1,'Bahçıvan','bahcivan','Bahçıvan için örnek ürün kaydı.',NULL,NULL,NULL,265.00,100,1,1,'products/samples/bahcivan.svg',1,'satista',311,1,26,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(36,1,24,1,'Triko Kazak','triko-kazak','Triko Kazak için örnek ürün kaydı.',NULL,NULL,NULL,280.00,100,1,1,'products/samples/triko-kazak.svg',1,'satista',312,1,15,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(37,1,27,1,'Havlu','havlu','Havlu için örnek ürün kaydı.',NULL,NULL,NULL,295.00,100,1,1,'products/samples/havlu.svg',1,'satista',313,1,19,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(38,1,24,1,'Mont','mont','Mont için örnek ürün kaydı.',NULL,NULL,NULL,310.00,100,1,1,'products/samples/mont.svg',1,'satista',314,1,23,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(39,1,24,1,'İş Pantolon','is-pantolon','İş Pantolon için örnek ürün kaydı.',NULL,NULL,NULL,325.00,100,1,1,'products/samples/is-pantolon.svg',1,'satista',315,1,27,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(40,1,25,1,'Masa Örtüsü','masa-ortusu','Masa Örtüsü için örnek ürün kaydı.',NULL,NULL,NULL,340.00,100,1,1,'products/samples/masa-ortusu.svg',1,'satista',316,1,29,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(41,1,25,1,'Kırlent','kirlent','Kırlent için örnek ürün kaydı.',NULL,NULL,NULL,355.00,100,1,1,'products/samples/kirlent.svg',1,'satista',317,1,33,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(42,1,27,1,'Panço','panco','Panço için örnek ürün kaydı.',NULL,NULL,NULL,370.00,100,1,1,'products/samples/panco.svg',1,'satista',318,0,37,NULL,NULL,'2026-04-28 18:35:14','2026-05-07 17:03:21'),(43,1,25,1,'Runner','runner','Runner için örnek ürün kaydı.',NULL,NULL,NULL,385.00,100,1,1,'products/samples/runner.svg',1,'satista',319,1,30,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(44,1,23,1,'Buff','buff','Buff için örnek ürün kaydı.',NULL,NULL,NULL,400.00,100,1,1,'products/samples/buff.svg',1,'satista',320,1,34,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(45,1,25,1,'Peçete','pecete','Peçete için örnek ürün kaydı.',NULL,NULL,NULL,415.00,100,1,1,'products/samples/pecete.svg',1,'satista',321,1,31,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(46,1,23,1,'Bandana','bandana','Bandana için örnek ürün kaydı.',NULL,NULL,NULL,430.00,100,1,1,'products/samples/bandana.svg',1,'satista',322,1,35,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(47,1,27,1,'Triko Atkı','triko-atki','Triko Atkı için örnek ürün kaydı.',NULL,NULL,NULL,445.00,100,1,1,'products/samples/triko-atki.svg',1,'satista',323,1,16,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(48,1,24,1,'Önlük','onluk','Önlük için örnek ürün kaydı.',NULL,NULL,NULL,460.00,100,1,1,'products/samples/onluk.svg',1,'satista',324,1,20,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(49,1,24,1,'Tulum','tulum','Tulum için örnek ürün kaydı.',NULL,NULL,NULL,475.00,100,1,1,'products/samples/tulum.svg',1,'satista',325,1,24,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(50,1,26,1,'Kupa','kupa','Kupa için örnek ürün kaydı.',NULL,NULL,NULL,490.00,100,1,1,'products/samples/kupa.svg',1,'satista',326,1,28,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(51,1,25,1,'Amerikan Servis','amerikan-servis','Amerikan Servis için örnek ürün kaydı.',NULL,NULL,NULL,505.00,100,1,1,'products/samples/amerikan-servis.svg',1,'satista',327,1,32,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21'),(52,1,27,1,'Bornoz','bornoz','Bornoz için örnek ürün kaydı.',NULL,NULL,NULL,520.00,100,1,1,'products/samples/bornoz.svg',1,'satista',328,1,36,NULL,NULL,'2026-04-28 18:35:14','2026-04-28 18:48:21');
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
INSERT INTO `sessions` VALUES ('AdkuQ0JE0FDEj3qoWKn5ycJXbOCg2vAv0zmQeQav',1,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6InEwRHA2ckRjaXNZMHozNnJ5blp6d2QxT0NES3BpdUtkUmZhV3RqVFciO3M6MTQ6InN0b3JlX2N1cnJlbmN5IjtzOjM6IlRSWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3QvYXBpL2V4Y2hhbmdlLXJhdGVzIjtzOjU6InJvdXRlIjtzOjE4OiJhcGkuZXhjaGFuZ2UtcmF0ZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZTlmMmNhY2U1MmMzNzU0ZjA0MmU4OTU5MzEwMWM5ZjA1ZDQ0NGVkY2E1MWI4NTliYjVkMjRlMjc5NjgyYTJmYSI7czo4OiJmaWxhbWVudCI7YTowOnt9czo2OiJsb2NhbGUiO3M6MjoiZW4iO3M6NDoiY2FydCI7YToxOntpOjk7YToyOntzOjEwOiJwcm9kdWN0X2lkIjtpOjk7czo4OiJxdWFudGl0eSI7aToxO319fQ==',1778197068);
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
INSERT INTO `size_table_columns` VALUES (106,1,'XS',0,0.8000,'2026-03-12 23:54:17','2026-04-21 20:53:56'),(107,1,'S',1,0.9000,'2026-03-12 23:54:17','2026-04-21 20:53:56'),(108,1,'M',2,0.9996,'2026-03-12 23:54:17','2026-04-21 20:53:56'),(109,1,'L',3,1.5000,'2026-03-12 23:54:17','2026-04-21 20:53:56'),(110,1,'XL',4,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(111,1,'2XL',5,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(112,1,'3XL',6,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(113,1,'4XL',7,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(114,1,'5XL',8,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(115,1,'6XL',9,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(116,1,'7XL',10,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(117,1,'8XL',11,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(118,2,'XS',0,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(119,2,'S',1,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(120,2,'M',2,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(121,2,'L',3,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(122,2,'XL',4,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(123,2,'2XL',5,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(124,2,'3XL',6,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(125,2,'4XL',7,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(126,2,'5XL',8,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(127,2,'6XL',9,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(128,2,'7XL',10,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(129,2,'8XL',11,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(130,3,'98',0,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(131,3,'104',1,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(132,3,'110',2,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(133,3,'116',3,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(134,3,'122',4,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(135,3,'128',5,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(136,3,'134',6,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(137,3,'140',7,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(138,3,'152',8,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(139,3,'158',9,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17'),(140,3,'164',10,1.0000,'2026-03-12 23:54:17','2026-03-12 23:54:17');
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-07 23:38:07

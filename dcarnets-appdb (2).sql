-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 08-04-2025 a las 15:55:00
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dcarnets-appdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dependencia` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution_id` bigint UNSIGNED NOT NULL,
  `peopletype_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_institution_id_foreign` (`institution_id`),
  KEY `assignments_peopletype_id_foreign` (`peopletype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1744118040),
('laravel_cache_356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1744118040;', 1744118040),
('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1744124651),
('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1744124651;', 1744124651),
('laravel_cache_spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:78:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:9:\"view_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"view_any_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:11:\"create_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"update_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"delete_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"delete_any_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:15:\"view_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:19:\"view_any_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:17:\"create_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:17:\"update_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:18:\"restore_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:22:\"restore_any_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:20:\"replicate_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:18:\"reorder_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:17:\"delete_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:21:\"delete_any_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:23:\"force_delete_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:27:\"force_delete_any_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:12:\"view_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:16:\"view_any_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:14:\"create_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:14:\"update_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:15:\"restore_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:19:\"restore_any_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:17:\"replicate_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:15:\"reorder_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:14:\"delete_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:18:\"delete_any_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:20:\"force_delete_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:24:\"force_delete_any_contact\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:16:\"view_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:20:\"view_any_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:18:\"create_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:18:\"update_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:19:\"restore_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:23:\"restore_any_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:21:\"replicate_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:19:\"reorder_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:18:\"delete_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:22:\"delete_any_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:24:\"force_delete_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:28:\"force_delete_any_institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:11:\"view_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:15:\"view_any_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:13:\"create_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:13:\"update_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:14:\"restore_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:18:\"restore_any_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:16:\"replicate_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:14:\"reorder_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:13:\"delete_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:17:\"delete_any_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:19:\"force_delete_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:23:\"force_delete_any_people\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:15:\"view_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:19:\"view_any_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:17:\"create_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:17:\"update_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:18:\"restore_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:22:\"restore_any_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:20:\"replicate_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:18:\"reorder_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:17:\"delete_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:21:\"delete_any_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:23:\"force_delete_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:27:\"force_delete_any_peopletype\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:11:\"view_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:15:\"view_any_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:13:\"create_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:13:\"update_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:14:\"restore_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:18:\"restore_any_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:16:\"replicate_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:14:\"reorder_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:13:\"delete_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:17:\"delete_any_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:19:\"force_delete_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:23:\"force_delete_any_period\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}}s:5:\"roles\";a:2:{i:0;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"Instituto\";s:1:\"c\";s:3:\"web\";}}}', 1744205918);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('SocialMedia','Contact','Address','WebSite') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contacts`
--

INSERT INTO `contacts` (`id`, `nombre`, `icono`, `tipo`, `created_at`, `updated_at`) VALUES
(1, 'Facebook', 'icons/contact-facebook.png', 'SocialMedia', '2025-03-15 06:26:07', '2025-03-15 06:26:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institutions`
--

DROP TABLE IF EXISTS `institutions`;
CREATE TABLE IF NOT EXISTS `institutions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institutions_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `institutions`
--

INSERT INTO `institutions` (`id`, `nombre`, `logo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Concepto Digital', 'institution/logo-LogoConceptodigital.png', 1, '2025-03-25 18:59:31', '2025-03-25 18:59:31'),
(2, 'Colegio Cristo Rey', 'institution/logo-358132668_614239054130914_4800305797006065945_n.png', 2, '2025-04-08 17:13:59', '2025-04-08 17:13:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institution_contacts`
--

DROP TABLE IF EXISTS `institution_contacts`;
CREATE TABLE IF NOT EXISTS `institution_contacts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `informacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `institution_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_contacts_contact_id_foreign` (`contact_id`),
  KEY `institution_contacts_institution_id_foreign` (`institution_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `institution_contacts`
--

INSERT INTO `institution_contacts` (`id`, `informacion`, `contact_id`, `institution_id`, `created_at`, `updated_at`) VALUES
(1, 'conceptodigital', 1, 1, '2025-03-25 18:59:32', '2025-03-25 18:59:32'),
(2, 'https://www.facebook.com/p/Colegio-Cristo-Rey-Venezuela-100066344755314/', 1, 2, '2025-04-08 17:13:59', '2025-04-08 17:13:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_03_13_224136_create_contacts_table', 2),
(5, '2025_03_13_230740_create_institutions_table', 2),
(6, '2025_03_15_004713_create_institution_contacts_table', 2),
(7, '2025_03_29_124800_create_schoolyears_table', 3),
(8, '2025_04_02_124908_create_peopletypes_table', 4),
(9, '2025_04_02_125339_create_periods_table', 4),
(10, '2025_04_02_125522_create_assignments_table', 4),
(13, '2025_04_07_013122_create_permission_tables', 5),
(14, '2025_04_02_131718_create_peoples_table', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `people`
--

DROP TABLE IF EXISTS `people`;
CREATE TABLE IF NOT EXISTS `people` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombres` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `institution_id` bigint UNSIGNED NOT NULL,
  `peopletype_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `people_institution_id_foreign` (`institution_id`),
  KEY `people_peopletype_id_foreign` (`peopletype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peopletypes`
--

DROP TABLE IF EXISTS `peopletypes`;
CREATE TABLE IF NOT EXISTS `peopletypes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grupo` int NOT NULL,
  `institution_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `peopletypes_institution_id_foreign` (`institution_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `peopletypes`
--

INSERT INTO `peopletypes` (`id`, `nombre`, `grupo`, `institution_id`, `created_at`, `updated_at`) VALUES
(1, 'Docente', 1, 1, '2025-04-03 19:09:32', '2025-04-03 19:09:32'),
(2, 'Administrativo', 1, 1, '2025-04-03 19:10:06', '2025-04-03 19:10:06'),
(3, 'Obrero', 1, 1, '2025-04-03 19:10:26', '2025-04-03 19:10:26'),
(4, 'Estudiante', 2, 1, '2025-04-03 19:10:40', '2025-04-03 19:10:40'),
(5, 'Docente', 1, 2, '2025-04-08 19:05:58', '2025-04-08 19:05:58'),
(6, 'Administrativo', 1, 2, '2025-04-08 19:06:53', '2025-04-08 19:06:53'),
(7, 'Estudiante', 2, 2, '2025-04-08 19:07:31', '2025-04-08 19:07:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE IF NOT EXISTS `periods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `periodo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `actual` tinyint(1) NOT NULL DEFAULT '1',
  `institution_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `periods_institution_id_foreign` (`institution_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `periods`
--

INSERT INTO `periods` (`id`, `periodo`, `fecha_inicio`, `fecha_fin`, `activo`, `actual`, `institution_id`, `created_at`, `updated_at`) VALUES
(1, '2024-2025', '2024-09-16', '2025-07-31', 1, 1, 1, '2025-04-03 18:31:15', '2025-04-03 18:31:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view_role', 'web', '2025-04-07 18:51:13', '2025-04-07 18:51:13'),
(2, 'view_any_role', 'web', '2025-04-07 18:51:13', '2025-04-07 18:51:13'),
(3, 'create_role', 'web', '2025-04-07 18:51:13', '2025-04-07 18:51:13'),
(4, 'update_role', 'web', '2025-04-07 18:51:14', '2025-04-07 18:51:14'),
(5, 'delete_role', 'web', '2025-04-07 18:51:14', '2025-04-07 18:51:14'),
(6, 'delete_any_role', 'web', '2025-04-07 18:51:14', '2025-04-07 18:51:14'),
(7, 'view_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(8, 'view_any_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(9, 'create_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(10, 'update_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(11, 'restore_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(12, 'restore_any_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(13, 'replicate_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(14, 'reorder_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(15, 'delete_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(16, 'delete_any_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(17, 'force_delete_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(18, 'force_delete_any_assignment', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(19, 'view_contact', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(20, 'view_any_contact', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(21, 'create_contact', 'web', '2025-04-07 19:01:16', '2025-04-07 19:01:16'),
(22, 'update_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(23, 'restore_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(24, 'restore_any_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(25, 'replicate_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(26, 'reorder_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(27, 'delete_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(28, 'delete_any_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(29, 'force_delete_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(30, 'force_delete_any_contact', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(31, 'view_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(32, 'view_any_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(33, 'create_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(34, 'update_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(35, 'restore_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(36, 'restore_any_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(37, 'replicate_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(38, 'reorder_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(39, 'delete_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(40, 'delete_any_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(41, 'force_delete_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(42, 'force_delete_any_institution', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(43, 'view_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(44, 'view_any_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(45, 'create_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(46, 'update_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(47, 'restore_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(48, 'restore_any_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(49, 'replicate_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(50, 'reorder_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(51, 'delete_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(52, 'delete_any_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(53, 'force_delete_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(54, 'force_delete_any_people', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(55, 'view_peopletype', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(56, 'view_any_peopletype', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(57, 'create_peopletype', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(58, 'update_peopletype', 'web', '2025-04-07 19:01:17', '2025-04-07 19:01:17'),
(59, 'restore_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(60, 'restore_any_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(61, 'replicate_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(62, 'reorder_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(63, 'delete_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(64, 'delete_any_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(65, 'force_delete_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(66, 'force_delete_any_peopletype', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(67, 'view_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(68, 'view_any_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(69, 'create_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(70, 'update_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(71, 'restore_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(72, 'restore_any_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(73, 'replicate_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(74, 'reorder_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(75, 'delete_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(76, 'delete_any_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(77, 'force_delete_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18'),
(78, 'force_delete_any_period', 'web', '2025-04-07 19:01:18', '2025-04-07 19:01:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'panel_user', 'web', '2025-04-07 18:37:27', '2025-04-07 18:37:27'),
(2, 'super_admin', 'web', '2025-04-07 18:51:14', '2025-04-07 18:51:14'),
(3, 'Instituto', 'web', '2025-04-08 17:38:37', '2025-04-08 17:38:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2),
(47, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(43, 3),
(44, 3),
(45, 3),
(46, 3),
(47, 3),
(48, 3),
(49, 3),
(50, 3),
(51, 3),
(52, 3),
(53, 3),
(54, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('DSitZCeHoFjIUzTXJ8T3Uzddyvnfjh323aGMyadl', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoid0paTk9US3hmR253TGZxNFZDQm8yQXlpQkhYQkdpVW5reEhaaFJkdyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJFl2dHVUQ2NPWnFib0hWbzV0aWpaTE83RXZ0aXQ1Q01FazUyWkswQldzalRKT3BsM0JyaUFhIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0MToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluL3Blb3BsZS9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjg6ImZpbGFtZW50IjthOjA6e319', 1744125329),
('rPW6icjGKcmDb4F7Xi2RcGoVLe1K1PcgIzvM1JQw', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoieXRSSEFWZFZUMHVYclliQll4SzI5UWRqYWFaNW0zTDRTVFk1dVMySiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vcGVvcGxlL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiQ3dG5lSGp6ZzJUUE93d0cxTFFzOUVlWmYyZ2padVQ2R0JDaVpGZmhVamlYVHJJLlN0dHVEMiI7fQ==', 1744127194);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Aristides Castro', 'aristidescastro@gmail.com', NULL, '$2y$12$YvtuTCcOZqboHVo5tijZLO7Evtit5CMEk52ZK0BWsjTJOpl3BriAa', 'gV5wLCfr3tHhedN5EgUZAoACWyi1vLJZyBTPmcO4DDy8gERoE8Pbo5NMdAcT', '2025-03-14 02:17:51', '2025-03-14 02:17:51'),
(2, 'Lolimar Lopez', 'lolimarlopez80@gmail.com', NULL, '$2y$12$7tneHjzg2TPOwwG1LQs9EeZf2gjZuT6GBCiZFfhUjiXTrI.SttuD2', 'aBOwTedKRfJ3f51KtmeZ7kYy4CyMURiY4HjB8KA1Nv5DHL2Z8Y1VHGOm6xPT', '2025-04-08 17:07:12', '2025-04-08 17:39:58');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `assignments_peopletype_id_foreign` FOREIGN KEY (`peopletype_id`) REFERENCES `peopletypes` (`id`);

--
-- Filtros para la tabla `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `institutions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `institution_contacts`
--
ALTER TABLE `institution_contacts`
  ADD CONSTRAINT `institution_contacts_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `institution_contacts_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `people_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `people_peopletype_id_foreign` FOREIGN KEY (`peopletype_id`) REFERENCES `peopletypes` (`id`);

--
-- Filtros para la tabla `peopletypes`
--
ALTER TABLE `peopletypes`
  ADD CONSTRAINT `peopletypes_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Filtros para la tabla `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

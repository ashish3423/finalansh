-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2026 at 01:21 PM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `codexansh`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('ansh-enterprises-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:2;', 1794656785),
('ansh-enterprises-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1794656785;', 1794656785);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `government_client_id` bigint(20) UNSIGNED NOT NULL,
  `contract_number` varchar(255) NOT NULL,
  `organization` varchar(180) DEFAULT NULL,
  `department` varchar(180) DEFAULT NULL,
  `contract_category` varchar(40) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `state` varchar(120) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `extension_date` date DEFAULT NULL,
  `contract_value` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `scope` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `government_client_id`, `contract_number`, `organization`, `department`, `contract_category`, `address`, `city`, `state`, `postal_code`, `title`, `service_type`, `start_date`, `end_date`, `extension_date`, `contract_value`, `status`, `scope`, `created_at`, `updated_at`) VALUES
(1, 1, 'GEMC-511687757646444', 'Test Org', 'Demo Department', 'regular_contract', 'sdgsdh yrwywy ywyweywe', 'Kanpur', 'Uttar Pradesh', '208027', 'Test Contracts of defence', 'ambulance', '2026-10-01', '2026-12-31', NULL, 2555500.00, 'active', 'Test Reminder Details', '2026-11-03 04:50:36', '2026-11-03 06:00:17'),
(2, 1, 'GEMC-511687757777888', 'Aiims', 'Health Department', 'regular_contract', 'Test City, Test Demo', 'Kanpur', 'Uttar Pradesh', '208027', 'Aiims', 'manpower', '2026-09-01', '2026-12-31', NULL, 5805880.00, 'active', 'yuutuytuyt hgghjgjhgjhghjg', '2026-11-14 01:42:41', '2026-11-14 01:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `contract_deployments`
--

CREATE TABLE `contract_deployments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `monthly_rate` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_deployments`
--

INSERT INTO `contract_deployments` (`id`, `contract_id`, `service_type`, `employee_id`, `vehicle_id`, `position`, `start_date`, `end_date`, `monthly_rate`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(2, 1, 'ambulance', 1, NULL, 'driver', '2026-11-14', NULL, 0.00, 'active', NULL, '2026-11-14 01:36:02', '2026-11-14 01:36:02'),
(3, 2, 'manpower', 2, NULL, 'Dummy Developer', '2026-11-14', NULL, 0.00, 'active', NULL, '2026-11-14 01:36:02', '2026-11-14 01:43:48'),
(4, 1, 'ambulance', NULL, 1, NULL, '2026-11-14', NULL, 0.00, 'active', NULL, '2026-11-14 01:36:02', '2026-11-14 01:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `employee_category` varchar(30) DEFAULT NULL,
  `work_location` varchar(180) DEFAULT NULL,
  `department` varchar(160) DEFAULT NULL,
  `aadhar_number` varchar(20) DEFAULT NULL,
  `account_number` varchar(40) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `uan_number` varchar(30) DEFAULT NULL,
  `esi_number` varchar(30) DEFAULT NULL,
  `ref_num1` varchar(100) DEFAULT NULL,
  `ref_num2` varchar(100) DEFAULT NULL,
  `id_card_status` varchar(20) DEFAULT NULL,
  `dress_status` varchar(20) DEFAULT NULL,
  `epf_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `esi_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `police_verification_status` varchar(10) NOT NULL DEFAULT 'no',
  `police_verification_certificate_number` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `monthly_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `daily_wages_rate` decimal(12,2) NOT NULL DEFAULT 0.00,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `name`, `role`, `employee_category`, `work_location`, `department`, `aadhar_number`, `account_number`, `ifsc_code`, `uan_number`, `esi_number`, `ref_num1`, `ref_num2`, `id_card_status`, `dress_status`, `epf_exempt`, `esi_exempt`, `police_verification_status`, `police_verification_certificate_number`, `phone`, `email`, `license_number`, `joining_date`, `monthly_salary`, `daily_wages_rate`, `photo_path`, `status`, `address`, `created_at`, `updated_at`) VALUES
(1, 'AE/1126/01', 'Test Rahul', 'Test Role', 'unskilled', NULL, 'Development', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_issued', 'not_issued', 0, 0, 'no', NULL, '6388963210', 'testrahul@gmail.com', '9788979879878', '2026-11-01', 0.00, 1085.00, 'employees/5F07xcxLmZBsq2hSNwF9PZPXK2XudpHpOUp4ZfBI.jpg', 'active', 'Test Street, Lorem City, Lipsum 208001', '2026-11-03 04:01:42', '2026-11-04 05:31:53'),
(2, 'AE/1126/02', 'Ramesh Sample', 'Dummy Developer', 'semi_skilled', NULL, 'Development', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_issued', 'not_issued', 0, 0, 'no', NULL, '8965745256', 'rameshd@gmail.com', '84546546546', '2026-10-01', 0.00, 985.00, 'employees/U6wMNGsj1dxaXJTfQ60RhMqNLGO0AJ6Fn50ax4tM.jpg', 'active', 'Lorem Lipsum City Street 987321', '2026-11-03 04:17:38', '2026-11-04 05:32:13'),
(3, 'AE/1126/03', 'Prashant Vashishth', 'IOS Developer', 'high_skilled', 'India', 'Ios Develpment', '9797797987897', '5465465465456', 'ghhh7897897', '879878789797', '7979877987', '8797979798', '56464664654', 'issued', 'issued', 1, 1, 'no', NULL, '9879874589', 'prashant@gmail.com', '77897987', '2026-08-01', 0.00, 1585.00, 'employees/mQCSd56EzSG1axizBdLCBk5KVBzoG6RZx3csl59L.jpg', 'active', 'qweqw qwenjhj lknhklhlk mnlkk987654', '2026-11-04 05:27:04', '2026-11-14 05:30:05'),
(4, 'AE/1126/04', 'wetwet', 'wetwet', 'semi_skilled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'not_issued', 'not_issued', 0, 0, 'no', NULL, '6346346346', NULL, NULL, NULL, 0.00, 200.00, 'employees/p6DXF4uTpbaBK61jvvdZ0GmLVknaj84fRuOboAjB.jpg', 'active', NULL, '2026-11-14 05:30:45', '2026-11-14 05:30:45');

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance`
--

CREATE TABLE `employee_attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_attendance`
--

INSERT INTO `employee_attendance` (`id`, `employee_id`, `attendance_date`, `check_in`, `check_out`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-11-03', '09:00:00', '18:00:00', 'present', NULL, '2026-11-03 05:02:57', '2026-11-03 05:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `employee_salaries`
--

CREATE TABLE `employee_salaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `salary_month` date NOT NULL,
  `employee_code_snapshot` varchar(40) NOT NULL,
  `employee_name_snapshot` varchar(160) NOT NULL,
  `designation_snapshot` varchar(100) NOT NULL,
  `contract_number_snapshot` varchar(80) DEFAULT NULL,
  `working_days` smallint(5) UNSIGNED NOT NULL,
  `daily_wages_rate` decimal(12,2) NOT NULL,
  `earned_wages` decimal(14,2) NOT NULL,
  `pf_wage_basis` decimal(14,2) NOT NULL,
  `epf_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `pf_employee_rate` decimal(5,2) NOT NULL,
  `pf_employer_rate` decimal(5,2) NOT NULL,
  `pf_employee_amount` decimal(14,2) NOT NULL,
  `pf_employer_amount` decimal(14,2) NOT NULL,
  `esi_applicable` tinyint(1) NOT NULL DEFAULT 0,
  `esi_exempt` tinyint(1) NOT NULL DEFAULT 0,
  `esi_employee_rate` decimal(5,2) NOT NULL,
  `esi_employer_rate` decimal(5,2) NOT NULL,
  `esi_employee_amount` decimal(14,2) NOT NULL,
  `esi_employer_amount` decimal(14,2) NOT NULL,
  `bonus_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `bonus_eligible_days` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `bonus_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(14,2) NOT NULL,
  `total_deductions` decimal(14,2) NOT NULL,
  `net_salary` decimal(14,2) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_salaries`
--

INSERT INTO `employee_salaries` (`id`, `employee_id`, `salary_month`, `employee_code_snapshot`, `employee_name_snapshot`, `designation_snapshot`, `contract_number_snapshot`, `working_days`, `daily_wages_rate`, `earned_wages`, `pf_wage_basis`, `epf_exempt`, `pf_employee_rate`, `pf_employer_rate`, `pf_employee_amount`, `pf_employer_amount`, `esi_applicable`, `esi_exempt`, `esi_employee_rate`, `esi_employer_rate`, `esi_employee_amount`, `esi_employer_amount`, `bonus_eligible`, `bonus_eligible_days`, `bonus_amount`, `gross_salary`, `total_deductions`, `net_salary`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-10-01', 'AE/1126/02', 'Ramesh Sample', 'Dummy Developer', 'GEMC-511687757646444', 25, 985.00, 24625.00, 24625.00, 0, 12.00, 12.00, 2955.00, 2955.00, 0, 0, 0.75, 3.25, 0.00, 0.00, 0, 25, 0.00, 24625.00, 2955.00, 21670.00, 1, '2026-11-03 04:54:09', '2026-11-03 04:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `government_clients`
--

CREATE TABLE `government_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `government_clients`
--

INSERT INTO `government_clients` (`id`, `name`, `department`, `contact_person`, `email`, `phone`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ministry of Defiance', 'Defence', 'Prashnat', NULL, '9874896523', 'Test Sample Dummy 202520', 'active', '2026-11-03 04:49:08', '2026-11-03 04:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_payments`
--

CREATE TABLE `invoice_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_11_03_000000_create_enterprise_operations_tables', 2),
(5, '2026_11_03_000001_add_role_to_users_table', 3),
(6, '2026_11_03_000002_add_daily_wages_and_photo_to_employees_table', 4),
(7, '2026_11_03_000003_create_salary_payroll_tables', 5),
(8, '2026_11_03_000004_set_bonus_threshold_unconfigured_by_default', 6),
(9, '2026_11_03_000005_update_epf_wage_ceiling_default', 7),
(10, '2026_11_03_000004_add_contract_details_and_extension_date', 8),
(11, '2026_11_04_000005_add_employee_profile_and_payroll_exemptions', 9),
(12, '2026_11_14_000001_create_vehicle_expiry_reminders_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_rule_settings`
--

CREATE TABLE `salary_rule_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pf_employee_rate` decimal(5,2) NOT NULL DEFAULT 12.00,
  `pf_employer_rate` decimal(5,2) NOT NULL DEFAULT 12.00,
  `pf_monthly_wage_ceiling` decimal(12,2) NOT NULL DEFAULT 15000.00,
  `esi_employee_rate` decimal(5,2) NOT NULL DEFAULT 0.75,
  `esi_employer_rate` decimal(5,2) NOT NULL DEFAULT 3.25,
  `esi_monthly_wage_limit` decimal(12,2) NOT NULL DEFAULT 21000.00,
  `esi_employee_exemption_daily_wage` decimal(10,2) NOT NULL DEFAULT 176.00,
  `bonus_monthly_eligibility_limit` decimal(12,2) NOT NULL DEFAULT 21000.00,
  `bonus_monthly_calculation_ceiling` decimal(12,2) NOT NULL DEFAULT 7000.00,
  `bonus_minimum_rate` decimal(5,2) NOT NULL DEFAULT 8.33,
  `bonus_maximum_rate` decimal(5,2) NOT NULL DEFAULT 20.00,
  `bonus_minimum_days` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salary_rule_settings`
--

INSERT INTO `salary_rule_settings` (`id`, `pf_employee_rate`, `pf_employer_rate`, `pf_monthly_wage_ceiling`, `esi_employee_rate`, `esi_employer_rate`, `esi_monthly_wage_limit`, `esi_employee_exemption_daily_wage`, `bonus_monthly_eligibility_limit`, `bonus_monthly_calculation_ceiling`, `bonus_minimum_rate`, `bonus_maximum_rate`, `bonus_minimum_days`, `created_at`, `updated_at`) VALUES
(1, 12.00, 12.00, 25000.00, 0.75, 3.25, 21000.00, 176.00, 0.00, 0.00, 8.33, 20.00, 30, '2026-11-03 04:39:34', '2026-11-03 04:42:34');

-- --------------------------------------------------------

--
-- Table structure for table `service_logs`
--

CREATE TABLE `service_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_number` varchar(255) NOT NULL,
  `contract_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_type` varchar(255) NOT NULL,
  `service_at` datetime NOT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `distance_km` decimal(10,1) NOT NULL DEFAULT 0.0,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('b5mpX3aSlO3DmjBtxclwGCRAzs8vy9D0KQNmDZE5', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:160.0) Gecko/20100101 Firefox/160.0', 'eyJfdG9rZW4iOiJuTGhSZGY5aDdHeTZZRHR2bGluNlhoVlc0WEFGejN4TXRPemxFazdqIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3ZlaGljbGVzIiwicm91dGUiOiJyZXNvdXJjZXMuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiNWQwZjEzZjc1MmY0NDBjY2MyNjgwN2FiZWUzMDE2NGRkNmM0ZjYyMTlkZGRjODcwZTdjNjBiOGNkYjY1ZjVjYiJ9', 1794657347),
('GosxGFabhnBHcnPZfsaqrYKLXzKjbJNAvcsuAThN', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJEMVpEMFFrZ3QyeERRTnRkN29zUlo4Rm1MWkltejhkRlltSUk2b0hqIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiI1ZDBmMTNmNzUyZjQ0MGNjYzI2ODA3YWJlZTMwMTY0ZGQ2YzRmNjIxOWRkZGM4NzBlN2M2MGI4Y2RiNjVmNWNiIn0=', 1794651321),
('GyjAUGyxbZiTQxvpvjZkb0UdPVRb7kNrcjVWbESM', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJTR09acjZhSlVkT0tUaHNhQUFHdEV2Y1h2QnRySEFUVVRJbFpibE5xIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9zYWxhcnlcL2VtcGxveWVlc1wvMVwvcHJldmlldz9zYWxhcnlfbW9udGg9MjAyNi0xMSZ3b3JraW5nX2RheXM9MjAiLCJyb3V0ZSI6InNhbGFyeS5lbXBsb3llZS1wcmV2aWV3In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiNWQwZjEzZjc1MmY0NDBjY2MyNjgwN2FiZWUzMDE2NGRkNmM0ZjYyMTlkZGRjODcwZTdjNjBiOGNkYjY1ZjVjYiJ9', 1794641711);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Ashish', 'ashish@gmail.com', NULL, '$2y$12$d6c.YzpN/9xblIKupq0jPejg6PXvTtIyusoVbxVP/mAO1EL6FkzhK', 'super-admin', NULL, '2026-11-03 03:26:18', '2026-11-03 03:26:18'),
(2, 'Sumit Upadhyay', 'sumit@gmail.com', NULL, '$2y$12$46QooULrXq/lbqXFH6HVKe7FF6jy6hQJaeY7oVuhhSqALQDEUjOKm', 'admin', NULL, '2026-11-03 03:36:17', '2026-11-03 03:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `make_model` varchar(255) DEFAULT NULL,
  `capacity` varchar(255) DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `permit_expiry` date DEFAULT NULL,
  `fitness_expiry` date DEFAULT NULL,
  `odometer_km` decimal(12,1) NOT NULL DEFAULT 0.0,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `registration_number`, `service_type`, `make_model`, `capacity`, `insurance_expiry`, `permit_expiry`, `fitness_expiry`, `odometer_km`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'UP78JJ1966', 'ambulance', '2026', NULL, '2026-12-31', '2026-12-31', '2026-12-31', 2500.0, 'deployed', NULL, '2026-11-03 04:52:07', '2026-11-14 01:36:02'),
(2, 'UP78JX9870', 'cab', '2026', '7', '2026-11-18', '2026-11-18', '2026-12-31', 2000.0, 'available', NULL, '2026-11-14 05:42:40', '2026-11-14 06:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_expiry_reminders`
--

CREATE TABLE `vehicle_expiry_reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `expiry_type` varchar(20) NOT NULL,
  `expiry_date` date NOT NULL,
  `remind_on` date NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_expiry_reminders`
--

INSERT INTO `vehicle_expiry_reminders` (`id`, `user_id`, `vehicle_id`, `expiry_type`, `expiry_date`, `remind_on`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'insurance', '2026-11-18', '2026-11-15', NULL, '2026-11-14 06:03:20', '2026-11-14 06:14:55'),
(2, 1, 2, 'permit', '2026-11-18', '2026-11-15', NULL, '2026-11-14 06:03:20', '2026-11-14 06:14:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contracts_contract_number_unique` (`contract_number`),
  ADD KEY `contracts_government_client_id_foreign` (`government_client_id`),
  ADD KEY `contracts_service_type_status_index` (`service_type`,`status`);

--
-- Indexes for table `contract_deployments`
--
ALTER TABLE `contract_deployments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_deployments_employee_id_foreign` (`employee_id`),
  ADD KEY `contract_deployments_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `contract_deployments_contract_id_service_type_status_index` (`contract_id`,`service_type`,`status`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  ADD KEY `employees_role_status_index` (`role`,`status`);

--
-- Indexes for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_attendance_employee_id_attendance_date_unique` (`employee_id`,`attendance_date`),
  ADD KEY `employee_attendance_attendance_date_status_index` (`attendance_date`,`status`);

--
-- Indexes for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_salaries_employee_id_salary_month_unique` (`employee_id`,`salary_month`),
  ADD KEY `employee_salaries_created_by_foreign` (`created_by`),
  ADD KEY `employee_salaries_salary_month_employee_code_snapshot_index` (`salary_month`,`employee_code_snapshot`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `government_clients`
--
ALTER TABLE `government_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_contract_id_foreign` (`contract_id`),
  ADD KEY `invoices_invoice_date_status_index` (`invoice_date`,`status`);

--
-- Indexes for table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_payments_invoice_id_payment_date_index` (`invoice_id`,`payment_date`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `salary_rule_settings`
--
ALTER TABLE `salary_rule_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_logs`
--
ALTER TABLE `service_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_logs_log_number_unique` (`log_number`),
  ADD KEY `service_logs_contract_id_foreign` (`contract_id`),
  ADD KEY `service_logs_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `service_logs_employee_id_foreign` (`employee_id`),
  ADD KEY `service_logs_service_at_service_type_status_index` (`service_at`,`service_type`,`status`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_registration_number_unique` (`registration_number`),
  ADD KEY `vehicles_service_type_status_index` (`service_type`,`status`);

--
-- Indexes for table `vehicle_expiry_reminders`
--
ALTER TABLE `vehicle_expiry_reminders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_expiry_reminders_unique` (`user_id`,`vehicle_id`,`expiry_type`,`expiry_date`),
  ADD KEY `vehicle_expiry_reminders_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `vehicle_expiry_reminders_due_idx` (`user_id`,`closed_at`,`remind_on`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contract_deployments`
--
ALTER TABLE `contract_deployments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `government_clients`
--
ALTER TABLE `government_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `salary_rule_settings`
--
ALTER TABLE `salary_rule_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `service_logs`
--
ALTER TABLE `service_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicle_expiry_reminders`
--
ALTER TABLE `vehicle_expiry_reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_government_client_id_foreign` FOREIGN KEY (`government_client_id`) REFERENCES `government_clients` (`id`);

--
-- Constraints for table `contract_deployments`
--
ALTER TABLE `contract_deployments`
  ADD CONSTRAINT `contract_deployments_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_deployments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_deployments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_attendance`
--
ALTER TABLE `employee_attendance`
  ADD CONSTRAINT `employee_attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD CONSTRAINT `employee_salaries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_salaries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`);

--
-- Constraints for table `invoice_payments`
--
ALTER TABLE `invoice_payments`
  ADD CONSTRAINT `invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_logs`
--
ALTER TABLE `service_logs`
  ADD CONSTRAINT `service_logs_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`),
  ADD CONSTRAINT `service_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_logs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicle_expiry_reminders`
--
ALTER TABLE `vehicle_expiry_reminders`
  ADD CONSTRAINT `vehicle_expiry_reminders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_expiry_reminders_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 26, 2025 at 04:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `usg_accreditation`
--

-- --------------------------------------------------------

--
-- Table structure for table `accomplishment_reports`
--

CREATE TABLE `accomplishment_reports` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `activity_title` varchar(255) NOT NULL,
  `narrative_report` text NOT NULL,
  `pictorials` text DEFAULT NULL COMMENT 'JSON array of image paths',
  `activity_designs` text DEFAULT NULL COMMENT 'JSON array of design file paths',
  `evaluation_sheets` text DEFAULT NULL COMMENT 'JSON array of evaluation sheet paths',
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_activities`
--

CREATE TABLE `calendar_activities` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `activity_date` date NOT NULL,
  `activity_title` varchar(255) NOT NULL,
  `responsible_person` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('planned','ongoing','completed','cancelled') NOT NULL DEFAULT 'planned',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendar_activities`
--

INSERT INTO `calendar_activities` (`id`, `organization_id`, `academic_year`, `activity_date`, `activity_title`, `responsible_person`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-2026', '2025-12-18', 'Intramurals', 'Steve', 'Sports', 'planned', '2025-12-17 13:47:05', '2025-12-17 13:47:05'),
(4, 6, '2025-2026', '2025-12-01', 'Kambuniyan', 'PPPPP', 'Uni-wide event', 'planned', '2025-12-18 05:53:40', '2025-12-18 05:53:40'),
(5, 6, '2025-2026', '2025-12-18', 'Defense', 'Ma\'am Remegio', 'Defense', 'ongoing', '2025-12-18 05:54:57', '2025-12-18 05:54:57'),
(6, 6, '2025-2026', '2025-12-19', 'holiday', 'l', 'l', 'planned', '2025-12-18 06:22:36', '2025-12-18 06:22:36'),
(7, 4, '2025-2026', '2025-12-25', 'Christmas Party', 'Sir Rael', 'ANY', 'planned', '2025-12-18 08:05:53', '2025-12-18 08:05:53');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_activity_signatories`
--

CREATE TABLE `calendar_activity_signatories` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `head_name` varchar(255) DEFAULT NULL,
  `adviser_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendar_activity_signatories`
--

INSERT INTO `calendar_activity_signatories` (`id`, `organization_id`, `academic_year`, `head_name`, `adviser_name`, `created_at`, `updated_at`) VALUES
(1, 6, '2025-2026', 'lol', 'lol', '2025-12-18 06:22:36', '2025-12-18 06:22:36'),
(2, 4, '2025-2026', 'Steve', 'Sir Rael', '2025-12-18 08:05:53', '2025-12-18 08:05:53');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `document_id`, `user_id`, `comment`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'It gud', '2025-12-17 14:58:34', '2025-12-17 14:58:34'),
(2, 3, 1, 'Where is the your summary of collections?', '2025-12-18 05:44:50', '2025-12-18 05:44:50'),
(3, 3, 1, 'lol', '2025-12-18 06:37:26', '2025-12-18 06:37:26'),
(4, 3, 1, 'There is no Summary of Expenses', '2025-12-18 08:02:46', '2025-12-18 08:02:46'),
(5, 4, 1, 'There are a bunch of discrepencies', '2025-12-18 08:09:54', '2025-12-18 08:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `commitment_forms`
--

CREATE TABLE `commitment_forms` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `officer_name` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `signed_date` date NOT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commitment_forms`
--

INSERT INTO `commitment_forms` (`id`, `organization_id`, `officer_name`, `position`, `organization_name`, `academic_year`, `signed_date`, `signature`, `status`, `created_at`, `updated_at`) VALUES
(2, 4, 'Steve', 'Treasurer', 'Computer Studies Student Organization', '2025-2026', '2025-10-11', NULL, 'draft', '2025-12-18 08:04:56', '2025-12-18 08:04:56');

-- --------------------------------------------------------

--
-- Table structure for table `document_review_history`
--

CREATE TABLE `document_review_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `from_status` varchar(50) DEFAULT NULL,
  `to_status` varchar(50) DEFAULT NULL,
  `comment_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_review_history`
--

INSERT INTO `document_review_history` (`id`, `document_id`, `user_id`, `action`, `from_status`, `to_status`, `comment_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'comment', NULL, NULL, 1, '2025-12-17 14:58:34', '2025-12-17 14:58:34'),
(2, 1, 1, 'status_change', 'pending', 'rejected', NULL, '2025-12-18 05:26:44', '2025-12-18 05:26:44'),
(3, 3, 1, 'comment', NULL, NULL, 2, '2025-12-18 05:44:50', '2025-12-18 05:44:50'),
(4, 3, 1, 'status_change', 'pending', 'approved', NULL, '2025-12-18 05:45:43', '2025-12-18 05:45:43'),
(5, 3, 1, 'comment', NULL, NULL, 3, '2025-12-18 06:37:26', '2025-12-18 06:37:26'),
(6, 3, 1, 'comment', NULL, NULL, 4, '2025-12-18 08:02:46', '2025-12-18 08:02:46'),
(7, 4, 1, 'status_change', 'pending', 'rejected', NULL, '2025-12-18 08:09:31', '2025-12-18 08:09:31'),
(8, 4, 1, 'comment', NULL, NULL, 5, '2025-12-18 08:09:54', '2025-12-18 08:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `document_submissions`
--

CREATE TABLE `document_submissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `document_type` enum('commitment_form','calendar_activities','program_expenditure','accomplishment_report','financial_report','other') NOT NULL,
  `document_title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','reviewed','approved','rejected') NOT NULL DEFAULT 'pending',
  `submitted_by` int(11) UNSIGNED NOT NULL,
  `reviewed_by` int(11) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_submissions`
--

INSERT INTO `document_submissions` (`id`, `organization_id`, `document_type`, `document_title`, `file_path`, `file_name`, `file_type`, `file_size`, `academic_year`, `description`, `status`, `submitted_by`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'commitment_form', 'Commitement Form', 'documents/4/1765977425_f21d7e2b165b69164228.docx', 'Other_Req.docx', 'application/vnd.openxmlformats-officedocument.word', 311007, '2025-2026', 'nothing', 'rejected', 5, 1, '2025-12-18 05:26:44', '2025-12-17 13:17:05', '2025-12-18 05:26:44'),
(2, 4, 'financial_report', 'Financial Report Q1', 'documents/4/1765978036_6fcc949a5ec933b26ac0.docx', 'Final Format of Financial Report.docx', 'application/vnd.openxmlformats-officedocument.word', 10458240, '2025-2026', 'Report', 'approved', 5, 1, '2025-12-17 13:43:26', '2025-12-17 13:27:16', '2025-12-17 13:43:26'),
(3, 6, 'financial_report', 'Financial Report Q1', 'documents/6/financial_report/2024-2025/1766036633_4286c7bfc36e302940cd.docx', 'Final Format of Financial Report.docx', 'application/vnd.openxmlformats-officedocument.word', 10458240, '2024-2025', 'Report for the financials of PSITS in S.Y 2024-2025', 'approved', 7, 1, '2025-12-18 05:45:43', '2025-12-18 05:43:53', '2025-12-18 05:45:43'),
(4, 4, 'financial_report', 'Financial Report Q2', 'documents/4/financial_report/2025-2026/1766045043_70227117261030c19dbf.docx', 'Final Format of Financial Report.docx', 'application/vnd.openxmlformats-officedocument.word', 10458240, '2025-2026', 'Anything', 'rejected', 5, 1, '2025-12-18 08:09:31', '2025-12-18 08:04:03', '2025-12-18 08:09:31'),
(5, 4, 'other', 'Other Requirements', 'documents/4/other/2025-2026/1766045347_8b2523bb8889660f99cd.docx', 'Other_Req.docx', 'application/vnd.openxmlformats-officedocument.word', 311007, '2025-2026', 'From Financial to Program Expenditures', 'pending', 5, NULL, NULL, '2025-12-18 08:09:07', '2025-12-18 08:09:07'),
(7, 4, 'other', '111', 'documents/4/other/2025-2026/1766755009_ffbe8dc0cc41e8ad276f.pdf', 'Technical Report.pdf', 'application/pdf', 670860, '2025-2026', '123', 'pending', 5, NULL, NULL, '2025-12-26 13:16:49', '2025-12-26 13:16:49'),
(8, 4, 'other', 'aa1', 'documents/4/other/2025-2026/1766759916_3747a170128b9f731870.pdf', 'Preview Test LMAO.pdf', 'application/pdf', 32353, '2025-2026', '3', 'pending', 5, NULL, NULL, '2025-12-26 14:38:36', '2025-12-26 14:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `financial_reports`
--

CREATE TABLE `financial_reports` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `collections` text NOT NULL COMMENT 'JSON data for collection types and amounts',
  `expenses` text NOT NULL COMMENT 'JSON data for activities/projects and expenses',
  `total_collection` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_expenses` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cash_on_bank` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cash_on_hand` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_remaining_fund` decimal(15,2) NOT NULL DEFAULT 0.00,
  `passbook_copy` varchar(255) DEFAULT NULL,
  `treasurer_name` varchar(255) DEFAULT NULL,
  `auditor_name` varchar(255) DEFAULT NULL,
  `head_name` varchar(255) DEFAULT NULL,
  `adviser_name` varchar(255) DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_reports`
--

INSERT INTO `financial_reports` (`id`, `organization_id`, `academic_year`, `collections`, `expenses`, `total_collection`, `total_expenses`, `cash_on_bank`, `cash_on_hand`, `total_remaining_fund`, `passbook_copy`, `treasurer_name`, `auditor_name`, `head_name`, `adviser_name`, `status`, `created_at`, `updated_at`) VALUES
(2, 4, '2024-2025', '[{\"type\":\"Lmao\",\"amount\":\"123900\"}]', '[{\"activity\":\"Intramurals\",\"amount\":\"110000\"}]', 123900.00, 110000.00, 2335.00, 11565.00, 13900.00, 'passbooks/4/1765984807_5c722de859477b94cf87.png', 'll', 'll', 'll', 'll', 'draft', '2025-12-17 15:20:07', '2025-12-17 15:20:07'),
(3, 6, '2024-2025', '[{\"type\":\"1st semester collection (Membership Fee & Fines)\",\"amount\":\"153000\"},{\"type\":\"PSITS Regional Convention\",\"amount\":\"125000\"}]', '[{\"activity\":\"Office Supplies\",\"amount\":\"33000\"}]', 278000.00, 33000.00, 25000.00, 220000.00, 245000.00, 'passbooks/6/1766037052_c0ca55721133da53c081.png', 'Jemark Tucayao', 'Justine Carl Rosas', 'Aejer Theranz Balayon', 'Ken Chester Felongco', 'draft', '2025-12-18 05:50:52', '2025-12-18 06:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1765973730, 1),
(2, '2024-01-01-000002', 'App\\Database\\Migrations\\CreateOrganizationsTable', 'default', 'App', 1765973730, 1),
(3, '2024-01-01-000003', 'App\\Database\\Migrations\\CreateCommitmentFormsTable', 'default', 'App', 1765973730, 1),
(4, '2024-01-01-000004', 'App\\Database\\Migrations\\CreateCalendarActivitiesTable', 'default', 'App', 1765973730, 1),
(5, '2024-01-01-000005', 'App\\Database\\Migrations\\CreateProgramExpendituresTable', 'default', 'App', 1765973730, 1),
(6, '2024-01-01-000006', 'App\\Database\\Migrations\\CreateAccomplishmentReportsTable', 'default', 'App', 1765973730, 1),
(7, '2024-01-01-000007', 'App\\Database\\Migrations\\CreateFinancialReportsTable', 'default', 'App', 1765973730, 1),
(8, '2024-01-01-000008', 'App\\Database\\Migrations\\CreateDocumentSubmissionsTable', 'default', 'App', 1765973730, 1),
(9, '2024-01-01-000009', 'App\\Database\\Migrations\\CreateCommentsTable', 'default', 'App', 1765973730, 1),
(10, '2024-01-01-000010', 'App\\Database\\Migrations\\CreateNotificationsTable', 'default', 'App', 1765973730, 1),
(11, '2024-01-01-000011', 'App\\Database\\Migrations\\AddForeignKeys', 'default', 'App', 1765973730, 1),
(12, '2024-01-01-000012', 'App\\Database\\Migrations\\CreateCalendarActivitySignatoriesTable', 'default', 'App', 1766038861, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `related_id` int(11) DEFAULT NULL COMMENT 'ID of related document or comment',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `related_id`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 5, 'New Comment on Document', 'Admin has commented on your submitted document: Financial Report Q1', 'comment', 2, 1, '2025-12-17 14:58:34', '2025-12-18 05:29:02'),
(2, 5, 'Document Status Updated', 'Admin updated the status of your submitted document \"Commitement Form\" to: Rejected', 'status', 1, 1, '2025-12-18 05:26:44', '2025-12-18 08:06:54'),
(3, 7, 'New Comment on Document', 'Admin has commented on your submitted document: Financial Report Q1', 'comment', 3, 1, '2025-12-18 05:44:50', '2025-12-18 05:45:11'),
(4, 7, 'Document Status Updated', 'Admin updated the status of your submitted document \"Financial Report Q1\" to: Approved', 'status', 3, 1, '2025-12-18 05:45:43', '2025-12-18 06:24:46'),
(5, 7, 'New Comment on Document', 'Admin has commented on your submitted document: Financial Report Q1', 'comment', 3, 1, '2025-12-18 06:37:26', '2025-12-18 06:37:43'),
(6, 7, 'New Comment on Document', 'Admin has commented on your submitted document: Financial Report Q1', 'comment', 3, 0, '2025-12-18 08:02:46', '2025-12-18 08:02:46'),
(7, 5, 'Document Status Updated', 'Admin updated the status of your submitted document \"Financial Report Q2\" to: Rejected', 'status', 4, 1, '2025-12-18 08:09:31', '2025-12-18 08:10:09'),
(8, 5, 'New Comment on Document', 'Admin has commented on your submitted document: Financial Report Q2', 'comment', 4, 1, '2025-12-18 08:09:54', '2025-12-26 12:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `acronym` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `acronym`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Student Council', 'SC', 'Main student governing body of the university', 'active', '2025-12-17 20:22:13', '2025-12-17 20:22:13'),
(4, 'Computer Studies Student Organization', 'CSSO', 'Based Org', 'active', '2025-12-17 13:15:55', '2025-12-17 13:15:55'),
(6, 'PHILIPPINES SOCIETY OF INFORMATION TECHNOLOGY STUDENTS', 'PSITS', 'Best org', 'active', '2025-12-18 05:36:51', '2025-12-18 05:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `program_expenditures`
--

CREATE TABLE `program_expenditures` (
  `id` int(11) UNSIGNED NOT NULL,
  `organization_id` int(11) UNSIGNED NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `fee_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `number_of_students` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_expenditures`
--

INSERT INTO `program_expenditures` (`id`, `organization_id`, `academic_year`, `fee_type`, `amount`, `frequency`, `number_of_students`, `total`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-2026', 'Membership Fee', 50.00, 'Once', 1200, 60000.00, '2025-12-17 13:44:36', '2025-12-17 13:44:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','organization') NOT NULL DEFAULT 'organization',
  `organization_id` int(11) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `organization_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1, '2025-12-26 14:12:24', '2025-12-17 20:22:13', '2025-12-26 14:12:24'),
(2, 'sc_user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'organization', 1, 1, NULL, '2025-12-17 20:22:13', '2025-12-17 20:22:13'),
(3, 'labatis@gmail123', '$2y$10$dscqxYgAfp4EY/ZH.fSFp.gjhFbPaiNGKwDw0u.1DZNSNoHHd/Jcq', 'organization', NULL, 1, NULL, '2025-12-17 12:47:16', '2025-12-17 15:51:04'),
(4, 'org_sample', '$2y$10$wHj1xX8QeH4D6g3Dqjv7eOQdBv1qZ3zq6zR9VJv3r0KpYqfW8sG0u', 'organization', NULL, 1, NULL, '2025-12-17 21:13:10', '2025-12-17 21:13:10'),
(5, 'Steve', '$2y$10$UDzddaY3dzOyhTn53DoPFuHFv2olr5/n9K0MBrmQinJvj15TBYXaC', 'organization', 4, 1, '2025-12-26 12:44:13', '2025-12-17 13:15:55', '2025-12-26 12:44:13'),
(6, 'KristalKaye', '$2y$10$puw9stWtrJMyyPNP95mCT.vBw9fK9w3dqjgwDzWfYO/6gjJS5Kmve', 'organization', NULL, 1, '2025-12-17 13:54:48', '2025-12-17 13:54:34', '2025-12-17 13:54:48'),
(7, 'RanzB', '$2y$10$uQYiwdij1cYQSLlmwdkRFOfIzs9oHkERU/T0KL6.aLNMQc6.3mOKS', 'organization', 6, 1, '2025-12-18 07:07:42', '2025-12-18 05:36:51', '2025-12-18 07:07:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accomplishment_reports`
--
ALTER TABLE `accomplishment_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `calendar_activities`
--
ALTER TABLE `calendar_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `calendar_activity_signatories`
--
ALTER TABLE `calendar_activity_signatories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `org_year_unique` (`organization_id`,`academic_year`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `academic_year` (`academic_year`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `commitment_forms`
--
ALTER TABLE `commitment_forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `document_review_history`
--
ALTER TABLE `document_review_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doc` (`document_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_comment` (`comment_id`);

--
-- Indexes for table `document_submissions`
--
ALTER TABLE `document_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_submissions_submitted_by_foreign` (`submitted_by`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `academic_year` (`academic_year`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_expenditures`
--
ALTER TABLE `program_expenditures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `users_organization_id_foreign` (`organization_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accomplishment_reports`
--
ALTER TABLE `accomplishment_reports`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calendar_activities`
--
ALTER TABLE `calendar_activities`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `calendar_activity_signatories`
--
ALTER TABLE `calendar_activity_signatories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `commitment_forms`
--
ALTER TABLE `commitment_forms`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `document_review_history`
--
ALTER TABLE `document_review_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `document_submissions`
--
ALTER TABLE `document_submissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `program_expenditures`
--
ALTER TABLE `program_expenditures`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accomplishment_reports`
--
ALTER TABLE `accomplishment_reports`
  ADD CONSTRAINT `accomplishment_reports_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `calendar_activities`
--
ALTER TABLE `calendar_activities`
  ADD CONSTRAINT `calendar_activities_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `calendar_activity_signatories`
--
ALTER TABLE `calendar_activity_signatories`
  ADD CONSTRAINT `calendar_activity_signatories_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `document_submissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `commitment_forms`
--
ALTER TABLE `commitment_forms`
  ADD CONSTRAINT `commitment_forms_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_review_history`
--
ALTER TABLE `document_review_history`
  ADD CONSTRAINT `fk_history_comment` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_history_document` FOREIGN KEY (`document_id`) REFERENCES `document_submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_submissions`
--
ALTER TABLE `document_submissions`
  ADD CONSTRAINT `document_submissions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_submissions_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD CONSTRAINT `financial_reports_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `program_expenditures`
--
ALTER TABLE `program_expenditures`
  ADD CONSTRAINT `program_expenditures_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

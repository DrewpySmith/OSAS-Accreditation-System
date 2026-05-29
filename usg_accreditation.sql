-- USG Accreditation System - SQLite Schema
-- Generated: 2026-05-29 18:44:55

-- Table: academic_years
CREATE TABLE `academic_years` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`year` VARCHAR NOT NULL,
	`start_date` DATE NOT NULL,
	`end_date` DATE NOT NULL,
	`is_current` TINYINT NOT NULL DEFAULT 0,
	`is_active` TINYINT NOT NULL DEFAULT 1,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL
);

-- Table: accomplishment_reports
CREATE TABLE `accomplishment_reports` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`activity_title` VARCHAR NOT NULL,
	`narrative_report` TEXT NOT NULL,
	`pictorials` TEXT NULL,
	`activity_designs` TEXT NULL,
	`evaluation_sheets` TEXT NULL,
	`status` TEXT CHECK(`status` IN ('draft','submitted','approved','rejected')) NOT NULL DEFAULT 'draft',
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `accomplishment_reports_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: announcement_reads
CREATE TABLE `announcement_reads` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`announcement_id` INT NOT NULL,
	`organization_id` INT NOT NULL,
	`read_at` DATETIME NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `announcement_reads_announcement_id_foreign` FOREIGN KEY (`announcement_id`) REFERENCES `announcements`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `announcement_reads_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: announcements
CREATE TABLE `announcements` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`title` VARCHAR NOT NULL,
	`message` TEXT NOT NULL,
	`sender_id` INT NOT NULL,
	`target_type` VARCHAR NOT NULL DEFAULT 'all',
	`target_value` VARCHAR NULL,
	`is_active` TINYINT NOT NULL DEFAULT 1,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL
);

-- Table: calendar_activities
CREATE TABLE `calendar_activities` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`activity_date` DATE NOT NULL,
	`activity_title` VARCHAR NOT NULL,
	`responsible_person` VARCHAR NOT NULL,
	`remarks` TEXT NULL,
	`status` TEXT CHECK(`status` IN ('planned','ongoing','completed','cancelled')) NOT NULL DEFAULT 'planned',
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `calendar_activities_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: calendar_activity_signatories
CREATE TABLE `calendar_activity_signatories` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`head_name` VARCHAR NULL,
	`adviser_name` VARCHAR NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `calendar_activity_signatories_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: chat_messages
CREATE TABLE `chat_messages` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`sender_id` INT NOT NULL,
	`receiver_org_id` INT NOT NULL,
	`message` TEXT NOT NULL,
	`is_read` TINYINT NOT NULL DEFAULT 0,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `chat_messages_receiver_org_id_foreign` FOREIGN KEY (`receiver_org_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: comments
CREATE TABLE `comments` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`document_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`comment` TEXT NOT NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `comments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `document_submissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: commitment_forms
CREATE TABLE `commitment_forms` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`officer_name` VARCHAR NOT NULL,
	`position` VARCHAR NOT NULL,
	`organization_name` VARCHAR NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`signed_date` DATE NOT NULL,
	`signature` VARCHAR NULL,
	`status` TEXT CHECK(`status` IN ('draft','submitted','approved','rejected')) NOT NULL DEFAULT 'draft',
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `commitment_forms_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: document_submissions
CREATE TABLE `document_submissions` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`document_type` TEXT CHECK(`document_type` IN ('commitment_form','calendar_activities','program_expenditure','accomplishment_report','financial_report','other')) NOT NULL,
	`document_title` VARCHAR NOT NULL,
	`file_path` VARCHAR NOT NULL,
	`file_name` VARCHAR NOT NULL,
	`file_type` VARCHAR NOT NULL,
	`file_size` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`description` TEXT NULL,
	`status` TEXT CHECK(`status` IN ('pending','reviewed','approved','rejected')) NOT NULL DEFAULT 'pending',
	`submitted_by` INT NOT NULL,
	`reviewed_by` INT NULL,
	`reviewed_at` DATETIME NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL, `campus` VARCHAR NULL,
	CONSTRAINT `document_submissions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `document_submissions_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: financial_reports
CREATE TABLE `financial_reports` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`collections` TEXT NOT NULL,
	`expenses` TEXT NOT NULL,
	`total_collection` DECIMAL NOT NULL DEFAULT 0,
	`total_expenses` DECIMAL NOT NULL DEFAULT 0,
	`cash_on_bank` DECIMAL NOT NULL DEFAULT 0,
	`cash_on_hand` DECIMAL NOT NULL DEFAULT 0,
	`total_remaining_fund` DECIMAL NOT NULL DEFAULT 0,
	`passbook_copy` VARCHAR NULL,
	`treasurer_name` VARCHAR NULL,
	`auditor_name` VARCHAR NULL,
	`head_name` VARCHAR NULL,
	`adviser_name` VARCHAR NULL,
	`status` TEXT CHECK(`status` IN ('draft','submitted','approved','rejected')) NOT NULL DEFAULT 'draft',
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `financial_reports_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: migrations
CREATE TABLE `migrations` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`version` VARCHAR NOT NULL,
	`class` VARCHAR NOT NULL,
	`group` VARCHAR NOT NULL,
	`namespace` VARCHAR NOT NULL,
	`time` INT NOT NULL,
	`batch` INT NOT NULL
);

-- Table: notifications
CREATE TABLE `notifications` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`user_id` INT NOT NULL,
	`title` VARCHAR NOT NULL,
	`message` TEXT NOT NULL,
	`type` VARCHAR NOT NULL,
	`related_id` INT NULL,
	`is_read` TINYINT NOT NULL DEFAULT 0,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: organization_checklists
CREATE TABLE `organization_checklists` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`application_letter` TINYINT NOT NULL DEFAULT 0,
	`officer_list` TINYINT NOT NULL DEFAULT 0,
	`commitment_forms` TINYINT NOT NULL DEFAULT 0,
	`constitution_bylaws` TINYINT NOT NULL DEFAULT 0,
	`org_structure` TINYINT NOT NULL DEFAULT 0,
	`calendar_activities` TINYINT NOT NULL DEFAULT 0,
	`financial_report` TINYINT NOT NULL DEFAULT 0,
	`program_expenditures` TINYINT NOT NULL DEFAULT 0,
	`accomplishment_report` TINYINT NOT NULL DEFAULT 0,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `organization_checklists_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: organization_registrations
CREATE TABLE `organization_registrations` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`name` VARCHAR NOT NULL,
	`acronym` VARCHAR NULL,
	`campus` VARCHAR NOT NULL,
	`description` TEXT NULL,
	`officer_email` VARCHAR NOT NULL,
	`officer_password` VARCHAR NOT NULL,
	`adviser_name` VARCHAR NOT NULL,
	`adviser_email` VARCHAR NOT NULL,
	`adviser_token` VARCHAR NULL,
	`signature_path` VARCHAR NULL,
	`signed_at` DATETIME NULL,
	`status` TEXT CHECK(`status` IN ('pending_adviser','pending_admin','rejected')) NOT NULL DEFAULT 'pending_adviser',
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL
);

-- Table: organizations
CREATE TABLE `organizations` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`name` VARCHAR NOT NULL,
	`acronym` VARCHAR NULL,
	`description` TEXT NULL,
	`status` TEXT CHECK(`status` IN ('active','inactive','suspended')) NOT NULL DEFAULT 'active',
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL
, `campus` VARCHAR NULL);

-- Table: password_resets
CREATE TABLE `password_resets` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`email` VARCHAR NOT NULL,
	`token` VARCHAR NOT NULL,
	`expires_at` DATETIME NOT NULL,
	`created_at` DATETIME NULL
);

-- Table: program_expenditures
CREATE TABLE `program_expenditures` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`organization_id` INT NOT NULL,
	`academic_year` VARCHAR NOT NULL,
	`fee_type` VARCHAR NOT NULL,
	`amount` DECIMAL NOT NULL,
	`frequency` VARCHAR NOT NULL,
	`number_of_students` INT NOT NULL,
	`total` DECIMAL NOT NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL,
	CONSTRAINT `program_expenditures_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: system_settings
CREATE TABLE `system_settings` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`setting_key` VARCHAR NOT NULL,
	`setting_value` TEXT NULL,
	`setting_type` VARCHAR NOT NULL DEFAULT 'text',
	`description` TEXT NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL
);

-- Table: users
CREATE TABLE `users` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`username` VARCHAR NOT NULL UNIQUE,
	`password` VARCHAR NOT NULL,
	`role` TEXT CHECK(`role` IN ('admin','organization')) NOT NULL DEFAULT 'organization',
	`organization_id` INT NULL,
	`is_active` TINYINT NOT NULL DEFAULT 1,
	`last_login` DATETIME NULL,
	`created_at` DATETIME NULL,
	`updated_at` DATETIME NULL
);

-- Index: academic_years_year
CREATE UNIQUE INDEX `academic_years_year` ON `academic_years` (`year`);

-- Index: accomplishment_reports_organization_id
CREATE INDEX `accomplishment_reports_organization_id` ON `accomplishment_reports` (`organization_id`);

-- Index: announcement_org_unique
CREATE UNIQUE INDEX `announcement_org_unique` ON `announcement_reads` (`announcement_id`, `organization_id`);

-- Index: announcement_reads_organization_id
CREATE INDEX `announcement_reads_organization_id` ON `announcement_reads` (`organization_id`);

-- Index: announcements_target_type
CREATE INDEX `announcements_target_type` ON `announcements` (`target_type`);

-- Index: calendar_activities_organization_id
CREATE INDEX `calendar_activities_organization_id` ON `calendar_activities` (`organization_id`);

-- Index: calendar_activity_signatories_academic_year
CREATE INDEX `calendar_activity_signatories_academic_year` ON `calendar_activity_signatories` (`academic_year`);

-- Index: calendar_activity_signatories_organization_id
CREATE INDEX `calendar_activity_signatories_organization_id` ON `calendar_activity_signatories` (`organization_id`);

-- Index: chat_messages_receiver_org_id
CREATE INDEX `chat_messages_receiver_org_id` ON `chat_messages` (`receiver_org_id`);

-- Index: chat_messages_sender_id
CREATE INDEX `chat_messages_sender_id` ON `chat_messages` (`sender_id`);

-- Index: comments_document_id
CREATE INDEX `comments_document_id` ON `comments` (`document_id`);

-- Index: commitment_forms_organization_id
CREATE INDEX `commitment_forms_organization_id` ON `commitment_forms` (`organization_id`);

-- Index: document_submissions_organization_id
CREATE INDEX `document_submissions_organization_id` ON `document_submissions` (`organization_id`);

-- Index: financial_reports_academic_year
CREATE INDEX `financial_reports_academic_year` ON `financial_reports` (`academic_year`);

-- Index: financial_reports_organization_id
CREATE INDEX `financial_reports_organization_id` ON `financial_reports` (`organization_id`);

-- Index: notifications_user_id
CREATE INDEX `notifications_user_id` ON `notifications` (`user_id`);

-- Index: org_year_unique
CREATE UNIQUE INDEX `org_year_unique` ON `calendar_activity_signatories` (`organization_id`, `academic_year`);

-- Index: organization_checklists_organization_id_academic_year
CREATE UNIQUE INDEX `organization_checklists_organization_id_academic_year` ON `organization_checklists` (`organization_id`, `academic_year`);

-- Index: organization_registrations_adviser_token
CREATE INDEX `organization_registrations_adviser_token` ON `organization_registrations` (`adviser_token`);

-- Index: password_resets_email
CREATE INDEX `password_resets_email` ON `password_resets` (`email`);

-- Index: program_expenditures_organization_id
CREATE INDEX `program_expenditures_organization_id` ON `program_expenditures` (`organization_id`);

-- Index: system_settings_setting_key
CREATE UNIQUE INDEX `system_settings_setting_key` ON `system_settings` (`setting_key`);


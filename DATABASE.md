# USG Accreditation System - Database Schema

This file contains all SQL queries needed to set up the database structure for the USG Accreditation Management System.

## Database Creation

```sql
-- Create the main database
CREATE DATABASE IF NOT EXISTS `usg_accreditation` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `usg_accreditation`;
```

## Users Table

```sql
-- Users table for authentication and user management
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('admin','organization') NOT NULL DEFAULT 'organization',
    `first_name` varchar(100) DEFAULT NULL,
    `last_name` varchar(100) DEFAULT NULL,
    `organization_id` int(11) DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `last_login` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`),
    KEY `role` (`role`),
    KEY `organization_id` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Organizations Table

```sql
-- Organizations table for managing student organizations
CREATE TABLE `organizations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `acronym` varchar(50) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `contact_person` varchar(255) DEFAULT NULL,
    `contact_email` varchar(255) DEFAULT NULL,
    `contact_phone` varchar(50) DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Document Submissions Table

```sql
-- Document submissions table for tracking uploaded documents
CREATE TABLE `document_submissions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organization_id` int(11) NOT NULL,
    `document_type` varchar(100) NOT NULL,
    `document_title` varchar(255) NOT NULL,
    `file_path` varchar(500) NOT NULL,
    `file_name` varchar(255) NOT NULL,
    `file_type` varchar(100) DEFAULT NULL,
    `file_size` bigint(20) NOT NULL DEFAULT 0,
    `academic_year` varchar(20) NOT NULL,
    `description` text DEFAULT NULL,
    `status` enum('pending','reviewed','approved','rejected') NOT NULL DEFAULT 'pending',
    `submitted_by` int(11) NOT NULL,
    `reviewed_by` int(11) DEFAULT NULL,
    `reviewed_at` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `organization_id` (`organization_id`),
    KEY `document_type` (`document_type`),
    KEY `status` (`status`),
    KEY `academic_year` (`academic_year`),
    KEY `submitted_by` (`submitted_by`),
    KEY `reviewed_by` (`reviewed_by`),
    CONSTRAINT `document_submissions_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_submissions_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_submissions_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Comments Table

```sql
-- Comments table for document feedback
CREATE TABLE `comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `document_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `comment` text NOT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `document_id` (`document_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document_submissions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Financial Reports Table

```sql
-- Financial reports table for tracking organization finances
CREATE TABLE `financial_reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organization_id` int(11) NOT NULL,
    `academic_year` varchar(20) NOT NULL,
    `total_collection` decimal(15,2) NOT NULL DEFAULT 0.00,
    `total_expenses` decimal(15,2) NOT NULL DEFAULT 0.00,
    `total_remaining_fund` decimal(15,2) NOT NULL DEFAULT 0.00,
    `file_path` varchar(500) DEFAULT NULL,
    `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
    `submitted_by` int(11) DEFAULT NULL,
    `approved_by` int(11) DEFAULT NULL,
    `approved_at` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `org_year` (`organization_id`,`academic_year`),
    KEY `academic_year` (`academic_year`),
    KEY `status` (`status`),
    KEY `submitted_by` (`submitted_by`),
    KEY `approved_by` (`approved_by`),
    CONSTRAINT `financial_reports_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `financial_reports_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `financial_reports_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Program Expenditures Table

```sql
-- Program expenditures table for tracking expense details
CREATE TABLE `program_expenditures` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organization_id` int(11) NOT NULL,
    `academic_year` varchar(20) NOT NULL,
    `fee_type` varchar(100) NOT NULL,
    `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `frequency` varchar(50) NOT NULL,
    `number_of_students` int(11) NOT NULL DEFAULT 0,
    `total` decimal(15,2) NOT NULL DEFAULT 0.00,
    `description` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `organization_id` (`organization_id`),
    KEY `academic_year` (`academic_year`),
    KEY `fee_type` (`fee_type`),
    CONSTRAINT `program_expenditures_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Calendar Activities Table

```sql
-- Calendar activities table for tracking organizational events
CREATE TABLE `calendar_activities` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organization_id` int(11) NOT NULL,
    `academic_year` varchar(20) NOT NULL,
    `activity_date` date NOT NULL,
    `activity_title` varchar(255) NOT NULL,
    `responsible_person` varchar(255) NOT NULL,
    `remarks` text DEFAULT NULL,
    `status` enum('planned','ongoing','completed','cancelled') NOT NULL DEFAULT 'planned',
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `organization_id` (`organization_id`),
    KEY `academic_year` (`academic_year`),
    KEY `activity_date` (`activity_date`),
    KEY `status` (`status`),
    CONSTRAINT `calendar_activities_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Commitment Forms Table

```sql
-- Commitment forms table for tracking organizational commitments
CREATE TABLE `commitment_forms` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organization_id` int(11) NOT NULL,
    `academic_year` varchar(20) NOT NULL,
    `president_name` varchar(255) NOT NULL,
    `president_signature` varchar(500) DEFAULT NULL,
    `adviser_name` varchar(255) NOT NULL,
    `adviser_signature` varchar(500) DEFAULT NULL,
    `total_members` int(11) NOT NULL DEFAULT 0,
    `total_activities_planned` int(11) NOT NULL DEFAULT 0,
    `file_path` varchar(500) DEFAULT NULL,
    `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
    `submitted_by` int(11) DEFAULT NULL,
    `approved_by` int(11) DEFAULT NULL,
    `approved_at` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `org_year` (`organization_id`,`academic_year`),
    KEY `academic_year` (`academic_year`),
    KEY `status` (`status`),
    KEY `submitted_by` (`submitted_by`),
    KEY `approved_by` (`approved_by`),
    CONSTRAINT `commitment_forms_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `commitment_forms_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `commitment_forms_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Accomplishment Reports Table

```sql
-- Accomplishment reports table for tracking organizational achievements
CREATE TABLE `accomplplishment_reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `organization_id` int(11) NOT NULL,
    `academic_year` varchar(20) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `date_accomplished` date NOT NULL,
    `participants_count` int(11) NOT NULL DEFAULT 0,
    `file_path` varchar(500) DEFAULT NULL,
    `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
    `submitted_by` int(11) DEFAULT NULL,
    `approved_by` int(11) DEFAULT NULL,
    `approved_at` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `organization_id` (`organization_id`),
    KEY `academic_year` (`academic_year`),
    KEY `status` (`status`),
    KEY `submitted_by` (`submitted_by`),
    KEY `approved_by` (`approved_by`),
    CONSTRAINT `accomplplishment_reports_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `accomplplishment_reports_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `accomplplishment_reports_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Document Review History Table

```sql
-- Document review history table for tracking review actions
CREATE TABLE `document_review_history` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `document_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `action` varchar(50) NOT NULL,
    `from_status` varchar(50) DEFAULT NULL,
    `to_status` varchar(50) DEFAULT NULL,
    `comment_id` int(11) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `document_id` (`document_id`),
    KEY `user_id` (`user_id`),
    KEY `action` (`action`),
    CONSTRAINT `document_review_history_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `document_submissions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_review_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `document_review_history_ibfk_3` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Notifications Table

```sql
-- Notifications table for system notifications
CREATE TABLE `notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `type` varchar(50) NOT NULL DEFAULT 'info',
    `related_id` int(11) DEFAULT NULL,
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `type` (`type`),
    KEY `is_read` (`is_read`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Academic Years Table

```sql
-- Academic years table for managing academic year periods
CREATE TABLE `academic_years` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `year` varchar(20) NOT NULL,
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `is_current` tinyint(1) NOT NULL DEFAULT 0,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `year` (`year`),
    KEY `is_current` (`is_current`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## System Settings Table

```sql
-- System settings table for configuration
CREATE TABLE `system_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text DEFAULT NULL,
    `setting_type` varchar(50) NOT NULL DEFAULT 'text',
    `description` text DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Initial Data Insertion

```sql
-- Insert default admin user (password: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `role`, `first_name`, `last_name`, `is_active`) VALUES
('admin', 'admin@usg-accreditation.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator', 1);

-- Insert sample academic years
INSERT INTO `academic_years` (`year`, `start_date`, `end_date`, `is_current`) VALUES
('2023-2024', '2023-08-01', '2024-07-31', 0),
('2024-2025', '2024-08-01', '2025-07-31', 1),
('2025-2026', '2025-08-01', '2026-07-31', 0);

-- Insert default system settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('system_name', 'USG Accreditation Management System', 'text', 'System name displayed in header'),
('system_email', 'admin@usg-accreditation.com', 'email', 'System email for notifications'),
('max_file_size', '50', 'number', 'Maximum file upload size in MB'),
('allowed_file_types', 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', 'text', 'Allowed file extensions'),
('auto_approve_documents', '0', 'boolean', 'Automatically approve documents (0=No, 1=Yes)'),
('notification_enabled', '1', 'boolean', 'Enable email notifications (0=No, 1=Yes)'),
('maintenance_mode', '0', 'boolean', 'Put system in maintenance mode (0=No, 1=Yes)');
```

## Database User Creation

```sql
-- Create database user for the application
CREATE USER IF NOT EXISTS 'usg_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, INDEX, REFERENCES ON `usg_accreditation`.* TO 'usg_user'@'localhost';
FLUSH PRIVILEGES;
```

## Database Optimization

```sql
-- Create indexes for better performance
CREATE INDEX idx_document_submissions_org_year_status ON document_submissions(organization_id, academic_year, status);
CREATE INDEX idx_financial_reports_org_year ON financial_reports(organization_id, academic_year);
CREATE INDEX idx_calendar_activities_org_date ON calendar_activities(organization_id, activity_date);
CREATE INDEX idx_notifications_user_unread ON notifications(user_id, is_read);
CREATE INDEX idx_document_review_history_doc_user ON document_review_history(document_id, user_id);

-- Create full-text search indexes for document titles
ALTER TABLE document_submissions ADD FULLTEXT(document_title);
ALTER TABLE calendar_activities ADD FULLTEXT(activity_title, remarks);
```

## Database Backup and Restore

### Backup Commands
```bash
# Full database backup
mysqldump -u root -p usg_accreditation > usg_accreditation_backup.sql

# Compressed backup
mysqldump -u root -p usg_accreditation | gzip > usg_accreditation_backup.sql.gz

# Backup with date
mysqldump -u root -p usg_accreditation > usg_accreditation_backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Commands
```bash
# Restore from backup
mysql -u root -p usg_accreditation < usg_accreditation_backup.sql

# Restore from compressed backup
gunzip < usg_accreditation_backup.sql.gz | mysql -u root -p usg_accreditation
```

## Database Maintenance

```sql
-- Optimize tables
OPTIMIZE TABLE users, organizations, document_submissions, comments, financial_reports, program_expenditures, calendar_activities, commitment_forms, accomplishment_reports, document_review_history, notifications, academic_years, system_settings;

-- Check table integrity
CHECK TABLE users, organizations, document_submissions, comments, financial_reports, program_expenditures, calendar_activities, commitment_forms, accomplishment_reports, document_review_history, notifications, academic_years, system_settings;

-- Analyze tables for query optimization
ANALYZE TABLE users, organizations, document_submissions, comments, financial_reports, program_expenditures, calendar_activities, commitment_forms, accomplishment_reports, document_review_history, notifications, academic_years, system_settings;
```

## Migration Notes

1. **Foreign Key Constraints**: All foreign key constraints are set up with appropriate ON DELETE actions
2. **Character Set**: All tables use utf8mb4 for full Unicode support
3. **Indexes**: Strategic indexes are created for common query patterns
4. **Default Values**: Sensible defaults are provided for all columns
5. **Timestamps**: Automatic timestamp management for created_at and updated_at columns

## Security Considerations

1. **Password Hashing**: User passwords are hashed using PHP's password_hash() function
2. **SQL Injection Prevention**: Use prepared statements in application code
3. **Data Validation**: Implement proper validation in application layer
4. **Access Control**: Implement role-based access control in application
5. **Audit Trail**: Document review history table tracks all changes

---

**Note**: Execute these SQL commands in order to properly set up the database structure. Make sure to replace 'your_secure_password' with an actual secure password for the database user.

-- Madreseh Planner - Database Schema
-- All tables use UTF-8 character set to support Persian text.

-- Semesters Table: Defines academic semesters
CREATE TABLE `semesters` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_archived` TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Users Table: Stores user accounts
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'director', 'assessor', 'user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- User Tokens Table: For "Remember Me" functionality
CREATE TABLE `user_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `selector` VARCHAR(255) NOT NULL,
  `hashed_validator` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Audiences Table: Lookup table for audience groups
CREATE TABLE `audiences` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Organizers Table: Lookup table for organizing departments
CREATE TABLE `organizers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Proposals Table: Core table for program proposals
CREATE TABLE `proposals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `semester_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `event_datetime` DATETIME NOT NULL,
  `event_end_datetime` DATETIME NULL,
  `objective` TEXT NOT NULL,
  `priority` ENUM('high', 'medium', 'low') NOT NULL,
  `status` ENUM('pending', 'approved') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Proposal Audiences Pivot Table: Many-to-many relationship
CREATE TABLE `proposal_audiences` (
  `proposal_id` INT NOT NULL,
  `audience_id` INT NOT NULL,
  PRIMARY KEY (`proposal_id`, `audience_id`),
  FOREIGN KEY (`proposal_id`) REFERENCES `proposals`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`audience_id`) REFERENCES `audiences`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Proposal Organizers Pivot Table: Many-to-many relationship
CREATE TABLE `proposal_organizers` (
  `proposal_id` INT NOT NULL,
  `organizer_id` INT NOT NULL,
  PRIMARY KEY (`proposal_id`, `organizer_id`),
  FOREIGN KEY (`proposal_id`) REFERENCES `proposals`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`organizer_id`) REFERENCES `organizers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

-- Macro Plan Events Table
CREATE TABLE `macro_plan_events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `semester_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

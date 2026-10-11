-- ════════════════════════════════════════════════════
-- NEW MONEY MUSE — DATABASE SCHEMA
-- Run this in Hostinger phpMyAdmin after creating
-- your database (newmoneymuse_db)
-- ════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ── USERS ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  plan          ENUM('free','pro') NOT NULL DEFAULT 'free',
  stripe_customer_id VARCHAR(100) NULL,
  hustle_match  VARCHAR(200) NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── SESSIONS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS sessions (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  token      VARCHAR(64) NOT NULL UNIQUE,
  expires_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_token (token),
  INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── EARNINGS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS earnings (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  date       DATE NOT NULL,
  platform   VARCHAR(50) NOT NULL,
  hours      DECIMAL(5,1) DEFAULT 0,
  gross      DECIMAL(10,2) NOT NULL,
  expenses   DECIMAL(10,2) DEFAULT 0,
  notes      TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_date (user_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── GOALS ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS goals (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  user_id        INT NOT NULL,
  name           VARCHAR(200) NOT NULL,
  target_amount  DECIMAL(10,2) NOT NULL,
  current_amount DECIMAL(10,2) DEFAULT 0,
  color          VARCHAR(20) DEFAULT 'green',
  deadline       VARCHAR(50) NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── JOURNAL ENTRIES ────────────────────────────────
CREATE TABLE IF NOT EXISTS journal_entries (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  entry_type ENUM('note','win','milestone') DEFAULT 'note',
  content    TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── SAVED CALCULATIONS ─────────────────────────────
CREATE TABLE IF NOT EXISTS saved_calculations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  calc_type   VARCHAR(50) NOT NULL,
  name        VARCHAR(200) NOT NULL,
  inputs      JSON NULL,
  result      VARCHAR(200) NULL,
  result_class VARCHAR(20) DEFAULT 'green',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── ROADMAP TASK PROGRESS ──────────────────────────
CREATE TABLE IF NOT EXISTS roadmap_tasks (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  phase_index INT NOT NULL,
  task_index  INT NOT NULL,
  done        TINYINT(1) DEFAULT 0,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_task (user_id, phase_index, task_index),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── NEWSLETTER SUBSCRIBERS ─────────────────────────
CREATE TABLE IF NOT EXISTS subscribers (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  email      VARCHAR(255) NOT NULL UNIQUE,
  name       VARCHAR(100) NULL,
  source     VARCHAR(100) NULL,
  status     ENUM('active','unsubscribed') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ════════════════════════════════════════════════════
-- DONE. Your database is ready.
-- Next: update /api/config.php with your credentials.
-- ════════════════════════════════════════════════════

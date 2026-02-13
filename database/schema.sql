-- ============================================
-- Mini ERP / Task Management System
-- Database Schema - MySQL 8.0
-- ============================================

CREATE DATABASE IF NOT EXISTS mini_erp
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mini_erp;

-- ----------------------------
-- Table: users (auth + roles)
-- ----------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_users_role (role),
    INDEX idx_users_email (email)
) ENGINE=InnoDB;

-- ----------------------------
-- Table: employees
-- ----------------------------
CREATE TABLE IF NOT EXISTS employees (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name  VARCHAR(50)  NOT NULL,
    last_name   VARCHAR(50)  NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    phone       VARCHAR(20)  DEFAULT NULL,
    department  VARCHAR(50)  NOT NULL,
    designation VARCHAR(50)  NOT NULL,
    salary      DECIMAL(10,2) DEFAULT NULL,
    hire_date   DATE         NOT NULL,
    status      ENUM('active', 'inactive', 'terminated') NOT NULL DEFAULT 'active',
    created_by  INT UNSIGNED DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_emp_department (department),
    INDEX idx_emp_status (status),
    CONSTRAINT fk_emp_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------
-- Table: projects
-- ----------------------------
CREATE TABLE IF NOT EXISTS projects (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    status      ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') NOT NULL DEFAULT 'planning',
    priority    ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    start_date  DATE         DEFAULT NULL,
    end_date    DATE         DEFAULT NULL,
    created_by  INT UNSIGNED DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_proj_status (status),
    INDEX idx_proj_priority (priority),
    CONSTRAINT fk_proj_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------
-- Table: tasks
-- ----------------------------
CREATE TABLE IF NOT EXISTS tasks (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id   INT UNSIGNED NOT NULL,
    assigned_to  INT UNSIGNED DEFAULT NULL,
    title        VARCHAR(150) NOT NULL,
    description  TEXT         DEFAULT NULL,
    status       ENUM('todo', 'in_progress', 'review', 'done') NOT NULL DEFAULT 'todo',
    priority     ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    due_date     DATE         DEFAULT NULL,
    created_by   INT UNSIGNED DEFAULT NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_task_status (status),
    INDEX idx_task_priority (priority),
    INDEX idx_task_project (project_id),
    INDEX idx_task_assigned (assigned_to),
    CONSTRAINT fk_task_project    FOREIGN KEY (project_id)  REFERENCES projects(id)  ON DELETE CASCADE,
    CONSTRAINT fk_task_assigned   FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL,
    CONSTRAINT fk_task_created_by FOREIGN KEY (created_by)  REFERENCES users(id)     ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------
-- Table: project_assignments (junction)
-- ----------------------------
CREATE TABLE IF NOT EXISTS project_assignments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    role        VARCHAR(50)  DEFAULT 'member',
    assigned_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_proj_emp (project_id, employee_id),
    CONSTRAINT fk_pa_project  FOREIGN KEY (project_id)  REFERENCES projects(id)  ON DELETE CASCADE,
    CONSTRAINT fk_pa_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------
-- Table: activity_log (audit)
-- ----------------------------
CREATE TABLE IF NOT EXISTS activity_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,
    action      VARCHAR(50)  NOT NULL,
    entity_type VARCHAR(50)  NOT NULL,
    entity_id   INT UNSIGNED NOT NULL,
    details     JSON         DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_log_user (user_id),
    INDEX idx_log_entity (entity_type, entity_id),
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------
-- Seed data: default users
-- Passwords will be updated by setup script
-- Default password: admin123 / user123
-- ----------------------------
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@minierp.com', '$2y$10$2chPGruH61eSO1.NqgTIJeTPA7O9KC5xleMDahWjjx41KIMJWCr22', 'admin'),
('user', 'user@minierp.com', '$2y$10$IQuxmhFkKMfCMth92121Qu3boyTZIXZaps7NyIDtwGFId3ImU.QeW', 'user');

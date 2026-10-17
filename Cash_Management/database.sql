CREATE DATABASE IF NOT EXISTS barmageyat_cashflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barmageyat_cashflow;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','partner','viewer') DEFAULT 'partner',
 is_active TINYINT DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 type ENUM('expense','income') NOT NULL
);

CREATE TABLE expenses (
 id INT AUTO_INCREMENT PRIMARY KEY,
 paid_by INT NOT NULL,
 category_id INT,
 amount DECIMAL(12,2) NOT NULL,
 currency VARCHAR(10) DEFAULT 'EGP',
 expense_date DATE NOT NULL,
 description TEXT,
 receipt_file VARCHAR(255),
 status ENUM('pending','approved','rejected') DEFAULT 'pending',
 approved_by INT NULL,
 approved_at DATETIME NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (paid_by) REFERENCES users(id),
 FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE income (
 id INT AUTO_INCREMENT PRIMARY KEY,
 received_by INT NOT NULL,
 category_id INT,
 customer_name VARCHAR(180),
 project_service VARCHAR(180),
 invoice_ref VARCHAR(120),
 payment_method VARCHAR(80),
 amount DECIMAL(12,2) NOT NULL,
 currency VARCHAR(10) DEFAULT 'EGP',
 income_date DATE NOT NULL,
 notes TEXT,
 attachment_file VARCHAR(255),
 status ENUM('pending','approved','rejected') DEFAULT 'pending',
 approved_by INT NULL,
 approved_at DATETIME NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (received_by) REFERENCES users(id),
 FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO users (name,email,password,role) VALUES
('Admin','admin@barmageyat.com', '$2y$10$Q7x8bSaVC4GmBtdI4BfbQ.qTQIC3f6.f0MDSQfBOLBUPkvIOIXzS6', 'admin'),
('Mahmoud','mahmoud@barmageyat.com', '$2y$10$Q7x8bSaVC4GmBtdI4BfbQ.qTQIC3f6.f0MDSQfBOLBUPkvIOIXzS6', 'partner');
-- Default password for both users: admin123

INSERT INTO categories (name,type) VALUES
('Hosting & Domains','expense'),('Marketing','expense'),('Software Subscriptions','expense'),('Travel','expense'),('Freelancers','expense'),('Equipment','expense'),('Other Expense','expense'),
('Customer Payment','income'),('Project Revenue','income'),('Consultation','income'),('Subscription','income'),('Other Income','income');

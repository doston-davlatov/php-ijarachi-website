/*
 * ======================================================
 *                    DATABASE YARATISH              
 * ======================================================
 */

-- Avvalgi bazani o‘chiramiz (agar mavjud bo‘lsa)
DROP DATABASE IF EXISTS ijarachi_db;

-- Yangi baza yaratamiz
CREATE DATABASE ijarachi_db;

-- Yaratilgan bazani tanlaymiz
USE ijarachi_db;

-- ============================================
-- 1. FOYDALANUVCHILAR JADVALLI (users)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,                   -- Unikal foydalanuvchi ID
    name VARCHAR(100) NOT NULL,                          -- Foydalanuvchi ismi
    email VARCHAR(100) NOT NULL UNIQUE,                  -- Email manzili (takrorlanmas)
    password VARCHAR(255) NOT NULL,                      -- Xavfsiz saqlanadigan parol (hashlangan)
    phone VARCHAR(20),                                   -- Telefon raqami
    role ENUM('user', 'admin') DEFAULT 'user',           -- Roli: oddiy foydalanuvchi yoki admin
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP        -- Ro‘yxatdan o‘tgan sana
);

-- ============================================
-- 2. TOIFALAR JADVALLI (categories)
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,                   -- Unikal toifa ID
    name VARCHAR(100) NOT NULL,                         -- Toifa nomi (masalan: Uy, Mashina, Texnika)
    description TEXT,                                   -- Qisqa tavsif (ixtiyoriy)
    status ENUM('active', 'inactive') DEFAULT 'active', -- Holati (aktiv/yoki yo‘q)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,     -- Yaratilgan sana
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Yangilangan sana
);

-- ============================================
-- 3. E’LONLAR JADVALLI (listings)
-- ============================================
CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,                   -- E’lon ID
    user_id INT NOT NULL,                                -- Kim joylagani (foydalanuvchi ID)
    title VARCHAR(255) NOT NULL,                         -- E’lon sarlavhasi
    description TEXT,                                    -- Batafsil matn
    category_id INT NOT NULL,                            -- Toifasi
    price DECIMAL(10, 2) NOT NULL,                       -- Narxi
    location VARCHAR(100),                               -- Joylashuv (shahar yoki tuman)
    images JSON DEFAULT '[]',                            -- Rasm URLlari (JSON formatida)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,      -- Yaratilgan vaqt
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Yangilangan vaqt
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, -- Foydalanuvchi o‘chirilsa, e’lonlar ham o‘chirilsin
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE -- Toifa o‘chirilsa, e’lonlar ham o‘chirilsin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. SEVIMLI E’LONLAR JADVALLI (favorites)
-- ============================================
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,                   -- Sevimli ID
    user_id INT NOT NULL,                                -- Kim saqlagan
    listing_id INT NOT NULL,                             -- Qaysi e’lonni saqlagan
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, listing_id)     -- Bir foydalanuvchi bitta e’lonni faqat 1 marta saqlay oladi
);

-- ============================================
-- 5. XABARLAR JADVALLI (messages)
-- ============================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,                   -- Xabar ID
    user_id INT NOT NULL,                               -- Qabul qiluvchi ID
    subject VARCHAR(255) NOT NULL,                       -- Xabar mavzusi
    message TEXT NOT NULL,                               -- Xabar matni
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,      -- Yaratilgan vaqt
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Foydalanuvchi o‘chirilsa, xabarlar ham o‘chirilsin
);

-- ============================================
-- BOSHLANG'ICH MA'LUMOTLAR (categories uchun)
-- ============================================
INSERT INTO categories (name, description) VALUES
('Uy', 'Uylarni ijaraga olish va sotish'),
('Mashina', 'Mashinalarni ijaraga olish va sotish'),
('Texnika', 'Texnika va jihozlar');
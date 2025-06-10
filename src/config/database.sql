/*
 * ======================================================
 *                    DATABASE YARATISH              
 * ======================================================
 */

-- Avvalgi bazani o‘chiramiz (agar mavjud bo‘lsa)
DROP DATABASE IF EXISTS  ijarachi_db;

-- Yangi baza yaratamiz
CREATE DATABASE ijarachi_db;

-- Yaratilgan bazani tanlaymiz
USE ijarachi_db;

-- ============================================
-- 1. FOYDALANUVCHILAR JADVALLI (users)
-- ============================================
CREATE TABLE users (
    'id' INT AUTO_INCREMENT PRIMARY KEY,                   -- Unikal foydalanuvchi ID
    'user' VARCHAR(100) NOT NULL,                          -- Foydalanuvchi ismi
    'email' VARCHAR(100) NOT NULL UNIQUE,                  -- Email manzili (takrorlanmas)
    'password' VARCHAR(255) NOT NULL,                      -- Xavfsiz saqlanadigan parol (hashlangan)
    'phone' VARCHAR(20),                                   -- Telefon raqami
    'role' ENUM('user', 'admin') DEFAULT 'user',           -- Roli: oddiy foydalanuvchi yoki admin
    'created_at' DATETIME DEFAULT CURRENT_TIMESTAMP        -- Ro‘yxatdan o‘tgan sana
);

-- ============================================
-- 2. TOIFALAR JADVALLI (categories)
-- ============================================
CREATE TABLE categories (
    'id' INT AUTO_INCREMENT PRIMARY KEY,                   -- Unikal toifa ID
    'name' VARCHAR(100) NOT NULL                           -- Masalan: Uy, Mashina, Texnika
);

-- ============================================
-- 3. E’LONLAR JADVALLI (listings)
-- ============================================
CREATE TABLE listings (
    'id' INT AUTO_INCREMENT PRIMARY KEY,                   -- E’lon ID
    'user_id' INT NOT NULL,                                -- Kim joylagani (foydalanuvchi ID)
    'title' VARCHAR(255) NOT NULL,                         -- E’lon sarlavhasi
    'description' TEXT,                                    -- Batafsil matn
    'category_id' INT NOT NULL,                            -- Toifasi
    'price' DECIMAL(10,2) NOT NULL,                        -- Narxi
    'location' VARCHAR(100),                               -- Joylashuv (shahar yoki tuman)
    'images' TEXT,                                         -- Rasm URL'lari (json yoki vergul bilan ajratilgan)
    'status' ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',  -- Holati
    'created_at' DATETIME DEFAULT CURRENT_TIMESTAMP,       -- Joylangan vaqti

    -- Tashqi kalitlar (bog‘lanishlar)
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ============================================
-- 4. SEVIMLI E’LONLAR JADVALLI (favorites)
-- ============================================
CREATE TABLE favorites (
    'id' INT AUTO_INCREMENT PRIMARY KEY,                   -- Sevimli ID
    'user_id' INT NOT NULL,                                -- Kim saqlagan
    'listing_id' INT NOT NULL,                             -- Qaysi e’lonni saqlagan

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,

    UNIQUE KEY unique_favorite (user_id, listing_id)     -- Bir foydalanuvchi bitta e’lonni faqat 1 marta saqlay oladi
);

-- ============================================
-- 5. XABARLAR JADVALLI (messages)
-- ============================================
CREATE TABLE messages (
    'id' INT AUTO_INCREMENT PRIMARY KEY,                   -- Xabar ID
    'sender_id' INT NOT NULL,                              -- Jo‘natuvchi ID
    'receiver_id' INT NOT NULL,                            -- Qabul qiluvchi ID
    'message' TEXT NOT NULL,                               -- Xabar matni
    'sent_at' DATETIME DEFAULT CURRENT_TIMESTAMP,          -- Yuborilgan vaqt

    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

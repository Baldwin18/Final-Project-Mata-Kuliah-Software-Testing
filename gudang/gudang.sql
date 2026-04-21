-- Jalankan file ini di phpMyAdmin
-- Menu: Import > pilih file ini > klik Go

CREATE DATABASE IF NOT EXISTS gudang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gudang;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS barang (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    kode       VARCHAR(50)  UNIQUE,
    nama       VARCHAR(100) NOT NULL UNIQUE,
    satuan     VARCHAR(20),
    harga      DECIMAL(15,2) DEFAULT 0,
    stok       INT           DEFAULT 0,
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transaksi (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    barang_id    INT         NOT NULL,
    tipe         ENUM('MASUK','KELUAR') NOT NULL,
    jumlah       INT         NOT NULL,
    stok_sebelum INT,
    stok_sesudah INT,
    keterangan   TEXT,
    tanggal      DATETIME    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
);

<?php

namespace App;

class Database
{
    private \PDO $pdo;

    public function __construct(string $dsn, string $user = '', string $pass = '')
    {
        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Buat tabel-tabel yang dibutuhkan (untuk SQLite in-memory saat testing).
     */
    public function migrate(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id       INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL
            );

            CREATE TABLE IF NOT EXISTS barang (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                kode       TEXT UNIQUE,
                nama       TEXT NOT NULL UNIQUE,
                satuan     TEXT,
                harga      REAL DEFAULT 0,
                stok       INTEGER DEFAULT 0,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS transaksi (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                barang_id    INTEGER NOT NULL,
                tipe         TEXT NOT NULL CHECK(tipe IN ('MASUK','KELUAR')),
                jumlah       INTEGER NOT NULL,
                stok_sebelum INTEGER,
                stok_sesudah INTEGER,
                keterangan   TEXT,
                tanggal      TEXT DEFAULT (datetime('now')),
                FOREIGN KEY (barang_id) REFERENCES barang(id)
            );
        ");
    }
}

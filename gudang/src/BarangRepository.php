<?php

namespace App;

class BarangRepository
{
    public function __construct(private \PDO $pdo) {}

    public function findAll(): array
    {
        return $this->pdo->query('SELECT * FROM barang ORDER BY nama')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM barang WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByNama(string $nama): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM barang WHERE LOWER(nama) = LOWER(?)');
        $stmt->execute([$nama]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(string $nama, ?string $kode, ?string $satuan, float $harga, int $stok): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO barang (kode, nama, satuan, harga, stok) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$kode, $nama, $satuan, $harga, $stok]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateStok(int $id, int $stokBaru): void
    {
        $stmt = $this->pdo->prepare('UPDATE barang SET stok = ? WHERE id = ?');
        $stmt->execute([$stokBaru, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM barang WHERE id = ?');
        $stmt->execute([$id]);
    }
}

<?php

namespace Tests\Integration;

use App\Database;
use App\BarangRepository;
use PHPUnit\Framework\TestCase;

class BarangRepositoryTest extends TestCase
{
    private BarangRepository $repo;

    protected function setUp(): void
    {
        // Gunakan SQLite in-memory agar tidak perlu MySQL saat CI
        $db = new Database('sqlite::memory:');
        $db->migrate();
        $this->repo = new BarangRepository($db->getPdo());
    }

    // ── Integration Test 1: Insert & FindAll ──────────────────────────────────

    public function test_insert_barang_dan_tampil_di_findall(): void
    {
        $this->repo->insert('Beras', 'BRG-001', 'kg', 5000.0, 100);
        $this->repo->insert('Gula',  'BRG-002', 'kg', 12000.0, 50);

        $all = $this->repo->findAll();
        $this->assertCount(2, $all);
    }

    // ── Integration Test 2: FindByNama ────────────────────────────────────────

    public function test_find_by_nama_mengembalikan_barang_yang_benar(): void
    {
        $this->repo->insert('Minyak', 'BRG-003', 'liter', 15000.0, 30);

        $barang = $this->repo->findByNama('minyak'); // case-insensitive
        $this->assertNotNull($barang);
        $this->assertSame('Minyak', $barang['nama']);
    }

    public function test_find_by_nama_tidak_ada_mengembalikan_null(): void
    {
        $result = $this->repo->findByNama('BarangTidakAda');
        $this->assertNull($result);
    }

    // ── Integration Test 3: Update Stok ──────────────────────────────────────

    public function test_update_stok_tersimpan_dengan_benar(): void
    {
        $id = $this->repo->insert('Tepung', 'BRG-004', 'kg', 8000.0, 20);
        $this->repo->updateStok($id, 45);

        $barang = $this->repo->findById($id);
        $this->assertSame(45, (int)$barang['stok']);
    }

    // ── Integration Test 4: Delete ────────────────────────────────────────────

    public function test_hapus_barang_tidak_ada_di_findall(): void
    {
        $id = $this->repo->insert('Garam', 'BRG-005', 'kg', 3000.0, 10);
        $this->repo->delete($id);

        $barang = $this->repo->findById($id);
        $this->assertNull($barang);
    }

    // ── Integration Test 5: Alur lengkap transaksi masuk & keluar ────────────

    public function test_alur_transaksi_masuk_keluar_stok_konsisten(): void
    {
        $id = $this->repo->insert('Kopi', 'BRG-006', 'kg', 50000.0, 10);

        // Masuk 20
        $barang       = $this->repo->findById($id);
        $stokSetelahMasuk = (int)$barang['stok'] + 20;
        $this->repo->updateStok($id, $stokSetelahMasuk);

        // Keluar 5
        $barang        = $this->repo->findById($id);
        $stokSetelahKeluar = (int)$barang['stok'] - 5;
        $this->repo->updateStok($id, $stokSetelahKeluar);

        $final = $this->repo->findById($id);
        $this->assertSame(25, (int)$final['stok']); // 10 + 20 - 5 = 25
    }

    // ── Integration Test 6: Duplikat nama ditolak ────────────────────────────

    public function test_duplikat_nama_barang_ditolak_database(): void
    {
        $this->repo->insert('Susu', 'BRG-007', 'liter', 7000.0, 5);

        $this->expectException(\PDOException::class);
        $this->repo->insert('Susu', 'BRG-008', 'liter', 7000.0, 5); // nama sama
    }

    // ── Integration Test 7: FindAll kosong ───────────────────────────────────

    public function test_findall_kosong_mengembalikan_array_kosong(): void
    {
        $this->assertSame([], $this->repo->findAll());
    }
}

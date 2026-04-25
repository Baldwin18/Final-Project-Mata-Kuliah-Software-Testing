<?php

namespace Tests\Unit;

use App\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    // ── Nama Barang ───────────────────────────────────────────────────────────

    public function test_nama_barang_valid(): void
    {
        $this->assertTrue(Validator::namaBarang('Beras 5kg'));
    }

    public function test_nama_barang_kosong_tidak_valid(): void
    {
        $this->assertFalse(Validator::namaBarang(''));
    }

    public function test_nama_barang_spasi_saja_tidak_valid(): void
    {
        $this->assertFalse(Validator::namaBarang('   '));
    }

    public function test_nama_barang_terlalu_panjang_tidak_valid(): void
    {
        $this->assertFalse(Validator::namaBarang(str_repeat('a', 101)));
    }

    public function test_nama_barang_tepat_100_karakter_valid(): void
    {
        $this->assertTrue(Validator::namaBarang(str_repeat('a', 100)));
    }

    // ── Kode Barang ───────────────────────────────────────────────────────────

    public function test_kode_barang_null_valid(): void
    {
        $this->assertTrue(Validator::kodeBarang(null));
    }

    public function test_kode_barang_kosong_valid(): void
    {
        $this->assertTrue(Validator::kodeBarang(''));
    }

    public function test_kode_barang_alphanumeric_valid(): void
    {
        $this->assertTrue(Validator::kodeBarang('BRG-001'));
    }

    public function test_kode_barang_dengan_spasi_tidak_valid(): void
    {
        $this->assertFalse(Validator::kodeBarang('BRG 001'));
    }

    // ── Harga ─────────────────────────────────────────────────────────────────

    public function test_harga_nol_valid(): void
    {
        $this->assertTrue(Validator::harga(0));
    }

    public function test_harga_positif_valid(): void
    {
        $this->assertTrue(Validator::harga(5000.0));
    }

    public function test_harga_negatif_tidak_valid(): void
    {
        $this->assertFalse(Validator::harga(-1.0));
    }

    // ── Stok ──────────────────────────────────────────────────────────────────

    public function test_stok_nol_valid(): void
    {
        $this->assertTrue(Validator::stok(0));
    }

    public function test_stok_negatif_tidak_valid(): void
    {
        $this->assertFalse(Validator::stok(-5));
    }

    // ── Jumlah Transaksi ──────────────────────────────────────────────────────

    public function test_jumlah_transaksi_positif_valid(): void
    {
        $this->assertTrue(Validator::jumlahTransaksi(10));
    }

    public function test_jumlah_transaksi_nol_tidak_valid(): void
    {
        $this->assertFalse(Validator::jumlahTransaksi(0));
    }

    public function test_jumlah_transaksi_negatif_tidak_valid(): void
    {
        $this->assertFalse(Validator::jumlahTransaksi(-1));
    }

    // ── Tipe Transaksi ────────────────────────────────────────────────────────

    public function test_tipe_masuk_valid(): void
    {
        $this->assertTrue(Validator::tipeTransaksi('MASUK'));
    }

    public function test_tipe_keluar_valid(): void
    {
        $this->assertTrue(Validator::tipeTransaksi('KELUAR'));
    }

    public function test_tipe_tidak_dikenal_tidak_valid(): void
    {
        $this->assertFalse(Validator::tipeTransaksi('TRANSFER'));
    }

    // ── Username & Password ───────────────────────────────────────────────────

    public function test_username_valid(): void
    {
        $this->assertTrue(Validator::username('admin'));
    }

    public function test_username_terlalu_pendek_tidak_valid(): void
    {
        $this->assertFalse(Validator::username('ab'));
    }

    public function test_username_dengan_spasi_tidak_valid(): void
    {
        $this->assertFalse(Validator::username('user name'));
    }

    public function test_password_valid(): void
    {
        $this->assertTrue(Validator::password('secret123'));
    }

    public function test_password_terlalu_pendek_tidak_valid(): void
    {
        $this->assertFalse(Validator::password('abc'));
    }
}

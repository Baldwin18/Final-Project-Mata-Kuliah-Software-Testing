<?php

namespace Tests\Unit;

use App\KalkulatorStok;
use PHPUnit\Framework\TestCase;

class KalkulatorStokTest extends TestCase
{
    // ── Stok Masuk ────────────────────────────────────────────────────────────

    public function test_stok_masuk_menambah_stok_dengan_benar(): void
    {
        $this->assertSame(15, KalkulatorStok::stokMasuk(10, 5));
    }

    public function test_stok_masuk_dari_nol(): void
    {
        $this->assertSame(20, KalkulatorStok::stokMasuk(0, 20));
    }

    public function test_stok_masuk_jumlah_nol_throw_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KalkulatorStok::stokMasuk(10, 0);
    }

    public function test_stok_masuk_jumlah_negatif_throw_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KalkulatorStok::stokMasuk(10, -5);
    }

    // ── Stok Keluar ───────────────────────────────────────────────────────────

    public function test_stok_keluar_mengurangi_stok_dengan_benar(): void
    {
        $this->assertSame(5, KalkulatorStok::stokKeluar(10, 5));
    }

    public function test_stok_keluar_habis_tepat(): void
    {
        $this->assertSame(0, KalkulatorStok::stokKeluar(10, 10));
    }

    public function test_stok_keluar_melebihi_stok_throw_exception(): void
    {
        $this->expectException(\UnderflowException::class);
        KalkulatorStok::stokKeluar(5, 10);
    }

    public function test_stok_keluar_jumlah_nol_throw_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KalkulatorStok::stokKeluar(10, 0);
    }

    public function test_stok_keluar_jumlah_negatif_throw_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KalkulatorStok::stokKeluar(10, -3);
    }

    // ── Nilai Inventori ───────────────────────────────────────────────────────

    public function test_nilai_inventori_dihitung_benar(): void
    {
        $barang = [
            ['stok' => 10, 'harga' => 5000.0],
            ['stok' => 5,  'harga' => 20000.0],
        ];
        $this->assertSame(150000.0, KalkulatorStok::nilaiInventori($barang));
    }

    public function test_nilai_inventori_kosong_adalah_nol(): void
    {
        $this->assertSame(0.0, KalkulatorStok::nilaiInventori([]));
    }

    public function test_nilai_inventori_stok_nol(): void
    {
        $barang = [['stok' => 0, 'harga' => 10000.0]];
        $this->assertSame(0.0, KalkulatorStok::nilaiInventori($barang));
    }

    // ── Barang Hampir Habis ───────────────────────────────────────────────────

    public function test_barang_hampir_habis_terdeteksi(): void
    {
        $barang = [
            ['nama' => 'Beras', 'stok' => 5],
            ['nama' => 'Gula',  'stok' => 50],
            ['nama' => 'Minyak','stok' => 0],
        ];
        $hasil = KalkulatorStok::barangHampirHabis($barang, 10);
        $this->assertCount(2, $hasil);
    }

    public function test_barang_hampir_habis_semua_aman_kosong(): void
    {
        $barang = [
            ['nama' => 'Beras', 'stok' => 100],
            ['nama' => 'Gula',  'stok' => 50],
        ];
        $this->assertEmpty(KalkulatorStok::barangHampirHabis($barang, 10));
    }

    public function test_barang_hampir_habis_batas_negatif_throw_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KalkulatorStok::barangHampirHabis([], -1);
    }

    // ── Rata-rata Stok ────────────────────────────────────────────────────────

    public function test_rata_rata_stok_dihitung_benar(): void
    {
        $barang = [
            ['stok' => 10],
            ['stok' => 20],
            ['stok' => 30],
        ];
        $this->assertSame(20.0, KalkulatorStok::rataRataStok($barang));
    }

    public function test_rata_rata_stok_kosong_adalah_nol(): void
    {
        $this->assertSame(0.0, KalkulatorStok::rataRataStok([]));
    }
}

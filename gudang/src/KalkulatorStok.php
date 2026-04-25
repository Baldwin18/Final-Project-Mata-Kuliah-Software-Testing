<?php

namespace App;

class KalkulatorStok
{
    /**
     * Hitung stok setelah transaksi masuk.
     */
    public static function stokMasuk(int $stokSaat, int $jumlah): int
    {
        if ($jumlah <= 0) {
            throw new \InvalidArgumentException('Jumlah masuk harus lebih dari 0.');
        }
        return $stokSaat + $jumlah;
    }

    /**
     * Hitung stok setelah transaksi keluar.
     * Throws jika stok tidak mencukupi.
     */
    public static function stokKeluar(int $stokSaat, int $jumlah): int
    {
        if ($jumlah <= 0) {
            throw new \InvalidArgumentException('Jumlah keluar harus lebih dari 0.');
        }
        if ($stokSaat < $jumlah) {
            throw new \UnderflowException("Stok tidak mencukupi. Stok saat ini: $stokSaat, diminta: $jumlah.");
        }
        return $stokSaat - $jumlah;
    }

    /**
     * Hitung nilai total inventori (stok × harga).
     *
     * @param array $barang Array of ['stok' => int, 'harga' => float]
     */
    public static function nilaiInventori(array $barang): float
    {
        return array_reduce($barang, function (float $carry, array $item): float {
            return $carry + ($item['stok'] * $item['harga']);
        }, 0.0);
    }

    /**
     * Filter barang yang stoknya di bawah batas minimum.
     *
     * @param array $barang        Array of ['nama' => string, 'stok' => int, ...]
     * @param int   $batasMinimum
     */
    public static function barangHampirHabis(array $barang, int $batasMinimum = 10): array
    {
        if ($batasMinimum < 0) {
            throw new \InvalidArgumentException('Batas minimum tidak boleh negatif.');
        }
        return array_values(array_filter($barang, fn($b) => $b['stok'] < $batasMinimum));
    }

    /**
     * Hitung rata-rata stok dari semua barang.
     */
    public static function rataRataStok(array $barang): float
    {
        if (empty($barang)) return 0.0;
        $total = array_sum(array_column($barang, 'stok'));
        return round($total / count($barang), 2);
    }
}

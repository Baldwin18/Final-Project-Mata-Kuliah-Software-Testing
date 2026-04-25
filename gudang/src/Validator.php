<?php

namespace App;

class Validator
{
    public static function namaBarang(string $nama): bool
    {
        $nama = trim($nama);
        return strlen($nama) >= 1 && strlen($nama) <= 100;
    }

    public static function kodeBarang(?string $kode): bool
    {
        if ($kode === null || $kode === '') return true; // opsional
        return (bool) preg_match('/^[A-Za-z0-9\-_]+$/', $kode) && strlen($kode) <= 50;
    }

    public static function harga(float $harga): bool
    {
        return $harga >= 0;
    }

    public static function stok(int $stok): bool
    {
        return $stok >= 0;
    }

    public static function jumlahTransaksi(int $jumlah): bool
    {
        return $jumlah > 0;
    }

    public static function tipeTransaksi(string $tipe): bool
    {
        return in_array($tipe, ['MASUK', 'KELUAR'], true);
    }

    public static function username(string $username): bool
    {
        $username = trim($username);
        return strlen($username) >= 3 && !str_contains($username, ' ');
    }

    public static function password(string $password): bool
    {
        return strlen($password) >= 6;
    }
}

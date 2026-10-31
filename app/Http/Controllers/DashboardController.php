<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function owner()
    {
        $totalProduk = Product::count();

        $totalPelanggan = Customer::count();

        $totalTransaksi = Transaction::count();

        $totalPenjualan = Transaction::sum('total_akhir');

        $transaksiTerbaru = Transaction::with([
                'customer',
                'user'
            ])
            ->latest('tanggal_transaksi')
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact(
            'totalProduk',
            'totalPelanggan',
            'totalTransaksi',
            'totalPenjualan',
            'transaksiTerbaru'
        ));
    }

    public function kasir()
    {
        $transaksiHariIni = Transaction::whereDate(
            'tanggal_transaksi',
            today()
        )->count();

        $penjualanHariIni = Transaction::whereDate(
            'tanggal_transaksi',
            today()
        )->sum('total_akhir');

        $transaksiTerbaru = Transaction::with([
                'customer',
                'user'
            ])
            ->latest('tanggal_transaksi')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', compact(
            'transaksiHariIni',
            'penjualanHariIni',
            'transaksiTerbaru'
        ));
    }
}
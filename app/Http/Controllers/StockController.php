<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StockController extends Controller
{
    public function index(Request $request)
    {
    $query = Product::with('category')
        ->orderBy('stok', 'asc')
        ->orderBy('nama', 'asc');

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(
            'nama',
            'like',
            '%' . $search . '%'
        );
    }

    $products = $query
        ->paginate(10)
        ->withQueryString();

    return view(
        'stocks.index',
        compact('products')
    );
    }

    public function laporan(Request $request)
    {
        $status = $request->get('status', 'semua');

        $query = Product::with([
            'category',
            'stockMovements' => function ($query) {
                $query->where('jenis', 'masuk')
                    ->with('supplier')
                    ->latest('tanggal_stok');
            }
        ]);

        // Filter berdasarkan status stok
        if ($status === 'tersedia') {
            $query->where('stok', '>', 5);
        } elseif ($status === 'rendah') {
            $query->whereBetween('stok', [1, 5]);
        } elseif ($status === 'habis') {
            $query->where('stok', 0);
        }

        // 1. Hitung Ringkasan Statistik DARI SELURUH DATA yang terfilter (sebelum dipaginate)
        $allProducts = (clone $query)->get();

        $totalProduk      = $allProducts->count();
        $totalStok        = $allProducts->sum('stok');
        $produkHabis      = $allProducts->where('stok', 0)->count();
        $produkStokRendah = $allProducts->where('stok', '>', 0)->where('stok', '<=', 5)->count();

        // 2. Ambil data dengan Pagination (10 item per halaman)
        $products = $query
            ->orderBy('stok', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view(
            'owner.laporan.stok',
            compact(
                'products',
                'totalProduk',
                'totalStok',
                'produkHabis',
                'produkStokRendah',
                'status'
            )
        );
    }

    public function laporanPdf(Request $request)
    {
        $status = $request->get('status', 'semua');

        $query = Product::with([
            'category',
            'stockMovements' => function ($query) {
                $query->where('jenis', 'masuk')
                ->with('supplier')
                ->latest('tanggal_stok');
                }
            ]);

        // Filter yang sama dengan laporan web
        if ($status === 'tersedia') {
            $query->where('stok', '>', 5);
        } elseif ($status === 'rendah') {
            $query->whereBetween('stok', [1, 5]);
        } elseif ($status === 'habis') {
            $query->where('stok', 0);
        }

        $products = $query
            ->orderBy('stok', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $totalProduk = $products->count();
        $totalStok = $products->sum('stok');

        $produkHabis = $products
            ->where('stok', 0)
            ->count();

        $produkStokRendah = $products
            ->where('stok', '>', 0)
            ->where('stok', '<=', 5)
            ->count();

        $pdf = Pdf::loadView(
            'owner.laporan.pdf.stok',
            compact(
                'products',
                'totalProduk',
                'totalStok',
                'produkHabis',
                'produkStokRendah',
                'status'
            )
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-stok.pdf');
    }
}
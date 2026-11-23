<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'nomor_invoice',
        'pelanggan_id',
        'dibuat_oleh',
        'tanggal_transaksi',
        'total',
        'diskon',
        'total_akhir',
        'jumlah_bayar',
        'jumlah_dp',
        'uang_diterima',
        'uang_diterima_pelunasan',
        'metode_pembayaran',
        'status_pembayaran',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'total' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_akhir' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'jumlah_dp' => 'decimal:2',
        'uang_diterima' => 'decimal:2',
        'uang_diterima_pelunasan' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class,
            'pelanggan_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'dibuat_oleh'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            TransactionDetail::class,
            'transaksi_id'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesReturn extends Model
{
    protected $fillable = [
        'nomor_retur',
        'transaksi_id',
        'user_id',
        'tanggal_retur',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_retur' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            Transaction::class,
            'transaksi_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            SalesReturnDetail::class,
            'sales_return_id'
        );
    }
}

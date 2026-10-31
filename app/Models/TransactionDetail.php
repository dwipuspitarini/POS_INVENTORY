<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaksi_id',
        'product_id',
        'quantity',
        'harga',
        'subtotal',
    ];


    protected $casts = [

        'harga' =>
            'decimal:2',

        'subtotal' =>
            'decimal:2',

    ];


    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            Transaction::class,
            'transaksi_id'
        );
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class,
            'product_id'
        );
    }
}
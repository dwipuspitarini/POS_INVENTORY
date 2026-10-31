<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturnDetail extends Model
{
    protected $fillable = [
        'sales_return_id',
        'transaction_detail_id',
        'product_id',
        'quantity',
        'kondisi',
        'harga',
        'subtotal',
        'alasan',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(
            SalesReturn::class,
            'sales_return_id'
        );
    }

    public function transactionDetail(): BelongsTo
    {
        return $this->belongsTo(
            TransactionDetail::class,
            'transaction_detail_id'
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

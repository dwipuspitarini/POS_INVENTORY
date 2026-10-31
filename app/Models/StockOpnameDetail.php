<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameDetail extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
    ];

    protected $casts = [
        'stok_sistem' => 'integer',
        'stok_fisik' => 'integer',
        'selisih' => 'integer',
    ];

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(
            StockOpname::class,
            'stock_opname_id'
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

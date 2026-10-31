<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    protected $fillable = [
        'kode',
        'tanggal_opname',
        'user_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            StockOpnameDetail::class,
            'stock_opname_id'
        );
    }
}

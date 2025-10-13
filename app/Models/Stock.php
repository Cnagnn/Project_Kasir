<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    //

    protected $fillable = [
        'product_id',
        'initial_stock',
        'remaining_stock',
        'buy_price',
        'sell_price',
        'created_at',
        'updated_at'
        // tambahkan nama kolom lain jika ada
    ];

    protected $guarded = [
        'id'
    ];

    public function product()
    {
    	return $this->BelongsTo(Products::class);
    }
}

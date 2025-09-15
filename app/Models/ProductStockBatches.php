<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class ProductStockBatches extends Model
{
    //

    protected $guarded = [
        'id'
    ];

    public function product()
    {
    	return $this->BelongsTo(Products::class);
    }
}

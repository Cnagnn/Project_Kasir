<?php

namespace App\Models;

use App\Models\Categories;
use App\Models\TransactionDetails;
use App\Models\ProductStockBatches;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    protected $guarded = [
        'id'
    ];

    public function category()
    {
    	return $this->BelongsTo(Categories::class);
    }

    public function ProductStockBatches()
    {
    	return $this->HasMany(ProductStockBatches::class);
    }

    public function TransactionDetail()
    {
    	return $this->HasMany(TransactionDetails::class);
    }
}

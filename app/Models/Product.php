<?php

namespace App\Models;

use App\Models\Categories;
use App\Models\TransactionDetails;
use App\Models\ProductStockBatches;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //

    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public function category()
    {
    	return $this->BelongsTo(Categories::class);
    }

    public function stockBatches()
    {
    	return $this->HasMany(ProductStockBatches::class);
    }

    public function transactionDetails()
    {
    	return $this->HasMany(TransactionDetails::class);
    }
}

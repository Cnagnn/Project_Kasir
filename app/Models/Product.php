<?php

namespace App\Models;

use App\Models\Stock;
use App\Models\Category;
use App\Models\TransactionDetails;
use App\Models\ProductStockBatches;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    //

    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public function category()
    {
    	return $this->BelongsTo(Category::class);
    }

    public function stock()
    {
    	return $this->HasMany(Stock::class);
    }

    public function transactionDetails()
    {
    	return $this->HasMany(TransactionDetails::class);
    }
}

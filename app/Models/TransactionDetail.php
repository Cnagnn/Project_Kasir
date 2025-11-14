<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    //

    protected $guarded = [
        'id'
    ];
    
    public function Transaction()
    {
    	return $this->BelongsTo(Transaction::class);
    }

    public function Product()
    {
    	return $this->BelongsTo(Product::class);
    }
}

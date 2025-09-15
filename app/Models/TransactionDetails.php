<?php

namespace App\Models;

use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    //
    public function Transactions()
    {
    	return $this->BelongsTo(Transactions::class);
    }

    public function Products()
    {
    	return $this->BelongsTo(Products::class);
    }
}

<?php

namespace App\Models;

use App\Models\Users;
use App\Models\TransactionDetails;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    //
    public function Users()
    {
    	return $this->BelongsTo(Users::class);
    }

    public function TransactionDetail()
    {
    	return $this->HasMany(TransactionDetails::class);
    }
}

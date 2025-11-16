<?php

namespace App\Models;

use App\Models\User;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $guarded = [
        'id'
    ];

    public function user()
    {
    	return $this->BelongsTo(User::class, 'user_id');
    }

    public function details()
    {
    	return $this->HasMany(TransactionDetail::class);
    }
}

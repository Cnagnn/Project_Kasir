<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Transactions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable 
{
    //
    protected $guarded = [
        'id'
    ];

    public function role()
    {
    	return $this->BelongsTo(Role::class);
    }

    public function Transaction()
    {
    	return $this->HasMany(Transactions::class);
    }
}

<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Transactions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable 
{
    //
    use HasFactory, SoftDeletes;

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

<?php

namespace App\Models;

use App\Models\Roles;
use App\Models\Transactions;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    //
    public function Roles()
    {
    	return $this->BelongsTo(Roles::class);
    }

    public function Transaction()
    {
    	return $this->HasMany(Transactions::class);
    }
}

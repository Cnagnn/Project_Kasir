<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    //

    protected $guarded = [
        'id'
    ];

    public function product()
    {
    	return $this->hasMany(Products::class);
    }
}

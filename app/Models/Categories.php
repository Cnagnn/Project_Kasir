<?php

namespace App\Models;

use App\Models\Products;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    //

    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public function product()
    {
    	return $this->hasMany(Products::class);
    }
}

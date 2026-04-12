<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTemplate extends Model
{
    protected $fillable = ['name', 'content'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

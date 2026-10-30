<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const STATUSES = ['active', 'inactive'];

    protected $fillable = ['name', 'price', 'quantity', 'description', 'expiration_date', 'status'];

    protected function casts(): array
    {
        return ['expiration_date' => 'date'];
    }
}

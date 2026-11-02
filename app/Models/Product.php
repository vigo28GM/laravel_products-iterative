<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const STATUSES = ['active', 'inactive'];

    protected $fillable = ['name', 'price', 'quantity', 'description', 'expiration_date', 'status'];

    protected function casts(): array
    {
        return ['expiration_date' => 'date'];
    }

    public function increaseQuantity(int $amount): void
    {
        $this->increment('quantity', $amount);
    }

    /**
     * Returns false (and changes nothing) when there is not enough stock.
     */
    public function decreaseQuantity(int $amount): bool
    {
        $updated = static::whereKey($this->getKey())
            ->where('quantity', '>=', $amount)
            ->decrement('quantity', $amount);

        if ($updated) {
            $this->refresh();
        }

        return (bool) $updated;
    }
}

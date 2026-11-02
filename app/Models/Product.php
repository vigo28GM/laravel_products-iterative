<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

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
        $this->assertPositive($amount);

        $this->increment('quantity', $amount);
    }

    /**
     * Returns false (and changes nothing) when there is not enough stock.
     */
    public function decreaseQuantity(int $amount): bool
    {
        $this->assertPositive($amount);

        $updated = static::whereKey($this->getKey())
            ->where('quantity', '>=', $amount)
            ->decrement('quantity', $amount);

        if ($updated) {
            $this->refresh();
        }

        return (bool) $updated;
    }

    private function assertPositive(int $amount): void
    {
        if ($amount < 1) {
            throw new InvalidArgumentException('Amount must be at least 1.');
        }
    }
}

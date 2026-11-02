<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_increase_quantity_adds_to_stock(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $product->increaseQuantity(4);

        $this->assertSame(9, $product->quantity);
        $this->assertSame(9, $product->fresh()->quantity);
    }

    public function test_increase_quantity_works_from_zero(): void
    {
        $product = Product::factory()->create(['quantity' => 0]);

        $product->increaseQuantity(1);

        $this->assertSame(1, $product->fresh()->quantity);
    }

    public function test_decrease_quantity_removes_from_stock(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $this->assertTrue($product->decreaseQuantity(2));

        $this->assertSame(3, $product->quantity);
        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_decrease_quantity_can_reach_exactly_zero(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $this->assertTrue($product->decreaseQuantity(5));

        $this->assertSame(0, $product->fresh()->quantity);
    }

    public function test_decrease_quantity_fails_without_enough_stock(): void
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $this->assertFalse($product->decreaseQuantity(3));

        $this->assertSame(2, $product->quantity);
        $this->assertSame(2, $product->fresh()->quantity);
    }

    public function test_decrease_quantity_only_affects_its_own_product(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);
        $other = Product::factory()->create(['quantity' => 5]);

        $product->decreaseQuantity(3);

        $this->assertSame(5, $other->fresh()->quantity);
    }

    public function test_expiration_date_is_cast_to_date(): void
    {
        $product = Product::factory()->create(['expiration_date' => '2030-05-06']);

        $this->assertSame('2030-05-06', $product->fresh()->expiration_date->format('Y-m-d'));
    }
}

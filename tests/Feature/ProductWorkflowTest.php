<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration scenarios: routes, controller, validation, model, session and views working together.
 */
class ProductWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_user_can_add_a_product_and_see_it_in_the_list(): void
    {
        $this->followingRedirects()
            ->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => 'Latvian Software',
                'price' => 49.90,
                'quantity' => 10,
                'description' => 'Made in Riga',
                'expiration_date' => '2031-03-01',
                'status' => 'active',
            ])
            ->assertOk()
            ->assertSee('Product created.')
            ->assertSee('Latvian Software')
            ->assertSee('Made in Riga')
            ->assertSee('2031-03-01');

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Latvian Software');
    }

    public function test_failed_creation_keeps_input_and_shows_errors(): void
    {
        $this->followingRedirects()
            ->from(route('products.create'))
            ->post(route('products.store'), ['name' => '', 'price' => 5, 'quantity' => 1, 'status' => 'active', 'description' => 'Kept text'])
            ->assertOk()
            ->assertSee('The name field is required.')
            ->assertSee('Kept text');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_user_can_edit_a_product_and_see_the_changes(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name', 'status' => 'active']);

        $this->get(route('products.edit', $product))->assertOk()->assertSee('Old Name');

        $this->followingRedirects()
            ->put(route('products.update', $product), [
                'name' => 'New Name',
                'price' => 1,
                'quantity' => 7,
                'status' => 'inactive',
            ])
            ->assertOk()
            ->assertSee('Product updated.')
            ->assertSee('New Name')
            ->assertSee('Status: inactive');

        $this->get(route('products.index'))->assertSee('New Name')->assertDontSee('Old Name');
    }

    public function test_user_can_increase_and_decrease_quantity_from_the_show_page(): void
    {
        $product = Product::factory()->create(['quantity' => 10]);
        $show = route('products.show', $product);

        $this->followingRedirects()
            ->from($show)
            ->patch(route('products.adjust-quantity', $product), ['action' => 'increase', 'amount' => 5])
            ->assertSee('Added 5 to stock.')
            ->assertSee('<span id="quantity">15</span>', false);

        $this->followingRedirects()
            ->from($show)
            ->patch(route('products.adjust-quantity', $product), ['action' => 'decrease', 'amount' => 12])
            ->assertSee('Removed 12 from stock.')
            ->assertSee('<span id="quantity">3</span>', false);

        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_removing_more_stock_than_available_shows_error_and_keeps_quantity(): void
    {
        $product = Product::factory()->create(['quantity' => 3]);

        $this->followingRedirects()
            ->from(route('products.show', $product))
            ->patch(route('products.adjust-quantity', $product), ['action' => 'decrease', 'amount' => 4])
            ->assertSee('Not enough stock: only 3 available.')
            ->assertSee('<span id="quantity">3</span>', false);

        $this->assertSame(3, $product->fresh()->quantity);
    }

    public function test_quantity_change_is_visible_in_the_product_list(): void
    {
        $product = Product::factory()->create(['name' => 'Stock Item', 'quantity' => 1]);

        $this->patch(route('products.adjust-quantity', $product), ['action' => 'increase', 'amount' => 41]);

        $this->get(route('products.index'))->assertSee('42');
    }

    public function test_full_product_lifecycle(): void
    {
        $this->post(route('products.store'), [
            'name' => 'Lifecycle Hardware',
            'price' => 10,
            'quantity' => 1,
            'status' => 'active',
        ]);
        $product = Product::where('name', 'Lifecycle Hardware')->firstOrFail();

        $this->put(route('products.update', $product), [
            'name' => 'Lifecycle Hardware v2',
            'price' => 10,
            'quantity' => 1,
            'status' => 'active',
        ]);
        $this->patch(route('products.adjust-quantity', $product), ['action' => 'increase', 'amount' => 9]);
        $this->assertSame(10, $product->fresh()->quantity);
        $this->assertSame('Lifecycle Hardware v2', $product->fresh()->name);

        $this->followingRedirects()
            ->delete(route('products.destroy', $product))
            ->assertSee('Product deleted.')
            ->assertDontSee('Lifecycle Hardware v2');

        $this->get(route('products.show', $product))->assertNotFound();
    }
}

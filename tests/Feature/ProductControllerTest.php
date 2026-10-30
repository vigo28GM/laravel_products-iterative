<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Software',
            'price' => 12.5,
            'quantity' => 3,
            'description' => 'Some description',
            'expiration_date' => '2030-01-31',
            'status' => 'active',
        ], $overrides);
    }

    public function test_index_lists_products(): void
    {
        $product = Product::factory()->create(['name' => 'Listed Product']);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertViewIs('products.index')
            ->assertViewHas('products')
            ->assertSee($product->name);
    }

    public function test_index_shows_empty_message_without_products(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('No products yet.');
    }

    public function test_create_shows_form(): void
    {
        $this->get(route('products.create'))
            ->assertOk()
            ->assertViewIs('products.create');
    }

    public function test_store_creates_product_and_redirects_with_success_message(): void
    {
        $response = $this->post(route('products.store'), $this->validData());

        $product = Product::firstOrFail();
        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHas('success', 'Product created.');
        $this->assertSame('Test Software', $product->name);
        $this->assertSame('2030-01-31', $product->expiration_date->format('Y-m-d'));
    }

    public function test_store_accepts_missing_optional_fields(): void
    {
        $this->post(route('products.store'), $this->validData(['description' => null, 'expiration_date' => null]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('products', 1);
    }

    public function test_store_rejects_invalid_data(): void
    {
        $this->post(route('products.store'), [
            'name' => '',
            'price' => -1,
            'quantity' => 'many',
            'expiration_date' => 'not-a-date',
            'status' => 'unknown',
        ])->assertSessionHasErrors(['name', 'price', 'quantity', 'expiration_date', 'status']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_show_displays_product(): void
    {
        $product = Product::factory()->create(['name' => 'Shown Product']);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertViewIs('products.show')
            ->assertSee('Shown Product');
    }

    public function test_show_returns_404_for_missing_product(): void
    {
        $this->get(route('products.show', 999))->assertNotFound();
    }

    public function test_edit_shows_form_with_product_data(): void
    {
        $product = Product::factory()->create(['name' => 'Editable Product']);

        $this->get(route('products.edit', $product))
            ->assertOk()
            ->assertViewIs('products.edit')
            ->assertSee('Editable Product');
    }

    public function test_update_changes_product_and_redirects_with_success_message(): void
    {
        $product = Product::factory()->create();

        $this->put(route('products.update', $product), $this->validData(['name' => 'Renamed', 'status' => 'inactive']))
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHas('success', 'Product updated.');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Renamed', 'status' => 'inactive']);
    }

    public function test_update_rejects_invalid_data(): void
    {
        $product = Product::factory()->create(['name' => 'Original']);

        $this->put(route('products.update', $product), $this->validData(['name' => '', 'quantity' => -5]))
            ->assertSessionHasErrors(['name', 'quantity']);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Original']);
    }

    public function test_destroy_deletes_product_and_redirects_with_success_message(): void
    {
        $product = Product::factory()->create();

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success', 'Product deleted.');

        $this->assertModelMissing($product);
    }

    public function test_validation_errors_are_displayed_by_flash_component(): void
    {
        $this->from(route('products.create'))
            ->followingRedirects()
            ->post(route('products.store'), [])
            ->assertSee('The name field is required.');
    }

    public function test_root_redirects_to_products_index(): void
    {
        $this->get('/')->assertRedirect('/products');
    }
}

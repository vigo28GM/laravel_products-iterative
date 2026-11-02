<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_show_page_lists_tags(): void
    {
        $product = Product::factory()->create();
        $product->tags()->attach(Tag::factory()->create(['name' => 'fragile']));

        $this->get(route('products.show', $product))->assertOk()->assertSee('fragile');
    }

    public function test_tag_can_be_added_to_a_product(): void
    {
        $product = Product::factory()->create();

        $this->post(route('products.tags.store', $product), ['name' => 'Fragile'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(['fragile'], $product->tags()->pluck('name')->all());
    }

    public function test_adding_same_tag_to_two_products_reuses_it(): void
    {
        $a = Product::factory()->create();
        $b = Product::factory()->create();

        $this->post(route('products.tags.store', $a), ['name' => 'sale']);
        $this->post(route('products.tags.store', $b), ['name' => ' SALE ']);

        $this->assertDatabaseCount('tags', 1);
        $this->assertDatabaseCount('product_tag', 2);
    }

    public function test_duplicate_tag_on_same_product_is_rejected(): void
    {
        $product = Product::factory()->create();
        $this->post(route('products.tags.store', $product), ['name' => 'sale']);

        $this->post(route('products.tags.store', $product), ['name' => 'sale'])
            ->assertSessionHasErrors('name');

        $this->assertCount(1, $product->tags);
    }

    public function test_tag_name_is_validated(): void
    {
        $product = Product::factory()->create();

        $this->post(route('products.tags.store', $product), ['name' => '   '])->assertSessionHasErrors('name');
        $this->post(route('products.tags.store', $product), ['name' => str_repeat('a', 51)])->assertSessionHasErrors('name');

        $this->assertDatabaseCount('tags', 0);
    }

    public function test_tag_can_be_removed_from_a_product_without_affecting_others(): void
    {
        $tag = Tag::factory()->create();
        $a = Product::factory()->create();
        $b = Product::factory()->create();
        $tag->products()->attach([$a->id, $b->id]);

        $this->delete(route('products.tags.destroy', [$a, $tag]))->assertRedirect();

        $this->assertCount(0, $a->fresh()->tags);
        $this->assertCount(1, $b->fresh()->tags);
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    public function test_tag_routes_return_404_for_missing_product(): void
    {
        $this->post(route('products.tags.store', 999), ['name' => 'x'])->assertNotFound();
    }
}

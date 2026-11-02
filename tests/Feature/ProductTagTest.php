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

    private function names(Product $product): array
    {
        return $product->tags()->orderBy('name')->pluck('name')->all();
    }

    public function test_show_page_lists_tags_and_has_the_update_form(): void
    {
        $product = Product::factory()->create();
        $product->tags()->attach(Tag::factory()->create(['name' => 'fragile']));

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('fragile')
            ->assertSee('name="tags[]"', false)
            ->assertSee('Update');
    }

    public function test_search_returns_matching_tag_names_prefix_matches_first(): void
    {
        foreach (['sale', 'wholesale', 'fragile', 'salad'] as $name) {
            Tag::factory()->create(['name' => $name]);
        }

        $this->getJson(route('tags.index', ['q' => 'Sal']))
            ->assertOk()
            ->assertExactJson(['salad', 'sale', 'wholesale']);
    }

    public function test_search_with_blank_query_returns_nothing_and_escapes_wildcards(): void
    {
        Tag::factory()->create(['name' => 'sale']);

        $this->getJson(route('tags.index'))->assertExactJson([]);
        $this->getJson(route('tags.index', ['q' => '%']))->assertExactJson([]);
    }

    public function test_update_attaches_existing_tags_and_creates_new_ones(): void
    {
        $product = Product::factory()->create();
        Tag::factory()->create(['name' => 'sale']);

        $this->put(route('products.tags.update', $product), ['tags' => ['sale', 'brand new']])
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHas('success');

        $this->assertSame(['brand new', 'sale'], $this->names($product));
        $this->assertDatabaseCount('tags', 2);
    }

    public function test_update_detaches_tags_missing_from_the_request(): void
    {
        $product = Product::factory()->create();
        $product->tags()->attach(Tag::factory()->count(2)->create());
        $keep = Tag::factory()->create(['name' => 'keep']);
        $product->tags()->attach($keep);

        $this->put(route('products.tags.update', $product), ['tags' => ['keep']]);

        $this->assertSame(['keep'], $this->names($product));
        $this->assertDatabaseCount('tags', 3);
    }

    public function test_update_without_tags_removes_all(): void
    {
        $product = Product::factory()->create();
        $product->tags()->attach(Tag::factory()->create());

        $this->put(route('products.tags.update', $product))->assertRedirect();

        $this->assertSame([], $this->names($product));
    }

    public function test_update_normalises_and_deduplicates_names(): void
    {
        $product = Product::factory()->create();

        $this->put(route('products.tags.update', $product), ['tags' => ['Sale', ' sale ', 'SALE', '  ']]);

        $this->assertSame(['sale'], $this->names($product));
    }

    public function test_same_tag_can_belong_to_several_products(): void
    {
        $a = Product::factory()->create();
        $b = Product::factory()->create();

        $this->put(route('products.tags.update', $a), ['tags' => ['sale']]);
        $this->put(route('products.tags.update', $b), ['tags' => ['sale']]);

        $this->assertDatabaseCount('tags', 1);
        $this->assertDatabaseCount('product_tag', 2);
    }

    public function test_update_does_not_touch_other_products(): void
    {
        $a = Product::factory()->create();
        $b = Product::factory()->create();
        $tag = Tag::factory()->create();
        $tag->products()->attach([$a->id, $b->id]);

        $this->put(route('products.tags.update', $a), ['tags' => []]);

        $this->assertSame([], $this->names($a));
        $this->assertCount(1, $b->fresh()->tags);
    }

    public function test_update_validates_input(): void
    {
        $product = Product::factory()->create();

        $this->put(route('products.tags.update', $product), ['tags' => 'sale'])->assertSessionHasErrors('tags');
        $this->put(route('products.tags.update', $product), ['tags' => [str_repeat('a', 51)]])->assertSessionHasErrors('tags.0');

        $this->assertDatabaseCount('tags', 0);
    }

    public function test_update_returns_404_for_missing_product(): void
    {
        $this->put(route('products.tags.update', 999), ['tags' => ['x']])->assertNotFound();
    }
}

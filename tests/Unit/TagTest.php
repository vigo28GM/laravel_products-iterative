<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_have_many_tags(): void
    {
        $product = Product::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $product->tags()->attach($tags);

        $this->assertCount(3, $product->fresh()->tags);
    }

    public function test_tag_can_belong_to_many_products(): void
    {
        $tag = Tag::factory()->create();
        $tag->products()->attach(Product::factory()->count(2)->create());

        $this->assertCount(2, $tag->fresh()->products);
    }

    public function test_deleting_a_product_detaches_its_tags_but_keeps_them(): void
    {
        $product = Product::factory()->create();
        $tag = Tag::factory()->create();
        $product->tags()->attach($tag);

        $product->delete();

        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        $this->assertDatabaseCount('product_tag', 0);
    }
}

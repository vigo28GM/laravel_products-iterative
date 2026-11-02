<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->merge(['name' => mb_strtolower(trim((string) $request->input('name')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $tag = Tag::firstOrCreate(['name' => $data['name']]);

        if ($product->tags()->where('tags.id', $tag->id)->exists()) {
            return back()->withErrors(['name' => "Product already has the tag \"{$tag->name}\"."]);
        }

        $product->tags()->attach($tag);

        return back()->with('success', "Tag \"{$tag->name}\" added.");
    }

    public function destroy(Product $product, Tag $tag): RedirectResponse
    {
        $product->tags()->detach($tag);

        return back()->with('success', "Tag \"{$tag->name}\" removed.");
    }
}

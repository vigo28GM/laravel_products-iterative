<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'tags' => ['nullable', 'array', 'max:50'],
            'tags.*' => ['nullable', 'string', 'max:50'],
        ]);

        $names = collect($request->input('tags', []))
            ->map(fn (?string $name) => mb_strtolower(trim((string) $name)))
            ->filter()
            ->unique();

        $product->tags()->sync(
            $names->map(fn (string $name) => Tag::firstOrCreate(['name' => $name])->id)
        );

        return redirect()->route('products.show', $product)->with('success', 'Tags updated.');
    }
}

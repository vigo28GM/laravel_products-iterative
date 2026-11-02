<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', ['products' => Product::latest()->get()]);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validated($request));

        return redirect()->route('products.show', $product)->with('success', 'Product created.');
    }

    public function show(Product $product): View
    {
        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', ['product' => $product]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request));

        return redirect()->route('products.show', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    public function adjustQuantity(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['increase', 'decrease'])],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
        ]);

        if ($data['action'] === 'increase') {
            $product->increaseQuantity($data['amount']);

            return $this->quantityResponse($request, $product, "Added {$data['amount']} to stock.");
        }

        if (! $product->decreaseQuantity($data['amount'])) {
            $message = "Not enough stock: only {$product->quantity} available.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'errors' => ['amount' => [$message]]], 422);
            }

            return back()->withErrors(['amount' => $message]);
        }

        return $this->quantityResponse($request, $product, "Removed {$data['amount']} from stock.");
    }

    private function quantityResponse(Request $request, Product $product, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'quantity' => $product->quantity]);
        }

        return back()->with('success', $message);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'expiration_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(Product::STATUSES)],
        ]);
    }
}

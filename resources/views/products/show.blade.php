<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Price: {{ $product->price }}</p>
    <p>Quantity: {{ $product->quantity }}</p>
    <p>Expiration date: {{ $product->expiration_date?->format('Y-m-d') }}</p>
    <p>Status: {{ $product->status }}</p>
    <form method="POST" action="{{ route('products.adjust-quantity', $product) }}">
        @csrf
        @method('PATCH')
        <label for="amount">Amount</label>
        <input id="amount" name="amount" type="number" min="1" step="1" value="1" required>
        <button type="submit" name="action" value="increase">Add stock</button>
        <button type="submit" name="action" value="decrease">Remove stock</button>
    </form>
    <a href="{{ route('products.edit', $product) }}">Edit</a>
    <a href="{{ route('products.index') }}">Back</a>
</x-layout>

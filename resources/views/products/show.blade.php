<x-layout :title="$product->name">
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Price: {{ $product->price }}</p>
    <p>Quantity: <span id="quantity">{{ $product->quantity }}</span></p>
    <p>Expiration date: {{ $product->expiration_date?->format('Y-m-d') }}</p>
    <p>Status: {{ $product->status }}</p>
    <div id="quantity-flash"></div>
    <form id="quantity-form" method="POST" action="{{ route('products.adjust-quantity', $product) }}">
        @csrf
        @method('PATCH')
        <label for="amount">Amount</label>
        <input id="amount" name="amount" type="number" min="1" step="1" value="1" required>
        <button type="submit" name="action" value="increase">Add stock</button>
        <button type="submit" name="action" value="decrease">Remove stock</button>
    </form>
    <h2>Tags</h2>
    <ul class="tags">
        @forelse ($product->tags as $tag)
            <li>
                {{ $tag->name }}
                <form method="POST" action="{{ route('products.tags.destroy', [$product, $tag]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" aria-label="Remove tag {{ $tag->name }}">&times;</button>
                </form>
            </li>
        @empty
            <li>No tags yet.</li>
        @endforelse
    </ul>
    <form method="POST" action="{{ route('products.tags.store', $product) }}">
        @csrf
        <label for="tag-name">Tag</label>
        <input id="tag-name" name="name" type="text" maxlength="50" value="{{ old('name') }}" required>
        <button type="submit">Add tag</button>
    </form>
    <a href="{{ route('products.edit', $product) }}">Edit</a>
    <a href="{{ route('products.index') }}">Back</a>
</x-layout>

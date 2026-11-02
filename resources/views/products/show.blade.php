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
    <form id="tags-form" method="POST" action="{{ route('products.tags.update', $product) }}"
          data-search-url="{{ route('tags.index') }}">
        @csrf
        @method('PUT')
        <ul class="tags" id="tag-list">
            @foreach ($product->tags as $tag)
                <li data-name="{{ $tag->name }}">
                    {{ $tag->name }}
                    <input type="hidden" name="tags[]" value="{{ $tag->name }}">
                    <button type="button" class="tag-remove" aria-label="Remove tag {{ $tag->name }}">&times;</button>
                </li>
            @endforeach
        </ul>
        <div class="autocomplete">
            <label for="tag-input">Add tag</label>
            <input id="tag-input" type="text" maxlength="50" autocomplete="off"
                   role="combobox" aria-expanded="false" aria-controls="tag-suggestions" aria-autocomplete="list">
            <ul id="tag-suggestions" role="listbox" hidden></ul>
        </div>
        <button type="submit">Update</button>
    </form>
    <a href="{{ route('products.edit', $product) }}">Edit</a>
    <a href="{{ route('products.index') }}">Back</a>
</x-layout>

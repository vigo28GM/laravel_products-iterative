@csrf
<p>
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name', $product->name ?? '') }}">
</p>
<p>
    <label for="price">Price</label>
    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}">
</p>
<p>
    <label for="quantity">Quantity</label>
    <input id="quantity" name="quantity" type="number" min="0" step="1" value="{{ old('quantity', $product->quantity ?? 0) }}">
</p>
<p>
    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $product->description ?? '') }}</textarea>
</p>
<p>
    <label for="expiration_date">Expiration date</label>
    <input id="expiration_date" name="expiration_date" type="date" value="{{ old('expiration_date', isset($product) ? $product->expiration_date?->format('Y-m-d') : '') }}">
</p>
<p>
    <label for="status">Status</label>
    <select id="status" name="status">
        @foreach (\App\Models\Product::STATUSES as $status)
            <option value="{{ $status }}" @selected(old('status', $product->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</p>
<button type="submit">Save</button>
<a href="{{ route('products.index') }}">Cancel</a>

@csrf
@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
<p>
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name', $product->name ?? '') }}">
</p>
<p>
    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $product->description ?? '') }}</textarea>
</p>
<p>
    <label for="price">Price</label>
    <input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price ?? '') }}">
</p>
<button type="submit">Save</button>
<a href="{{ route('products.index') }}">Cancel</a>

<x-layout title="Products">
    <h1>Products</h1>
    <table>
        <thead>
            <tr><th>Name</th><th>Price</th><th></th></tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                    <td>{{ $product->price }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}">Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">No products yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-layout>

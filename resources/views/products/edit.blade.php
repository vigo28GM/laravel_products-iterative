<x-layout title="Edit product">
    <h1>Edit {{ $product->name }}</h1>
    <form method="POST" action="{{ route('products.update', $product) }}">
        @method('PUT')
        @include('products._form')
    </form>
</x-layout>

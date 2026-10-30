<x-layout title="New product">
    <h1>New product</h1>
    <form method="POST" action="{{ route('products.store') }}">
        @include('products._form')
    </form>
</x-layout>

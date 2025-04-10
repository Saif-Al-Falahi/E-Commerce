@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            @if($product->image)
                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-96 object-cover rounded-lg">
            @else
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-500">No image</span>
                </div>
            @endif
        </div>

        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-6">{{ $product->description }}</p>

            <div class="mb-6">
                <span class="text-2xl font-bold">{{ $product->formatted_price }}</span>
                <span class="ml-4 text-gray-600">Stock: {{ $product->stock }}</span>
            </div>

            <div class="mb-6">
                <p class="text-gray-600">Category: <a href="{{ route('categories.show', $product->category) }}" class="text-blue-500 hover:text-blue-700">{{ $product->category->name }}</a></p>
            </div>

            @auth
                @if(Auth::user()->is_admin)
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit Product
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Delete Product
                            </button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex items-center space-x-4">
                        @csrf
                        <div>
                            <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                            <select name="quantity" id="quantity"
                                class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @for($i = 1; $i <= min($product->stock, 10); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            {{ $product->stock < 1 ? 'disabled' : '' }}>
                            {{ $product->stock < 1 ? 'Out of Stock' : 'Add to Cart' }}
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection 
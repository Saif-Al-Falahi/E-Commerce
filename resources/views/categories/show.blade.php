@extends('layouts.app')

@section('content')
<div class="card p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $category->description }}</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn-secondary">
            Back to Categories
        </a>
    </div>
    
    @if($category->products->count() > 0)
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Products in this category</h3>
        
        <div class="product-grid">
            @foreach($category->products as $product)
                <div class="product-card">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" 
                            alt="{{ $product->name }}"
                            class="product-image">
                    @else
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-md mb-4">
                            <span class="text-gray-400">No image available</span>
                        </div>
                    @endif

                    <div class="flex-grow">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="product-price">${{ number_format($product->price, 2) }}</span>
                            <span class="text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mt-auto">
                            <a href="{{ route('products.show', $product) }}"
                                class="text-blue-600 hover:text-blue-700 font-medium">View Details</a>
                            
                            @auth
                                @if(Auth::user()->is_admin)
                                    <div class="flex space-x-3">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-yellow-600 hover:text-yellow-700">Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="btn-primary text-sm"
                                            {{ $product->stock < 1 ? 'disabled' : '' }}>
                                            {{ $product->stock < 1 ? 'Out of Stock' : 'Add to Cart' }}
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-100 p-6 rounded-lg text-center">
            <p class="text-gray-600">No products found in this category.</p>
        </div>
    @endif
</div>
@endsection 
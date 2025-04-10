@extends('layouts.app')

@section('content')
<div class="card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            @if(request('category') && isset($selectedCategory))
                {{ $selectedCategory->name }} Products
            @else
                All Products
            @endif
        </h2>
        @auth
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.products.create') }}" class="btn-primary">
                    Add New Product
                </a>
            @endif
        @endauth
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-wrap items-end" style="gap: 20px;">
            <div class="w-64">
                <div class="relative">
                    <input type="text" name="search" id="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search products..."
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-3 pr-10 py-2 text-sm">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="w-48">
                <select name="category" id="category"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <select name="sort" id="sort"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 text-sm">
                    <option value="">Latest First</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm">
                    Filter
                </button>
            </div>
            @if(request()->anyFilled(['search', 'category', 'sort']))
                <div>
                    <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 py-2 px-2 text-sm inline-flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                </div>
            @endif
        </form>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-500">No products found. Try adjusting your search.</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-blue-500 hover:underline">View all products</a>
        </div>
    @else
        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                    @if($product->image)
                        <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" 
                            alt="{{ $product->name }}"
                            class="product-image">
                    @else
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-md mb-4">
                            <span class="text-gray-400">No image available</span>
                        </div>
                    @endif

                    <div class="flex-grow">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ $product->description }}</p>
                        <div class="text-xs text-blue-600 mb-4">{{ $product->category->name }}</div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="product-price">{{ $product->formatted_price }}</span>
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
    @endif

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection 
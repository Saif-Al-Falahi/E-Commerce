@extends('admin.layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-8 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Manage Products</h2>
            <a href="{{ route('admin.products.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                
                Add New Product
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mx-8 my-6" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="overflow-x-auto px-8 py-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-5 whitespace-nowrap">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-full object-cover">
                        @else
                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ Str::limit($product->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500">
                        ${{ number_format($product->price, 2) }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        @if($product->stock > 0)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            In Stock ({{ $product->stock }})
                        </span>
                        @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Out of Stock
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium space-x-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-8 py-6 border-t border-gray-200">
        {{ $products->links() }}
    </div>
</div>
@endsection 
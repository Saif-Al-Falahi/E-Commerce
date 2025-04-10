@extends('admin.layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-blue-100 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Products</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $stats['products_count'] }}</p>
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">Manage Products →</a>
    </div>
    
    <div class="bg-green-100 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-green-800 mb-2">Categories</h3>
        <p class="text-3xl font-bold text-green-600">{{ $stats['categories_count'] }}</p>
        <a href="{{ route('admin.categories.index') }}" class="text-green-600 hover:text-green-800 text-sm mt-2 inline-block">Manage Categories →</a>
    </div>
    
    <div class="bg-purple-100 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-purple-800 mb-2">Users</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $stats['users_count'] }}</p>
        <a href="{{ route('admin.users.index') }}" class="text-purple-600 hover:text-purple-800 text-sm mt-2 inline-block">Manage Users →</a>
    </div>
    
    <div class="bg-yellow-100 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Carts</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ $stats['carts_count'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Products</h3>
        @if($recent_products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recent_products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No products found.</p>
        @endif
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Low Stock Products</h3>
        @if($low_stock_products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($low_stock_products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No low stock products found.</p>
        @endif
    </div>
</div>
@endsection 
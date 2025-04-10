@extends('admin.layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-8 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Manage Categories</h2>
            <a href="{{ route('admin.categories.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                
                Add New Category
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
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="text-sm text-gray-500">{{ Str::limit($category->description, 100) }}</div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $category->products_count }} products
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium space-x-3">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this category? This will also delete all associated products.')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-8 py-6 border-t border-gray-200">
        {{ $categories->links() }}
    </div>
</div>
@endsection 
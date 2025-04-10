@extends('layouts.app')

@section('content')
<div class="card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Categories</h2>
        @auth
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.categories.create') }}" class="btn-primary">
                    Add New Category
                </a>
            @endif
        @endauth
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="card p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex justify-between items-start">
                    <div class="flex-grow">
                        <h3 class="text-xl font-semibold mb-2">
                            <a href="{{ route('categories.show', $category) }}"
                                class="text-blue-600 hover:text-blue-700 transition-colors duration-200">
                                {{ $category->name }}
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $category->description }}</p>
                        <p class="text-sm font-medium {{ $category->products_count > 0 ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}
                        </p>
                    </div>

                    @auth
                        @if(Auth::user()->is_admin)
                            <div class="flex space-x-3 ml-4">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                    class="text-yellow-600 hover:text-yellow-700 transition-colors duration-200">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 transition-colors duration-200">Delete</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $categories->links() }}
    </div>
</div>
@endsection 
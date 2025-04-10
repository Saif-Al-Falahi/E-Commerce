@extends('admin.layouts.app')

@section('admin-content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-8 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Edit Category: {{ $category->name }}</h2>
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary flex items-center">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Categories
            </a>
        </div>
    </div>

    <div class="p-8">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="max-w-2xl">
            @csrf
            @method('PATCH')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="Enter category name">
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="5" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                    placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary flex items-center">
                    <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 
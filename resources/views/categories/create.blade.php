@extends('layouts.app')

@section('content')
<div class="card p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Category</h2>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="form-input @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="4" 
                class="form-input @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="btn-primary">
                Create Category
            </button>
            <a href="{{ route('categories.index') }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection 
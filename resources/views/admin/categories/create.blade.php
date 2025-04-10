@extends('admin.layouts.app')

@section('admin-content')
<div class="max-w-2xl mx-auto mt-8 bg-white rounded-lg shadow-sm p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Create New Category</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <table class="w-full">
            <tbody>
                <tr>
                    <td class="pb-6 align-top">
                        <label for="name" class="text-base text-gray-900">Name</label>
                    </td>
                    <td class="pb-6">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter category name">
                        @error('name')
                            <p class="mt-1 text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td class="pb-6 align-top">
                        <label for="description" class="text-base text-gray-900">Description</label>
                    </td>
                    <td class="pb-6">
                        <textarea name="description" id="description" rows="6"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter category description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Create Category
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
@endsection 
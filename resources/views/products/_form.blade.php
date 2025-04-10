@csrf

<div class="mb-4">
    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" required
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
    @error('name')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
    <textarea name="description" id="description" rows="4" required
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
    <input type="number" name="price" id="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" min="0" required
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror">
    @error('price')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">Stock</label>
    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock') border-red-500 @enderror">
    @error('stock')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
    <select name="category_id" id="category_id" required
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_id') border-red-500 @enderror">
        <option value="">Select a category</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-6">
    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Product Image</label>
    <input type="file" name="image" id="image" accept="image/*"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('image') border-red-500 @enderror">
    @error('image')
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
    @if(isset($product) && $product->image)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover">
        </div>
    @endif
</div>

<div class="flex items-center justify-between">
    <button type="submit"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        {{ isset($product) ? 'Update Product' : 'Create Product' }}
    </button>
    <a href="{{ route('products.index') }}"
        class="text-gray-600 hover:text-gray-800">
        Cancel
    </a>
</div> 
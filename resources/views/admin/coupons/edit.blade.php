@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Coupon: {{ $coupon->code }}</h1>
        <a href="{{ route('admin.coupons.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Coupons
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Coupon Code</label>
                    <input type="text" name="code" id="code" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('code', $coupon->code) }}" required>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                    <select name="type" id="type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            required>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700">Discount Value</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="value-symbol">{{ config('ecommerce.currency.symbol') }}</span>
                        </div>
                        <input type="number" name="value" id="value" step="0.01" min="0" 
                               class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               value="{{ old('value', $coupon->value) }}" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="value-type">%</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="min_purchase" class="block text-sm font-medium text-gray-700">Minimum Purchase Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">{{ config('ecommerce.currency.symbol') }}</span>
                        </div>
                        <input type="number" name="min_purchase" id="min_purchase" step="0.01" min="0" 
                               class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               value="{{ old('min_purchase', $coupon->min_purchase) }}">
                    </div>
                </div>

                <div>
                    <label for="max_uses" class="block text-sm font-medium text-gray-700">Maximum Uses</label>
                    <input type="number" name="max_uses" id="max_uses" min="0" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('max_uses', $coupon->max_uses) }}" 
                           placeholder="Leave empty for unlimited uses">
                    <p class="mt-1 text-sm text-gray-500">Current uses: {{ $coupon->times_used }}</p>
                </div>

                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="starts_at" id="starts_at" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}">
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                    <input type="date" name="expires_at" id="expires_at" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d')) }}">
                </div>

                <div class="col-span-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Update Coupon
                </button>
            </div>
        </form>

        <div class="mt-4">
            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                        onclick="return confirm('Are you sure you want to delete this coupon?')">
                    Delete Coupon
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const valueSymbol = document.getElementById('value-symbol');
        const valueType = document.getElementById('value-type');

        function updateValueSymbols() {
            if (typeSelect.value === 'percentage') {
                valueSymbol.style.display = 'none';
                valueType.style.display = 'block';
            } else {
                valueSymbol.style.display = 'block';
                valueType.style.display = 'none';
            }
        }

        typeSelect.addEventListener('change', updateValueSymbols);
        updateValueSymbols();
    });
</script>
@endpush

@endsection 
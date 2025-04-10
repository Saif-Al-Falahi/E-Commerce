@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    {{-- Single Notification Section --}}
    <div class="notifications mb-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Shopping Cart</h2>
        @if($cart->cartItems->count() > 0)
            <form action="{{ route('cart.clear', $cart) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to clear your cart?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700">Clear Cart</button>
            </form>
        @endif
    </div>

    @if($cart->cartItems->count() > 0)
        <div class="space-y-4">
            @foreach($cart->cartItems as $item)
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center space-x-4">
                        @if($item->product->image)
                            <img src="{{ str_starts_with($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image) }}"
                                alt="{{ $item->product->name }}"
                                class="w-16 h-16 object-cover rounded">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-500 text-sm">No image</span>
                            </div>
                        @endif

                        <div>
                            <h3 class="font-semibold">{{ $item->product->name }}</h3>
                            <p class="text-gray-600">{{ $item->product->formatted_price }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center">
                            @csrf
                            @method('PATCH')
                            <div class="relative">
                                <select name="quantity" onchange="this.form.submit()"
                                    class="block w-20 appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-5 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    @for($i = 1; $i <= $item->product->stock; $i++)
                                        <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-700">
                                    
                                </div>
                            </div>
                        </form>

                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Remove</button>
                        </form>

                        <span class="font-semibold">{{ $item->formatted_subtotal }}</span>
                    </div>
                </div>
            @endforeach

            <div class="mt-6 border-t pt-4">
                @if($cart->coupon)
                    <div class="bg-blue-50 p-4 rounded-md mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-blue-600">Coupon applied: <span class="font-semibold">{{ $cart->coupon->code }}</span></p>
                                <p class="text-xs text-blue-500">Discount: {{ $cart->formatted_discount }}</p>
                            </div>
                            <form action="{{ route('coupon.remove', $cart) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Remove Coupon</button>
                            </form>
                        </div>
                    </div>
                @else
                    <form action="{{ route('coupon.apply') }}" method="POST" class="flex space-x-2 mb-4">
                        @csrf
                        <input type="text" 
                            name="code" 
                            placeholder="Have a coupon code? Enter it here" 
                            class="shadow appearance-none border rounded w-64 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                            value="{{ old('code') }}">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                            Apply Coupon
                        </button>
                    </form>
                @endif

                <div class="flex flex-col items-end space-y-2">
                    <p class="text-gray-600">Subtotal: {{ $cart->formatted_subtotal }}</p>
                    @if($cart->discount_amount > 0)
                        <p class="text-green-600">Discount: -{{ $cart->formatted_discount }}</p>
                    @endif
                    <p class="text-lg font-semibold">Total: {{ $cart->formatted_total }}</p>
                    <button onclick="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-600">Your cart is empty.</p>
            <a href="{{ route('products.index') }}"
                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Continue Shopping
            </a>
        </div>
    @endif

    {{-- Checkout Modal --}}
    <div id="checkoutModal" class="fixed inset-0 bg-black bg-opacity-40 hidden h-full backdrop-blur-sm transition-all duration-300 flex items-center justify-center" style="z-index: 100;">
        <div class="relative p-6 border w-[28rem] max-h-[90vh] overflow-y-auto shadow-xl rounded-xl bg-white transform transition-all duration-300 ease-out">
            <div class="mt-2">
                <h3 class="text-xl leading-6 font-semibold text-gray-900 text-center sticky top-0 bg-white pb-4">Confirm Your Order</h3>
                <div class="mt-4 px-2 py-3">
                    <p class="text-base text-gray-600 text-center mb-6">
                        Please review your order details before confirming
                    </p>
                    <div class="space-y-4 bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-600">Total Items</p>
                            <p class="text-sm font-medium text-gray-900">{{ $cart->cartItems->sum('quantity') }}</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-600">Subtotal</p>
                            <p class="text-sm font-medium text-gray-900">{{ $cart->formatted_subtotal }}</p>
                        </div>
                        @if($cart->discount_amount > 0)
                            <div class="flex justify-between items-center text-green-600">
                                <p class="text-sm">Discount Applied</p>
                                <p class="text-sm font-medium">-{{ $cart->formatted_discount }}</p>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <p class="text-base font-semibold text-gray-900">Total</p>
                                <p class="text-base font-semibold text-gray-900">{{ $cart->formatted_total }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 space-y-3">
                    <form id="checkoutForm" action="{{ route('cart.checkout', $cart) }}" method="POST" class="space-y-3">
                        @csrf
                        <button type="submit" class="w-full mb-2 px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition-colors duration-200">
                            Confirm Order
                        </button>
                        <button type="button" onclick="closeModal()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('checkoutModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('checkoutModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('checkoutModal').addEventListener('click', function(e) {
        if (e.target.id === 'checkoutModal') {
            closeModal();
        }
    });

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
@endpush
@endsection 
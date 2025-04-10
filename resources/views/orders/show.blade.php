@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Order #{{ $cart->id }}</h2>
            <p class="text-gray-600">Placed on {{ $cart->updated_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-2">Order Summary</h3>
        <div class="bg-gray-50 p-4 rounded-md">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Order ID:</span>
                <span>{{ $cart->id }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Order Date:</span>
                <span>{{ $cart->updated_at->format('F d, Y') }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Total Items:</span>
                <span>{{ $cart->cartItems->sum('quantity') }}</span>
            </div>
            <div class="flex justify-between font-semibold">
                <span>Total:</span>
                <span>{{ $cart->formatted_total }}</span>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold mb-2">Order Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b text-left">Product</th>
                        <th class="py-3 px-4 border-b text-left">Price</th>
                        <th class="py-3 px-4 border-b text-left">Quantity</th>
                        <th class="py-3 px-4 border-b text-left">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart->cartItems as $item)
                        <tr>
                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center">
                                    @if($item->product->image)
                                        <img src="{{ str_starts_with($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image) }}" 
                                            alt="{{ $item->product->name }}" 
                                            class="w-12 h-12 object-cover rounded mr-4">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded mr-4 flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">No image</span>
                                        </div>
                                    @endif
                                    <span>{{ $item->product->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 border-b">{{ $item->product->formatted_price }}</td>
                            <td class="py-3 px-4 border-b">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 border-b">{{ $item->formatted_subtotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="py-3 px-4 text-right font-semibold">Total:</td>
                        <td class="py-3 px-4 font-semibold">{{ $cart->formatted_total }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection 
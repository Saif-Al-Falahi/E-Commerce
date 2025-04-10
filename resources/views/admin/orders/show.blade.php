@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Order #{{ $cart->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Orders
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Order Information</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-medium">#{{ $cart->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span>{{ $cart->updated_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Completed
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-semibold">${{ number_format($cart->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span>{{ $cart->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span>{{ $cart->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer Since:</span>
                    <span>{{ $cart->user->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Products:</span>
                    <span>{{ $cart->cartItems->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Items:</span>
                    <span>{{ $cart->cartItems->sum('quantity') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Value:</span>
                    <span class="font-semibold">${{ number_format($cart->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subtotal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cart->cartItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($item->product->image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="{{ str_starts_with($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image) }}"
                                                alt="{{ $item->product->name }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">No image</span>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->product->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $item->product->category->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${{ number_format($item->product->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">${{ number_format($item->subtotal, 2) }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-medium">
                            Total:
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">${{ number_format($cart->total, 2) }}</div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection 
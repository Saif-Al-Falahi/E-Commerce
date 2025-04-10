@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">My Orders</h2>
        <p class="text-gray-600">View your order history</p>
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

    @if($orders->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-600">You don't have any orders yet.</p>
            <a href="{{ route('products.index') }}" 
                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Start Shopping
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b text-left">Order ID</th>
                        <th class="py-3 px-4 border-b text-left">Date</th>
                        <th class="py-3 px-4 border-b text-left">Total</th>
                        <th class="py-3 px-4 border-b text-left">Items</th>
                        <th class="py-3 px-4 border-b text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="py-3 px-4 border-b">{{ $order->id }}</td>
                            <td class="py-3 px-4 border-b">{{ $order->updated_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4 border-b">{{ $order->formatted_total }}</td>
                            <td class="py-3 px-4 border-b">{{ $order->cartItems->count() }}</td>
                            <td class="py-3 px-4 border-b">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection 
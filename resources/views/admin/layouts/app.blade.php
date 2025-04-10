<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel E-Commerce') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-700 transition-colors duration-200">
                                {{ config('app.name', 'Laravel E-Commerce') }} <span class="text-sm text-gray-500">Admin</span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.products.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.products.*') ? 'border-blue-500 text-gray-900 font-medium' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Products
                            </a>
                            <a href="{{ route('admin.categories.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.categories.*') ? 'border-blue-500 text-gray-900 font-medium' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Categories
                            </a>
                            <a href="{{ route('admin.coupons.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.coupons.*') ? 'border-blue-500 text-gray-900 font-medium' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Coupons
                            </a>
                            <a href="{{ route('admin.orders.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.orders.*') ? 'border-blue-500 text-gray-900 font-medium' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Orders
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium flex items-center">
                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            View Site
                        </a>
                        <div class="relative">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html> 
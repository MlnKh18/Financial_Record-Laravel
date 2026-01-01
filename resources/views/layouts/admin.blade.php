<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex">
    {{-- SIDEBAR --}}
    @include('layouts.partials.admin-sidebar')

    {{-- CONTENT --}}
    <main class="flex-1 p-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>

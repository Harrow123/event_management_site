<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Evently</title>
    <link href="/event_management_site/public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<header class="bg-gradient-to-r from-green-400 to-blue-500 text-white">
    <nav class="container mx-auto flex items-center justify-between p-4">
        <!-- Admin Logo and brand name -->
        <a href="/event_management_site/admin" class="text-2xl font-bold">Evently Admin</a>
        
        <div class="flex items-center relative">
            <!-- Dashboard Link -->
            <a href="/event_management_site/admin/" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Dashboard</a>
            
            <!-- Events Dropdown -->
            <div class="relative w-full" x-data="{ open: false }">
                <button @click="open = !open" class="relative z-10 hover:bg-white hover:text-green-400 px-3 py-2 rounded transition focus:outline-none">Events</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white text-black mt-1 rounded shadow-lg py-1" style="display: none;" x-cloak>
                    <a href="/event_management_site/admin/events" class="block px-4 py-2 hover:bg-gray-200">All Events</a>
                    <a href="/event_management_site/admin/events/create" class="block px-4 py-2 hover:bg-gray-200">Create Event</a>
                </div>
            </div>

            <!-- Users Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="relative z-10 hover:bg-white hover:text-green-400 px-3 py-2 rounded transition focus:outline-none">Users</button>
                <div x-show="open" @click.away="open = false" class="absolute z-20 bg-white text-black mt-1 rounded shadow-lg py-1" style="display: none;" x-cloak>
                    <a href="/event_management_site/admin/users" class="block px-4 py-2 hover:bg-gray-200">Manage Users</a>
                    <a href="/event_management_site/admin/users/create" class="block px-4 py-2 hover:bg-gray-200">Create User</a>
                </div>
            </div>

            <!-- Logout Link -->
            <a href="/event_management_site/admin/logout" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Logout</a>
        </div>
    </nav>
</header>

    <!-- Alpine.js for dropdown interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.0/dist/alpine.js" defer></script>

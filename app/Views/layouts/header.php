<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Discovery Platform</title>
    <link href="/event_management_site/public/css/tailwind.css" rel="stylesheet">
</head>
<body>
    <header class="bg-gradient-to-r from-green-400 to-blue-500 text-white">
        <nav class="container mx-auto flex items-center justify-between p-4">
            <!-- Logo and brand name -->
            <a href="<?php echo $base_url; ?>" class="text-2xl font-bold">Evently</a>
            <?php $current_page = $_SERVER['REQUEST_URI']; ?>
            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="<?php echo $base_url; ?>" class="<?php echo ($current_page == '/' ? 'active' : ''); ?> hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Home</a>
                <a href="<?php echo $base_url; ?>about" class="<?php echo ($current_page == '/' ? 'active' : ''); ?> hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">About</a>
                <a href="<?php echo $base_url; ?>contact" class="<?php echo ($current_page == '/' ? 'active' : ''); ?> hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Contact</a>
                <a href="<?php echo $base_url; ?>events" class="<?php echo ($current_page == '/' ? 'active' : ''); ?> hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Find Events</a>
                
                <!-- Conditional authentication links -->
                <?php if ($authentication->isLoggedIn()) : ?>
                    <a href="<?php echo $base_url; ?>users/events" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">My Events</a>
                    <a href="<?php echo $base_url; ?>events/create" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Create Event</a>
                    <a href="<?php echo $base_url; ?>users/profile" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Profile</a>
                    <a href="<?php echo $base_url; ?>auth/logout" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Logout</a>
                <?php else : ?>
                    <a href="<?php echo $base_url; ?>auth/login" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Login</a>
                    <a href="<?php echo $base_url; ?>auth/register" class="hover:bg-white hover:text-green-400 px-3 py-2 rounded transition">Register</a>
                <?php endif; ?>
            </div>
            <!-- Mobile Menu Button -->
            <button class="md:hidden flex items-center px-3 py-2 border rounded text-white border-white">
                <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0z"/><path d="M0 9h20v2H0z"/><path d="M0 15h20v2H0z"/></svg>
            </button>
        </nav>
    </header>


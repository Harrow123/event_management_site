<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management Site</title>
    <!-- Tailwind CSS Link -->
    <link href="/event_management_site/public/css/tailwind.css" rel="stylesheet">
</head>
<body>
<header>
    <!-- Navigation bar, logo, etc. -->
    <nav>
        <ul>
            <li><a href="<?php echo $base_url; ?>">Home</a></li>
            <li><a href="/events/list">Events</a></li>
            <li><a href="<?php echo $base_url; ?>users/profile">Profile</a></li>
            <!-- Additional links here -->
        </ul>
    </nav>
</header>

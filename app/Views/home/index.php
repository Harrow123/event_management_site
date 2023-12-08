<div class="container mx-auto px-4 bg-red-400">
    <section class="hero">
        <h1>Welcome to the Event Management System</h1>
        <p>Find and manage events with ease.</p>
    </section>

    <section class="featured-events">
        <h2>Featured Events</h2>
        <!-- Loop through featured events and display them -->
        <?php foreach ($featuredEvents as $event): ?>
            <div class="event">
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                <p><?php echo htmlspecialchars($event['description']); ?></p>
                <!-- More event details here -->
            </div>
        <?php endforeach; ?>
    </section>

    <section class="about">
        <h2>About Us</h2>
        <p>
            Learn more about our mission and services.
            <!-- Further about us details -->
        </p>
    </section>
</div>

<style>
    /* Include some inline CSS or link to an external stylesheet */
</style>

<script>
    // JavaScript for interactive elements, if any
</script>
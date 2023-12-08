<?php
class HomeController {
    public function index($twig) {
        // Fetch featured events or any other necessary data
        // $featuredEvents = []; // Assume this is fetched from the model

        // Dummy events, these will be fetched from the database using the model
        $featuredEvents = [
            [
                'title' => 'Tech Conference 2023',
                'description' => 'An annual conference for tech enthusiasts.',
                'date' => '2023-03-15',
                'location' => 'Convention Center, Techville'
            ],
            [
                'title' => 'Art and Design Expo',
                'description' => 'Explore the latest trends in art and design.',
                'date' => '2023-04-20',
                'location' => 'Downtown Gallery, ArtCity'
            ],
            [
                'title' => 'Music Festival Summer',
                'description' => 'A festival celebrating the best of summer music.',
                'date' => '2023-07-05',
                'location' => 'Open Air Park, MusicLand'
            ]
        ];
        

        // Render the home view using Twig
        echo $twig->render('home/index.html.twig', ['featuredEvents' => $featuredEvents]);
    }
}

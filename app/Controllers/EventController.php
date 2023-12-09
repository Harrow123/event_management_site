<?php

class EventController {
    private $twig;
    private $eventModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->eventModel = new Event($pdo);
    }

    public function listEvents($twig) {
        // Fetch events from the Event model
        // Render the list.html.twig with fetched events
        echo $this->twig->render('events/list.html.twig', ['events' => $events]);
    }

    public function viewEvent($twig, $eventId) {
        // Fetch event details from the Event model
        // Render the detail.html.twig with event details
    }

    // Add methods for creating, updating, and deleting events
    public function createEvent($eventData) {
        // Validate and create event
    }

    public function deleteEvent($eventId) {
        // Validate and create event
    }
}

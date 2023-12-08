<?php
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/app/Views');
$twig = new \Twig\Environment($loader, [
    'debug' => true
    // 'cache' => __DIR__ . '/path/to/cache', // Uncomment this line in production(go live)
]);

return $twig;

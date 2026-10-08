<?php    
    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    // Configuration d'accès à la base de données
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_NAME', $_ENV['DB_NAME']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);

    // Configuration du TIME OUT de session
    const SESSION_TIMEOUT = 1800; // 30 minutes (1800 secondes)
?>
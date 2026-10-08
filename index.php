<?php
    require_once __DIR__.'/models/Usager.php';
    session_start();
    require_once __DIR__.'/includes/config.php';
    require_once __DIR__.'/includes/bd.php';
    require_once __DIR__.'/includes/auth.php';
    require_once __DIR__.'/includes/initApp.php';

    // Ajoute un compte administrateur
    InitApp::init();

    $page = $_GET['page'] ?? 'accueil';

    // Enlève tout sauf les lettres, les chiffres et les tirets pour la sécurité
    // (évite de pouvoir chercher des fichers EX: ../password)
    $page = preg_replace('/[^a-z0-9\-]/', '', $page); 

    $pagesAutorisees = [
        '404' => true,
        '403' => true,
        'accueil' => true, 
        'document' => Auth::estConnecte(),
        'usager' => Auth::estAdminOuEmploye(),
        'transaction' => Auth::estAdministrateur(),        
        'login' => !Auth::estConnecte(),
        'logout' => Auth::estConnecte()
    ];

    if (!isset($pagesAutorisees[$page])) {        
        $page = '404'; // Page non trouvée (404)
    } elseif (!$pagesAutorisees[$page]) {        
        $page = '403'; // Accès refusé (403) 
    }

    if (!Auth::estConnecte() 
        && $page !== 'login'
        && $page !== '404'
        && $page !== '403'
        && $page !== 'accueil') {
        header('Location: index.php?page=login');
        exit();
    }

    require_once  __DIR__.'/layouts/header.php';
    require_once  __DIR__.'/layouts/siteFrame.php';
    echo '<main>';
    require_once "pages/{$page}.php";
    echo '</main>';
    require_once __DIR__.'/layouts/footer.php';
?>
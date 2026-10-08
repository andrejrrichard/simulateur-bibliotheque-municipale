<?php
    Auth::logout();
    header('Location: index.php?page=accueil');
    exit();
?>
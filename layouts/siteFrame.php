<header>
    <h1>
        Bienvenue à la bibliothèque municipale
        <?php if (Auth::estConnecte()): ?>
            cher <?= htmlspecialchars($_SESSION['usager']->getType()) ?> <?= htmlspecialchars($_SESSION['usager']->getCodeNom()) ?>
        <?php else: ?>
            cher Visiteur
        <?php endif; ?>
    </h1>
    <nav class="navbar navbar-expand-lg mb-2"> 
        <div class="container-fluid">   
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav">
                    <?php $currentPage = $_GET['page'] ?? 'accueil';?>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage === 'accueil') ? 'menu-selectionner' : '' ?>" 
                            href="?page=accueil">
                                Accueil
                        </a>
                    </li>
                    
                    <?php if (Auth::estConnecte()): ?>            
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage === 'document') ? 'menu-selectionner' : '' ?>" 
                                href="?page=document">
                                    Documents
                            </a>
                        </li>
                        
                        <?php if (Auth::estAdminOuEmploye()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($currentPage === 'usager') ? 'menu-selectionner' : '' ?>" 
                                    href="?page=usager">
                                    <?= Auth::estAdministrateur() ? 'Usagers' : 'Membres' ?>
                                </a>
                            </li>
                        <?php endif; ?>  

                        <?php if (Auth::estAdministrateur()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($currentPage === 'transaction') ? 'menu-selectionner' : '' ?>" 
                                    href="?page=transaction">
                                    Transactions
                                </a>
                            </li>
                        <?php endif; ?>   
                        
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage === 'logout') ? 'menu-selectionner' : '' ?>" 
                                href="?page=logout">
                                Déconnexion
                            </a>
                        </li>        
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage === 'login') ? 'menu-selectionner' : '' ?>" 
                                href="?page=login">
                                Connexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>            
        </div>
    </nav>
</header>
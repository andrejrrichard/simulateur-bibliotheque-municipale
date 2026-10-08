<?php
    require_once __DIR__ .'/../controllers/Usager.php';
    extract(UsagerController::traiteRequete());
?>
  
<?php if ($mode === 'ajouter' && Auth::estAdministrateur()): ?>
    <div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">
        <div class="card bg-transparent border-warning">
            <div class="card-header d-flex justify-content-between align-items-center border-warning">
                <h3 class="mb-0">Formulaire d'ajout d'un usager</h3>
                <form method="GET" action="">
                    <input type="hidden" name="page" value="usager">
                    <?php if (!empty($rechercheMot)): ?>
                        <input type="hidden" name="rechercheMot" value="<?= htmlspecialchars($rechercheMot) ?>">
                    <?php endif; ?>
                    <button class="btn couleur-marine" type="submit">✕</button>
                </form>  
            </div>
            
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="page" value="usager">
                    
                    <div class="mb-3">
                        <span class="text-warning">* Ces champs sont obligatoires</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code usager: *</label>
                            <input class="form-control" type="text" name="code" 
                                   value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" 
                                   placeholder="Code usager">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mot de passe: *</label>
                            <input class="form-control" type="password" name="motDePasse" 
                                   placeholder="Mot de passe">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type d'usager: *</label>
                            <select class="form-select" name="usagerType" required>
                                <option value="">Sélectionnez un type</option>
                                <?php foreach (Usager::getAllTypes() as $code): ?>
                                    <option value="<?= htmlspecialchars($code) ?>" 
                                        <?= isset($_POST['usagerType']) && $_POST['usagerType'] == $code ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($code) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Courriel:</label>
                            <input class="form-control" type="email" name="courriel" 
                                   value="<?= htmlspecialchars($_POST['courriel'] ?? '') ?>" 
                                   placeholder="courriel@exemple.com">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom:</label>
                            <input class="form-control" type="text" name="nom" 
                                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" 
                                   placeholder="Nom complet">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone:</label>
                            <input class="form-control" type="tel" name="telephone" 
                                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" 
                                   placeholder="(555) 123-4567">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Adresse:</label>
                            <input class="form-control" type="text" name="adresse" 
                                   value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>" 
                                   placeholder="Adresse complète">
                        </div>
                    </div>
                    
                    <button class="btn mt-2 couleur-marine" type="submit">Ajouter</button>
                </form>
            </div>
        </div>
    </div>                          
<?php endif; ?>

<?php if ($mode === 'voir'): ?>
    <div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">
        <div class="card bg-transparent border-warning d-flex flex-column" style="min-height: 0;">
            <div class="card-header d-flex justify-content-between align-items-center border-warning">
                <h3 class="mb-0"><?= htmlspecialchars($usagerSelectionne->getCodeNom()) ?></h3>
                <form method="GET" action="">
                    <input type="hidden" name="page" value="usager">
                    <?php if (!empty($rechercheMot)): ?>
                        <input type="hidden" name="rechercheMot" value="<?= htmlspecialchars($rechercheMot) ?>">
                    <?php endif; ?>
                    <button class="btn couleur-marine" type="submit">✕</button>
                </form>  
            </div>
            
            <div class="card-body d-flex flex-column" style="min-height: 0; flex: 1;">
                <h4 class="mb-3">Liste des documents associés</h4>

                <div class="d-flex flex-column" style="min-height: 0; flex: 1;">  
                    <div class="d-flex border-bottom border-warning pb-2 mb-2" style="gap: 10px; flex-shrink: 0;">
                        <div class="table-header-cell">Statut</div>                
                        <div class="table-header-cell">Retour prévu</div>
                        <div class="table-header-cell">Titre</div>
                    </div>

                    <div class="table-body-scroll" style="min-height: 0; flex: 1;">
                        <?php if ($documentsUsager): ?>                                
                            <?php foreach ($documentsUsager as $document): ?>
                                <div class="d-flex border-bottom border-warning py-2" style="gap: 10px;">
                                    <div class="table-cell">
                                        <span class="badge <?= $document->getStatutCouleur() ?>">
                                            <?= htmlspecialchars($document->getStatut()) ?>
                                        </span>
                                    </div>
                                    <div class="table-cell date-local">
                                        <?= htmlspecialchars($document->getDateRetourPrevue()) ?>
                                    </div>
                                    <div class="table-cell">
                                        <?= htmlspecialchars($document->getCodeTitre()) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>                            
                        <?php else: ?>
                            <div class="text-center text-warning py-4">
                                <h5>Aucun document associé à  
                                    <?= Auth::estAdministrateur() ? 'cet usager' : 'ce membre' ?>
                                </h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ .'/../layouts/message.php'; ?>

<?php if ($mode === null): ?>
    <div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">
        <h4 class="mb-4">Liste des <?= Auth::estAdministrateur() ? 'usagers' : 'membres' ?></h4>

        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <?php if (Auth::estAdministrateur()): ?>            
                <a class="btn couleur-marine" 
                    href="?page=usager&mode=ajouter<?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">            
                    Ajouter
                </a>
                
                <a class="btn couleur-jaune" 
                    href="?page=usager&filtre=<?=Usager::TYPE_ADMIN?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                    Admin
                </a>
                <a class="btn couleur-bleu" 
                    href="?page=usager&filtre=<?=Usager::TYPE_EMPLOYE?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                    Employé
                </a>
                <a class="btn couleur-verte" 
                    href="?page=usager&filtre=<?=Usager::TYPE_MEMBRE?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                    Membre
                </a>
            <?php endif; ?>
            
            <form class="d-flex gap-2 ms-auto" method="GET" action="">
                <input type="hidden" name="page" value="usager">
                <input class="form-control" type="text" name="rechercheMot" 
                       value="<?= htmlspecialchars($rechercheMot) ?>" placeholder="Rechercher...">
                <button class="btn couleur-marine" type="submit">Rechercher</button>
            </form>
        </div>

        <div class="d-flex flex-column" style="min-height: 0;">            
            <div class="d-flex border-bottom border-warning pb-2 mb-2" style="gap: 10px;">
                <div class="table-header-cell">Actions</div>                
                <div class="table-header-cell">Nom</div>
                <div class="table-header-cell">Téléphone</div>
                <div class="table-header-cell d-none d-md-table-cell">Courriel</div>
                <div class="table-header-cell d-none d-lg-table-cell">Adresse</div>
                <?php if (Auth::estAdministrateur()): ?>
                    <div class="table-header-cell d-none d-xl-table-cell">Type</div>
                <?php endif; ?>
            </div>

            <div class="table-body-scroll flex-grow-1" style="min-height: 0;">
                <?php if ($usagers): ?>
                    <?php foreach ($usagers as $usager): ?>
                        <div class="d-flex border-bottom border-warning py-2" style="gap: 10px;">
                            <div class="table-cell">
                                <?php if ($usager->estMembre()): ?> 
                                    <form method="GET" action="">
                                        <input type="hidden" name="page" value="usager">
                                        <input type="hidden" name="usager_selectionne_id" value="<?= $usager->getId() ?>">
                                        <button class="btn couleur-marine" type="submit">Ouvrir</button>
                                    </form>
                                <?php endif; ?>
                            </div>                            
                            <div class="table-cell"><?= htmlspecialchars($usager->getCodeNom()) ?></div>
                            <div class="table-cell"><?= htmlspecialchars($usager->getTelephone()) ?></div>
                            <div class="table-cell d-none d-md-table-cell"><?= htmlspecialchars($usager->getCourriel()) ?></div>
                            <div class="table-cell d-none d-lg-table-cell"><?= htmlspecialchars($usager->getAdresse()) ?></div>
                            <?php if (Auth::estAdministrateur()): ?>
                                <div class="table-cell d-none d-xl-table-cell"><?= htmlspecialchars($usager->getType()) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-warning py-4">
                        <h5>Aucun <?= Auth::estAdministrateur() ? 'usager' : 'membre' ?> trouvé</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
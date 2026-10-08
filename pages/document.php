<?php
    require_once __DIR__ .'/../controllers/Document.php';
    require_once __DIR__ .'/../models/Usager.php';
    extract(DocumentController::traiteRequete());    
?>

<?php if ($mode === 'voir'): ?>
    <div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">
        <div class="card bg-transparent border-warning">
            <div class="card-header d-flex justify-content-between align-items-center border-warning">
                <h3 class="mb-0">
                    <?= htmlspecialchars($documentSelectionne->getCodeTitre()) ?>
                </h3>
                <form method="GET" action="">
                    <input type="hidden" name="page" value="document">
                    <?php if (!empty($rechercheMot)): ?>
                        <input type="hidden" name="rechercheMot" value="<?= htmlspecialchars($rechercheMot) ?>">
                    <?php endif; ?>
                    <button class="btn couleur-marine" type="submit">✕</button>
                </form>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-transparent border-warning mb-1">
                            <div class="card-header border-warning">
                                <h4 class="mb-0">État actuel
                                    <span class="badge <?= $documentSelectionne->getStatutCouleur() ?> ms-2">
                                        <?= htmlspecialchars($documentSelectionne->getStatut()) ?>
                                    </span>
                                </h4>
                            </div>
                            <div class="card-body">
                                <p class="mb-2 table-cell">Prêté à: <?= htmlspecialchars($pret_usager_nom) ?></p>
                                <p class="mb-2 table-cell">Date de retour prévue: 
                                    <span class="date-local table-cell"><?= htmlspecialchars($documentSelectionne->getDateRetourPrevue()) ?></span>
                                </p>
                                <p class="mb-0 table-cell">Réservé à: <?= htmlspecialchars($reserver_usager_nom) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-transparent border-warning">
                            <div class="card-header border-warning">
                                <h4 class="mb-0">Actions disponibles</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-2">
                                    <?php if (isset($documentActions['preter']) && $documentActions['preter']): ?>
                                        <form method="POST" action="?page=document">
                                            <input type="hidden" name="action" value="preter">
                                            <input type="hidden" name="document_selectionne_id" value="<?= $documentSelectionne->getId() ?>">
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <select class="form-select w-auto" name="membre_id" required>
                                                    <option value="">Sélectionner un membre</option>
                                                    <?php foreach ($membresPret as $membre): ?>
                                                        <option value="<?= $membre->getId() ?>">
                                                            <?= htmlspecialchars($membre->getCodeNom()) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input class="form-control w-auto" type="date" 
                                                    name="date_retour_locale" id="date_retour_locale"
                                                    required value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                                                <input type="hidden" name="date_retour" id="date_retour_utc">                                               
                                                <button class="btn couleur-marine" type="submit">
                                                    Prêter
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (isset($documentActions['annuler_pret']) && $documentActions['annuler_pret']): ?>
                                        <form method="POST" action="?page=document">
                                            <input type="hidden" name="action" value="annuler_pret">
                                            <input type="hidden" name="document_selectionne_id" value="<?= $documentSelectionne->getId() ?>">
                                            <button class="btn couleur-marine" type="submit">
                                                Annuler le prêt
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (isset($documentActions['retour']) && $documentActions['retour']): ?>
                                        <form method="POST" action="?page=document">
                                            <input type="hidden" name="action" value="retour">
                                            <input type="hidden" name="document_selectionne_id" value="<?= $documentSelectionne->getId() ?>">
                                            <button class="btn couleur-marine" type="submit">
                                                Retour
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (isset($documentActions['reserver_membre']) && $documentActions['reserver_membre']): ?>
                                        <form method="POST" action="?page=document">
                                            <input type="hidden" name="action" value="reserver_membre">
                                            <input type="hidden" name="document_selectionne_id" value="<?= $documentSelectionne->getId() ?>">
                                            <button class="btn couleur-marine" type="submit">
                                                Réserver
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (isset($documentActions['reserver']) && $documentActions['reserver']): ?>
                                        <form method="POST" action="?page=document">
                                            <input type="hidden" name="action" value="reserver">
                                            <input type="hidden" name="document_selectionne_id" value="<?= $documentSelectionne->getId() ?>">
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <select class="form-select w-auto" name="membre_id" required>
                                                    <option value="">Sélectionner un membre</option>
                                                    <?php foreach ($membresReservation as $membre): ?>
                                                        <option value="<?= $membre->getId() ?>">
                                                            <?= htmlspecialchars($membre->getCodeNom()) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button class="btn couleur-marine" type="submit">
                                                    Réserver
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (isset($documentActions['annuler_reservation']) && $documentActions['annuler_reservation']): ?>
                                        <form method="POST" action="?page=document">
                                            <input type="hidden" name="action" value="annuler_reservation">
                                            <input type="hidden" name="document_selectionne_id" value="<?= $documentSelectionne->getId() ?>">
                                            <button class="btn couleur-marine" type="submit">
                                                Annuler la réservation
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ .'/../layouts/message.php'; ?>

<?php if ($mode === null): ?>
    <div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">       
        <h4 class="mb-4">Liste des documents</h4>

        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
            <?php if (Auth::estMembre()): ?>
                <a class="btn couleur-mauve" href="?page=document&filtre=<?= Document::USAGER ?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                    Mes documents
                </a>
            <?php endif; ?>
            <a class="btn couleur-rouge" href="?page=document&filtre=<?= Document::RETARD ?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                Retard
            </a>
            <a class="btn couleur-jaune" href="?page=document&filtre=<?= Document::EMPRUNTE ?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                Prêté
            </a>
            <a class="btn couleur-bleu" href="?page=document&filtre=<?= Document::RESERVE ?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                Réservé
            </a>
            <a class="btn couleur-verte" href="?page=document&filtre=<?= Document::DISPONIBLE ?><?= !empty($rechercheMot) ? '&rechercheMot='.urlencode($rechercheMot) : '' ?>">
                Disponible
            </a>
            
            <form class="d-flex gap-2 ms-auto" method="GET" action="">
                <input type="hidden" name="page" value="document">
                <input class="form-control" type="text" name="rechercheMot" value="<?= htmlspecialchars($rechercheMot) ?>" placeholder="Rechercher...">
                <button class="btn couleur-marine" type="submit">Rechercher</button>
            </form>
        </div>

        <div class="d-flex flex-column" style="min-height: 0;">
            <div class="d-flex border-bottom border-warning pb-2 mb-2" style="gap: 10px;">
                <div class="table-header-cell">Ouvrir</div>
                <div class="table-header-cell">Statut</div>
                <div class="table-header-cell">Titre</div>
                <div class="table-header-cell d-none d-md-table-cell">Description</div>
                <div class="table-header-cell d-none d-md-table-cell">Categorie</div>
                <div class="table-header-cell d-none d-md-table-cell">Type</div>              
                <div class="table-header-cell d-none d-lg-table-cell">Genre</div>
                <div class="table-header-cell d-none d-lg-table-cell">Auteur</div>
                <div class="table-header-cell d-none d-lg-table-cell">Année</div>
                <div class="table-header-cell d-none d-xl-table-cell">ISBN</div>
            </div>

            <div class="table-body-scroll flex-grow-1" style="min-height: 0;">
                <?php if ($documents): ?>
                    <?php foreach ($documents as $document): ?>
                        <div class="d-flex border-bottom border-warning py-2" style="gap: 10px;">
                            <div class="table-cell">
                                <form method="GET" action="">
                                    <input type="hidden" name="page" value="document">
                                    <input type="hidden" name="document_selectionne_id" value="<?= $document->getId() ?>">
                                    <button class="btn couleur-marine" type="submit">Ouvrir</button>
                                </form>
                            </div>
                            <div class="table-cell">
                                <span class="badge <?= $document->getStatutCouleur() ?>">
                                    <?= htmlspecialchars($document->getStatut()) ?>
                                </span>
                            </div>
                            <div class="table-cell"><?= htmlspecialchars($document->getCodeTitre()) ?></div>
                            <div class="table-cell d-none d-md-table-cell"><?= htmlspecialchars($document->getDescription()) ?></div>
                            <div class="table-cell d-none d-md-table-cell"><?= htmlspecialchars($document->getCategorie()) ?></div>
                            <div class="table-cell d-none d-md-table-cell"><?= htmlspecialchars($document->getType()) ?></div>
                            <div class="table-cell d-none d-lg-table-cell"><?= htmlspecialchars($document->getGenre()) ?></div>
                            <div class="table-cell d-none d-lg-table-cell"><?= htmlspecialchars($document->getAuteur()) ?></div>
                            <div class="table-cell d-none d-lg-table-cell"><?= htmlspecialchars($document->getAnneePublication()) ?></div>
                            <div class="table-cell d-none d-lg-table-cell"><?= htmlspecialchars($document->getIdentifiantInternational()) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-warning py-4"><h5>Aucun document trouvé</h5></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
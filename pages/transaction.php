<?php
    require_once __DIR__ .'/../models/Transaction.php';
    require_once __DIR__ . '/../controllers/Transaction.php';    
    extract(TransactionController::traiteRequete());
?>

<?php require_once __DIR__ . '/../layouts/message.php'; ?>

<div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">
    <h4 class="mb-4">Liste des transactions</h4>

    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <a class="btn couleur-mauve" 
            href="?page=transaction&filtre=<?= Transaction::TRANSACTION_RESERVATION ?><?= !empty($rechercheMot) ? '&rechercheMot=' . urlencode($rechercheMot) : '' ?>">
            Réservation
        </a>
        <a class="btn couleur-rouge" 
            href="?page=transaction&filtre=<?= Transaction::TRANSACTION_RESERVATION_ANNULE ?><?= !empty($rechercheMot) ? '&rechercheMot=' . urlencode($rechercheMot) : '' ?>">
            Réservation annulée
        </a>
        <a class="btn couleur-jaune" 
            href="?page=transaction&filtre=<?= Transaction::TRANSACTION_PRET ?><?= !empty($rechercheMot) ? '&rechercheMot=' . urlencode($rechercheMot) : '' ?>">
            Prêt
        </a>
        <a class="btn couleur-bleu" 
            href="?page=transaction&filtre=<?= Transaction::TRANSACTION_PRET_ANNULE ?><?= !empty($rechercheMot) ? '&rechercheMot=' . urlencode($rechercheMot) : '' ?>">
            Prêt annulé
        </a>
        <a class="btn couleur-verte" 
            href="?page=transaction&filtre=<?= Transaction::TRANSACTION_RETOUR ?><?= !empty($rechercheMot) ? '&rechercheMot=' . urlencode($rechercheMot) : '' ?>">
            Retour
        </a>
        
        <form class="d-flex gap-2 ms-auto" method="GET" action="">
            <input type="hidden" name="page" value="transaction">
            <input class="form-control" type="text" name="rechercheMot" value="<?= htmlspecialchars($rechercheMot) ?>" placeholder="Rechercher...">
            <button class="btn couleur-marine" type="submit">Rechercher</button>
        </form>
    </div>

    <div class="d-flex flex-column" style="min-height: 0;">
        <div class="d-flex border-bottom border-warning py-2" style="gap: 10px;">
            <div class="table-header-cell">Usager</div>
            <div class="table-header-cell">Effectué le</div>
            <div class="table-header-cell">Action</div>
            <div class="table-header-cell">Document</div>
            <div class="table-header-cell d-none d-md-table-cell">Prêté à</div>
            <div class="table-header-cell d-none d-md-table-cell">Retour prévu</div>
            <div class="table-header-cell d-none d-md-table-cell">Réservé à</div>
        </div>

        <div class="table-body-scroll flex-grow-1" style="min-height: 0;">
            <?php if ($transactions): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <div class="d-flex border-bottom border-warning py-2" style="gap: 10px;">
                        <div class="table-cell"><?= htmlspecialchars($transaction->getUsager()) ?></div>
                        <div class="table-cell datetime-local"><?= htmlspecialchars($transaction->getDateTransaction()) ?></div>
                        <div class="table-cell"><?= htmlspecialchars($transaction->getTypeAction()) ?></div>
                        <div class="table-cell"><?= htmlspecialchars($transaction->getDocument()) ?></div>
                        <div class="table-cell d-none d-md-table-cell"><?= htmlspecialchars($transaction->getPretUsager()) ?></div>
                        <div class="table-cell d-none d-md-table-cell date-local"><?= htmlspecialchars($transaction->getDateRetourPrevue()) ?></div>
                        <div class="table-cell d-none d-md-table-cell"><?= htmlspecialchars($transaction->getReserverUsager()) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-warning py-4">
                    <h5>Aucune transaction trouvée</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
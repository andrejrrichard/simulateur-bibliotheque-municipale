<?php 
    require_once __DIR__ .'/../controllers/login.php';
    extract(LoginController::traiteRequete());
?>

<div class="container-fluid mt-3 d-flex flex-column" style="min-height: 0;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">   
            <div class="card bg-transparent border-warning">            
                <div class="card-header border-warning text-center">
                    <h3 class="mb-0">Formulaire de connexion</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-1">
                            <span class="text-warning">* Ces champs sont obligatoires<span>
                        </div> 
                        <div class="mb-1">
                            <label class="form-label">Code: *</label>
                            <input class="form-control" placeholder="Code" type="text" name="code" value="<?= htmlspecialchars($code) ?>">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Mot de passe: *</label>
                            <input class="form-control" type="password" name="motDePasse">
                        </div>
                        <button class="btn mt-1" type="submit">Connexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>   
<?php require_once __DIR__ .'/../layouts/message.php'; ?> 
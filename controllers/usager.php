<?php
    require_once __DIR__ .'/../models/Usager.php';
    require_once __DIR__ .'/../models/Document.php';

    class UsagerController {

        private static function traiterCreation(&$data) {           
            try {
                $code = trim($_POST['code'] ?? '');
                $motDePasse = trim($_POST['motDePasse'] ?? '');
                $usagerType = trim($_POST['usagerType'] ?? ''); 
                $courriel = trim($_POST['courriel'] ?? '');
                $prenom = trim($_POST['prenom'] ?? '');
                $nom = trim($_POST['nom'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');
                $adresse = trim($_POST['adresse'] ?? '');            
                if (empty($code) || empty($motDePasse) || empty($usagerType)) {
                    $_SESSION['flash_message'] = 'Champs obligatoires manquants';
                    return;
                }

                $usager = Usager::create([
                    'code' => $code,
                    'password_hash' => password_hash($motDePasse, PASSWORD_DEFAULT), 
                    'usager_type' => $usagerType,
                    'courriel' => $courriel,
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'telephone' => $telephone,
                    'adresse' => $adresse
                ]);                
            
                if ($usager->getId() > 0) {
                    $_SESSION['flash_message'] = 'Usager créé';
                    header('Location: index.php?page=usager');
                    exit();
                }                
            } catch (Exception $e) {
                $_SESSION['flash_message'] = $e->getMessage();
            }
        }

        private static function chargeData(&$data) {
            try {
                $usagerSelectionneId = $_GET['usager_selectionne_id'] ?? 0;
                if ($usagerSelectionneId > 0) {
                    $data['usagerSelectionne'] = Usager::getById($usagerSelectionneId); 
                    if($data['usagerSelectionne'] !== null) {                                           
                        $data['documentsUsager'] = Document::getDocumentsByUsager($usagerSelectionneId);
                        $data['mode'] = 'voir';  
                    }else{
                        header("Location: ?page=usager");   
                        exit;
                    }                
                } elseif (isset($_GET['mode']) && $_GET['mode'] === 'ajouter') {
                    $data['mode'] = 'ajouter';
                } else {
                    $data['mode'] = null; 
                }

                $filtre = $data['filtreActif'] ?? '';
                if (Auth::estEmploye()) {
                    $usagers = Usager::getByType(Usager::TYPE_MEMBRE);
                }else{
                    $usagers = Usager::getByType($filtre);
                }
                $rechercheMot = $data['rechercheMot'] ?? '';
                if (!empty($rechercheMot)) {
                    $usagers = array_filter($usagers, function($usager) use ($rechercheMot) {
                        $champsRecherche = [
                            $usager->getType(),
                            $usager->getCodeNom(),
                            $usager->getTelephone(),
                            $usager->getCourriel(),
                            $usager->getAdresse()
                        ];                        
                        foreach ($champsRecherche as $champ) {
                            if (stripos($champ, $rechercheMot) !== false) {
                                return true;
                            }
                        }
                        return false;
                    });
                }
                $usagers = array_values($usagers);                
                foreach ($usagers as $usager) {
                    $data['usagers'][] = $usager;
                }
            } catch (Exception $e) {
                $_SESSION['flash_message'] = $e->getMessage();
            }
        }

        public static function traiteRequete() {
            $data = [
                'rechercheMot' => $_GET['rechercheMot'] ?? '',
                'filtreActif' => $_GET['filtre'] ?? '',
                'usagers' => [],
                'usagerSelectionne' => null,  
                'documentsUsager' => [],
                'mode' => null                 
            ];            
            if (Auth::estAdministrateur() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                self::traiterCreation($data);
            }
            self::chargeData($data);            
            return $data;
        }
    }
?>
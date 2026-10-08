<?php
    require_once __DIR__.'/../models/Document.php';
    require_once __DIR__.'/../models/Usager.php';
    require_once __DIR__.'/../includes/auth.php';
    
    class DocumentController {

        private static function traiteActions(&$data) {       
            try {    
                $documentSelectionneId = $_POST['document_selectionne_id'] ?? 0;         
                if ($documentSelectionneId > 0) {
                    $documentSelectionne = Document::getById($documentSelectionneId);
                    if($documentSelectionne !== null){
                        $membre_id = $_POST['membre_id'] ?? 0;
                        switch ($_POST['action'] ?? 0) {
                            case 'reserver_membre':
                                $documentSelectionne->reserver($_SESSION['usager']->getId(), $_SESSION['usager']->getId());
                                $_SESSION['flash_message'] = 'Le document est réservé';
                                break;
                            case 'reserver':
                                $documentSelectionne->reserver($_SESSION['usager']->getId(), $membre_id);
                                $_SESSION['flash_message'] = 'Le document est réservé';
                                break;
                            case 'annuler_reservation':
                                $documentSelectionne->annulerReservation($_SESSION['usager']->getId());
                                $_SESSION['flash_message'] = 'La réservation est annulée';
                                break; 
                            case 'preter':
                                $documentSelectionne->emprunter(
                                    $_SESSION['usager']->getId(),
                                    $membre_id,
                                    $_POST['date_retour'] ?? ''
                                );
                                $_SESSION['flash_message'] = 'Le document est prêté';
                                break;                    
                            case 'annuler_pret':
                                $documentSelectionne->annulerPret($_SESSION['usager']->getId());
                                $_SESSION['flash_message'] = 'La prêt est annulée';
                                break;                    
                            case 'retour':
                                $documentSelectionne->retourPret($_SESSION['usager']->getId());
                                if($documentSelectionne->estReserve()){
                                    $_SESSION['flash_message'] = 'Attention, le document est réservé';
                                }else{
                                    $_SESSION['flash_message'] = 'Le document est retourné';
                                }                            
                                break;
                            default:
                                $_SESSION['flash_message'] = "Cette action n'existe pas";
                            break;
                        }
                    }else{
                        header("Location: ?page=document");   
                        exit; 
                    }
                }   
            } catch (Exception $e) {
                $_SESSION['flash_message'] = $e->getMessage();
            }        
            header("Location: ?page=document&document_selectionne_id=".$documentSelectionneId);
            exit;            
        }

        private static function chargeData(&$data) {
            try {    
                $documentSelectionneId = $_GET['document_selectionne_id'] ?? 0;                 
                if ($documentSelectionneId > 0) {                    
                    $documentSelectionne = Document::getById($documentSelectionneId);
                    if($documentSelectionne !== null) {
                        $data['documentSelectionne'] = $documentSelectionne;  
                                
                        $pret_usager = $documentSelectionne->getPretUsagerId() ? Usager::getById($documentSelectionne->getPretUsagerId()): null;
                        if($pret_usager){                
                            if (Auth::estAdminOuEmploye() || $pret_usager->getId() === $_SESSION['usager']->getId()){                    
                                $data['pret_usager_nom'] = $pret_usager->getCodeNom();
                            }else{
                                $data['pret_usager_nom'] = 'Un autre membre';
                            }
                        }

                        $reserver_usager = $documentSelectionne->getReserverUsagerId() ? Usager::getById($documentSelectionne->getReserverUsagerId()): null;
                        if($reserver_usager){                
                            if (Auth::estAdminOuEmploye() || $reserver_usager->getId() === $_SESSION['usager']->getId()){                    
                                $data['reserver_usager_nom'] = $reserver_usager->getCodeNom();
                            }else{
                                $data['reserver_usager_nom'] = 'Un autre membre';
                            }
                        }
                        
                        $data['documentActions'] = [
                            'reserver' => Auth::estAdminOuEmploye() && !$documentSelectionne->estReserve(),
                            'reserver_membre' => Auth::estMembre() 
                                && !$documentSelectionne->estReserve() 
                                && $documentSelectionne->getPretUsagerId() !== $_SESSION['usager']->getId(),

                            'annuler_reservation' => $documentSelectionne->estReserve() && (
                                Auth::estAdminOuEmploye() || 
                                    (Auth::estMembre() && $documentSelectionne->estReserve() 
                                    && $documentSelectionne->getReserverUsagerId() === $_SESSION['usager']->getId())
                                ),

                            'preter' => Auth::estAdminOuEmploye() && !$documentSelectionne->estEmprunte(),
                            'retour' => Auth::estAdminOuEmploye() && $documentSelectionne->estEmprunte(),
                            'annuler_pret' => Auth::estAdminOuEmploye() && $documentSelectionne->estEmprunte(),            
                        ];                
                        if (Auth::estAdminOuEmploye()){
                            if($documentSelectionne->estReserve()){
                                $data['membresPret'] = [$reserver_usager];
                            }else{
                                $data['membresPret'] = Usager::getByType(Usager::TYPE_MEMBRE);
                            } 

                            $data['membresReservation'] = Usager::getByType(Usager::TYPE_MEMBRE);   
                            if($documentSelectionne->estEmprunte()){
                                $data['membresReservation'] = array_filter(
                                    $data['membresReservation'],
                                    function($usager) use ($pret_usager) {
                                        return $usager->getId() !== $pret_usager->getId();
                                    }
                                );
                                $data['membresReservation'] = array_values($data['membresReservation']);
                            }                     
                        }
                        $data['mode'] = 'voir';
                    } else {
                        header("Location: ?page=document");   
                        exit; 
                    }
                } else {
                    $data['mode'] = null; 
                }
                
                $filtreActif = $data['filtreActif'] ?? null;
                if($filtreActif && $filtreActif == Document::USAGER){
                    $documents = Document::getDocumentsByUsager($_SESSION['usager']->getId());
                }else{
                    $documents = Document::getDocumentsByFilter($filtreActif); 
                }              
                             

                $rechercheMot = $data['rechercheMot'] ?? '';
                if (!empty($rechercheMot)) {
                    $documents = array_filter($documents, function($document) use ($rechercheMot) {
                        $champsRecherche = [
                            $document->getCodeTitre(),
                            $document->getDescription(),
                            $document->getCategorie(),
                            $document->getType(),
                            $document->getGenre(),
                            $document->getAuteur(),
                            $document->getAnneePublication(),
                            $document->getIdentifiantInternational()
                        ];                    
                        foreach ($champsRecherche as $champ) {
                            if (stripos($champ, $rechercheMot) !== false) {
                                return true;
                            }
                        }
                        return false;
                    });
                }
                $documents = array_values($documents);                
                foreach ($documents as $document) {
                    $data['documents'][] = $document;
                }
            } catch (Exception $e) {
                $_SESSION['flash_message'] = $e->getMessage();
            }
        }

        public static function traiteRequete() {
            $data = [
                'rechercheMot' => $_GET['rechercheMot'] ?? '',
                'filtreActif' => $_GET['filtre'] ?? '',
                'documents' => [],
                'documentSelectionne' => null,
                'membresPret' => [],
                'membresReservation' => [],
                'documentActions' => null,
                'reserver_usager_nom' => '',
                'pret_usager_nom' => '',
                'mode' => null
            ];            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                self::traiteActions($data);
            }                        
            self::chargeData($data);            
            return $data;
        }
    }
?>
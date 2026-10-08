<?php
    require_once __DIR__.'/../includes/bd.php';
    require_once __DIR__ .'/../models/Transaction.php';

    class Document {
        public const USAGER = '42';
        public const DISPONIBLE = 'disponible';
        public const EMPRUNTE = 'emprunte';
        public const RESERVE = 'reserve';
        public const RETARD = 'retard';

        private const FILTRE_SQL = [
            self::DISPONIBLE => "SELECT * FROM vue_documents WHERE pret_usager_id IS NULL AND reserver_usager_id IS NULL",
            self::EMPRUNTE => "SELECT * FROM vue_documents WHERE pret_usager_id IS NOT NULL",
            self::RESERVE => "SELECT * FROM vue_documents WHERE reserver_usager_id IS NOT NULL",
            self::RETARD => "SELECT * FROM vue_documents WHERE pret_usager_id IS NOT NULL AND date_retour_prevue < UTC_TIMESTAMP()"
        ];

        private int $id;
        private string $titre;
        private string $description;
        private string $categorie;
        private string $type;          
        private string $genre;
        private string $auteur;
        private string $annee_publication;
        private string $identifiant_international;
        private string $statut;
        private ?int $pret_usager_id;
        private ?string $date_retour_prevue;
        private ?int $reserver_usager_id;        

        private function __construct(
            string $statut = '',
            string $titre = '',
            string $description = '',
            string $categorie = '',
            string $type = '', 
            string $genre = '',      
            string $auteur = '',
            string $annee_publication = '',
            string $identifiant_international = '',            
            ?int $pret_usager_id = null,
            ?string $date_retour_prevue = null,
            ?int $reserver_usager_id = null            
        ) {
            $this->id = 0;
            $this->titre = $titre;
            $this->description = $description;
            $this->categorie = $categorie;
            $this->type = $type;
            $this->genre = $genre;
            $this->auteur = $auteur;
            $this->annee_publication = $annee_publication;
            $this->identifiant_international = $identifiant_international;
            $this->statut = $statut;
            $this->pret_usager_id = $pret_usager_id;
            $this->date_retour_prevue = $date_retour_prevue;
            $this->reserver_usager_id = $reserver_usager_id;           
        }

        public function getId(): int { return $this->id; }
        public function getCodeTitre(): string { return $this->titre; }
        public function getDescription(): string { return $this->description; }
        public function getCategorie(): string { return $this->categorie; }        
        public function getType(): string { return $this->type; }
        public function getGenre(): string { return $this->genre; }
        public function getAuteur(): string { return $this->auteur; }
        public function getAnneePublication(): string { return $this->annee_publication; }
        public function getIdentifiantInternational(): string { return $this->identifiant_international; }
        public function getStatut(): string { return $this->statut; }
        public function getPretUsagerId(): ?int { return $this->pret_usager_id; }
        public function getReserverUsagerId(): ?int { return $this->reserver_usager_id; }
        public function getDateRetourPrevue(): string { return $this->date_retour_prevue ?? ''; }

        public function getStatutCouleur(): string {
            return match($this->getStatut()) {
                'Retard' => 'couleur-rouge',
                'Prêté' => 'couleur-jaune',
                'Réservé' => 'couleur-bleu',
                default => 'couleur-verte'
            };
        }

        private static function requeteGetDocuments(string $sql, array $params = []): array {
            try {
                $con = Database::getInstance()->prepare($sql);
                $con->execute($params); 

                $documents = [];
                foreach ($con->fetchAll() as $data) {
                    $document = new self(            
                        $data['statut'],
                        $data['titre'],
                        $data['description'],
                        $data['document_categorie'] ?? '',
                        $data['document_type'] ?? '',   
                        $data['document_genre'] ?? '',
                        $data['auteur'],
                        $data['annee_publication'],
                        $data['identifiant_international'],
                        $data['pret_usager_id'] ?? null,        
                        $data['date_retour_prevue'] ?? null,
                        $data['reserver_usager_id'] ?? null,                        
                    );
                    if (isset($data['id']) && $data['id'] > 0) {
                        $document->setId($data['id']);
                    }
                    $documents[] = $document;
                }
                return $documents;
            } catch (Exception $e) {
                throw $e;
            }
        }

        public static function getDocumentsByFilter(?string $filtre): array {
            try {           
                if ($filtre !== null && isset(self::FILTRE_SQL[$filtre])) {
                    $requete = self::FILTRE_SQL[$filtre];
                }else{
                    $requete = "SELECT * FROM vue_documents";
                }
                return self::requeteGetDocuments($requete);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public static function getById(int $id): ?Document {
            try {    
                if ($id <= 0) return null;  
                $requete = "SELECT * FROM vue_documents WHERE id = :id LIMIT 1";     
                $documents = self::requeteGetDocuments($requete, [':id' => $id]);         
                return $documents[0] ?? null; 
            } catch (Exception $e) {
                throw $e;
            }
        }

        public static function getDocumentsByUsager(int $id): array {
            try {    
                if ($id > 0){
                    $requete = "SELECT * FROM vue_documents WHERE reserver_usager_id = ? OR pret_usager_id = ?"; 
                    return self::requeteGetDocuments($requete, [$id, $id]); 
                }       
                return []; 
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function estEmprunte(): bool {
            return $this->pret_usager_id !== null;
        }

        public function estReserve(): bool {
            return $this->reserver_usager_id !== null;
        }

        private function setId(int $id): void { $this->id = $id; }        

        private function logTransaction(string $type_action, int $usager_id): void {
            $sql = "INSERT INTO transaction 
                    (date_transaction, document_id, type_action, usager_id, 
                    pret_usager_id, date_retour_prevue,
                    reserver_usager_id)
                    VALUES 
                    (UTC_TIMESTAMP(), :document_id, :type_action, :usager_id,
                    :pret_usager_id, :date_retour_prevue,
                    :reserver_usager_id)";        
            $con = Database::getInstance()->prepare($sql);
            $con->execute([
                ':document_id' => $this->id,
                ':type_action' => $type_action,
                ':usager_id' => $usager_id,
                ':pret_usager_id' => $this->pret_usager_id,
                ':date_retour_prevue' => $this->date_retour_prevue,
                ':reserver_usager_id' => $this->reserver_usager_id
            ]);
        }

        private function terminerPret(): void {
            if ($this->pret_usager_id === null) return;
            try {
                $sql = "UPDATE document SET 
                        pret_usager_id = NULL,
                        date_retour_prevue = NULL
                        WHERE id = :id";
                $con = Database::getInstance()->prepare($sql);
                $con->execute([':id' => $this->id]);
                $this->pret_usager_id = null;
                $this->date_retour_prevue = null;
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function annulerPret(int $usager_id): void {
            try {
                $this->terminerPret();
                $this->logTransaction(Transaction::TRANSACTION_PRET_ANNULE, $usager_id);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function retourPret(int $usager_id): void {
            try {
                $this->terminerPret();
                $this->logTransaction(Transaction::TRANSACTION_RETOUR, $usager_id);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function annulerReservation(int $usager_id): void {
            try {
                $sql = "UPDATE document SET 
                        reserver_usager_id = NULL
                        WHERE id = :id";
                $con = Database::getInstance()->prepare($sql);
                $con->execute([':id' => $this->id]);
                $this->reserver_usager_id = null;
                $this->logTransaction(Transaction::TRANSACTION_RESERVATION_ANNULE, $usager_id);
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function emprunter(int $usager_id, int $pret_usager_id, string $date_retour_prevue): void {
            
            $dateRetour = new DateTime($date_retour_prevue, new DateTimeZone('UTC'));
            $maintenant = new DateTime('now', new DateTimeZone('UTC'));
            $timestampRetour = $dateRetour->getTimestamp();
            $timestampMaintenant = $maintenant->getTimestamp();
            if (($timestampRetour - $timestampMaintenant) < 86400) { // 24 h plus tard minimum
                throw new Exception("La date de retour doit être au moins 24h après le prêt");
            }
            $reserver_usager_id = $this->reserver_usager_id;
            if($this->reserver_usager_id !== null){
                if ($this->reserver_usager_id !== $pret_usager_id) {
                    throw new Exception("Le prêt doit être fait au membre qui l'a réservé");
                }else{
                    // fin de la reservation pour l'usager qui emprunte
                    $reserver_usager_id = null; 
                }
            }
            try {
                $sql = "UPDATE document SET 
                        pret_usager_id = :pret_usager_id,
                        date_retour_prevue = :date_retour_prevue,
                        reserver_usager_id = :reserver_usager_id
                        WHERE id = :id";            
                $con = Database::getInstance()->prepare($sql);
                $con->execute([
                    ':id' => $this->id,
                    ':pret_usager_id' => $pret_usager_id,
                    ':date_retour_prevue' => $date_retour_prevue,
                    ':reserver_usager_id' => $reserver_usager_id
                ]);                
                $this->pret_usager_id = $pret_usager_id;
                $this->date_retour_prevue = $date_retour_prevue;
                $this->reserver_usager_id = $reserver_usager_id;   
                $this->logTransaction(Transaction::TRANSACTION_PRET, $usager_id);         
            } catch (Exception $e) {
                throw $e;
            }
        }

        public function reserver(int $usager_id, int $reserver_usager_id): void {   
            try {         
                $sql = "UPDATE document SET 
                        reserver_usager_id = :reserver_usager_id
                        WHERE id = :id";
                $con = Database::getInstance()->prepare($sql);
                $con->execute([
                    ':id' => $this->id,
                    ':reserver_usager_id' => $reserver_usager_id
                ]);            
                $this->reserver_usager_id = $reserver_usager_id;          
                $this->logTransaction(Transaction::TRANSACTION_RESERVATION, $usager_id);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
?>
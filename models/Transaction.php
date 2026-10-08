<?php
    require_once __DIR__.'/../includes/bd.php';

    class Transaction {

        public const TRANSACTION_PRET = 'pret';
        public const TRANSACTION_RESERVATION = 'reservation';
        public const TRANSACTION_RESERVATION_ANNULE = 'reservation_annule';
        public const TRANSACTION_PRET_ANNULE = 'pret_annule';
        public const TRANSACTION_RETOUR = 'retour';

        private string $date_transaction;
        private string $document;
        private string $type_action;
        private string $usager;
        private string $pret_usager;
        private string $date_retour_prevue;
        private string $reserver_usager;

        private function __construct(
            string $date_transaction,
            string $document,
            string $type_action,
            string $usager,
            string $pret_usager,
            string $date_retour_prevue,
            string $reserver_usager,
        ) {
            $this->date_transaction = $date_transaction;
            $this->document = $document;
            $this->type_action = $type_action;
            $this->usager = $usager;
            $this->pret_usager = $pret_usager;
            $this->date_retour_prevue = $date_retour_prevue;
            $this->reserver_usager = $reserver_usager;
        }
        
        public function getDateTransaction(): string {
            return $this->date_transaction;
        }
        
        public function getDocument(): string {
            return $this->document;
        }
        
        public function getTypeAction(): string {
            $conversion = [
                'reservation' => 'Réservation',
                'reservation_annule' => 'Réservation annulée',
                'pret' => 'Prêt',
                'pret_annule' => 'Prêt annulé',
                'retour' => 'Retour'
            ];
            return $conversion[$this->type_action];
        }
        
        public function getUsager(): string {
            return $this->usager;
        }
        
        public function getPretUsager(): string {
            return $this->pret_usager;
        }
        
        public function getDateRetourPrevue(): string {
            return $this->date_retour_prevue;
        }
        
        public function getReserverUsager(): string {
            return $this->reserver_usager;
        }

        public static function getByTypeAction(string $type_action_filter = ''): array {
            try{
                $sql = "SELECT * FROM vue_transactions";
                $params = [];        
                if ($type_action_filter !== '') {
                    $sql .= " WHERE type_action = :type_action";
                    $params[':type_action'] = $type_action_filter;
                }        
                $con = Database::getInstance()->prepare($sql);
                $con->execute($params);  
                
                $transactions = [];
                foreach ($con->fetchAll() as $data) {
                    $transactions[] = new self(
                        $data['date_transaction'] ?? '',
                        $data['document'] ?? '',
                        $data['type_action'] ?? '',
                        $data['usager'] ?? '',
                        $data['pret_usager'] ?? '',
                        $data['date_retour_prevue'] ?? '',
                        $data['reserver_usager'] ?? ''
                    );
                }
                return $transactions;
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
?>
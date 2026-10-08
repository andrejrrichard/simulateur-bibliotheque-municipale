<?php
    require_once __DIR__ .'/../models/Transaction.php';
    
    class TransactionController {

        private static function chargeData(&$data) {
            try {            
                $transactions = Transaction::getByTypeAction($_GET['filtre'] ?? '');
                $rechercheMot = $data['rechercheMot'];
                if (!empty($rechercheMot)) {
                    $transactions = array_filter($transactions, function($transaction) use ($rechercheMot) {
                        $champsRecherche = [
                            $transaction->getDateTransaction(),
                            $transaction->getDocument(),
                            $transaction->getTypeAction(),
                            $transaction->getUsager(),
                            $transaction->getPretUsager(),
                            $transaction->getDateRetourPrevue(),
                            $transaction->getReserverUsager()
                        ];
                        
                        foreach ($champsRecherche as $champ) {
                            if (stripos($champ, $rechercheMot) !== false) {
                                return true;
                            }
                        }
                        return false;
                    });
                }

                $transactions = array_values($transactions);                
                foreach ($transactions as $transaction) {
                    $data['transactions'][] = $transaction;
                }
            } catch (Exception $e) {
                $_SESSION['flash_message'] = $e->getMessage();
            }
        }

        public static function traiteRequete() {
            $data = [
                'rechercheMot' => $_GET['rechercheMot'] ?? '',
                'transactions' => []                          
            ];            
            self::chargeData($data);            
            return $data;
        }
    }
?>
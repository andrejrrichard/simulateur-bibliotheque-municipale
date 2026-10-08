<?php
    require_once __DIR__.'/../models/Usager.php';
    require_once __DIR__.'/../includes/config.php';

    class Auth {        

        public static function estConnecte() {

            if (!isset($_SESSION['usager'])) return false;

            if (isset($_SESSION['derniere_activite'])) {
                $inactif = time() - $_SESSION['derniere_activite'];

                if ($inactif > SESSION_TIMEOUT) {
                    self::logout();
                    header('Location: index.php?page=accueil');
                    exit();
                }
            }

            $_SESSION['derniere_activite'] = time();
            return true;
        }
        
        public static function estMembre() {
            return self::estConnecte() && $_SESSION['usager']->estMembre();
        }
        
        public static function estEmploye() {
            return self::estConnecte() && $_SESSION['usager']->estEmploye();
        }

        public static function estAdministrateur() {
            return self::estConnecte() && $_SESSION['usager']->estAdministrateur();
        }
        
        public static function estAdminOuEmploye() {
            return self::estEmploye() || self::estAdministrateur();
        }
        
        public static function login(string $code, string $motDePasse): bool {            
            try {
                $usager = Usager::getByCode($code);             
                if ($usager && $usager->verifierMotDePasse($motDePasse)) {
                    $_SESSION['usager'] = $usager;                                    
                    return true;
                }
                return false;               
            } catch (Exception $e) {
                $_SESSION['flash_message'] = $e->getMessage();
                return false;  
            }
        }

        public static function logout() {
            session_unset();
            session_destroy();
        }
    }
?>
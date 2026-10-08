<?php
    class LoginController {

        public static function traiteRequete() {
            $data = [
                'code' => ''
            ];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data['code'] = trim($_POST['code'] ?? '');
                $motDePasse  = $_POST['motDePasse'] ?? '';    
                if (empty($data['code']) || empty($motDePasse )) {
                    $_SESSION['flash_message'] = 'Code ou mot de passe incorrect';
                } else {        
                    if (Auth::login($data['code'], $motDePasse)) {
                        header('Location: index.php?page=accueil');
                        exit();
                    }else{
                        $_SESSION['flash_message'] = 'Code ou mot de passe incorrect';
                    }
                }
            }                      
            return $data;
        }
    }
?>
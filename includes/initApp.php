<?php
    require_once __DIR__.'/../includes/bd.php';
    require_once __DIR__.'/../models/Usager.php';

    class InitApp {

        public static function init() {
            try {
                $usagers = Usager::getByType();      
                if(empty($usagers)){  
                    $usagersData = [
                        [
                            'code' => 'Admin',
                            'mot_de_passe' => $_ENV['ADMIN_PASSWORD'], 
                            'type' => 'Admin',
                            'nom' => 'Administrateur de la bibliothèque',
                            'courriel' => 'admin@exemple.com',
                            'telephone' => '555-0001',
                            'adresse' => '1, rue principale, Québec'
                        ]
                    ];

                    foreach ($usagersData as $usagerData) {
                        Usager::create([
                            'code' => $usagerData['code'],
                            'password_hash' => password_hash($usagerData['mot_de_passe'], PASSWORD_DEFAULT),
                            'usager_type' => $usagerData['type'],
                            'nom' => $usagerData['nom'],
                            'courriel' => $usagerData['courriel'],
                            'telephone' => $usagerData['telephone'],
                            'adresse' => $usagerData['adresse']
                        ]);
                    }
                }                
            } catch (Exception $e) {
                error_log("Erreur initApp: " . $e->getMessage());
            }
        }
    }
?>
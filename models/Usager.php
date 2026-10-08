<?php
    require_once __DIR__.'/../includes/bd.php';

    class Usager {
        
        public const TYPE_ADMIN = 'Admin';
        public const TYPE_MEMBRE = 'Membre';
        public const TYPE_EMPLOYE = 'Employe';

        private int $id;
        private string $code;
        private string $password_hash;
        private string $usager_type;
        private string $nom;
        private string $telephone;
        private string $courriel;
        private string $adresse;

        public function __construct(
            string $code = '',
            string $password_hash = '',
            string $usager_type = '',
            string $nom = '',
            string $telephone = '',
            string $courriel = '',
            string $adresse = ''
        ) {
            $this->id = 0;
            $this->code = $code;
            $this->password_hash = $password_hash;
            $this->usager_type = $usager_type;
            $this->nom = $nom;
            $this->telephone = $telephone;
            $this->courriel = $courriel;
            $this->adresse = $adresse;
        }   
        
        public function estMembre(): bool { return $this->usager_type === self::TYPE_MEMBRE; }

        public function estEmploye(): bool { return $this->usager_type === self::TYPE_EMPLOYE; }

        public function estAdministrateur(): bool { return $this->usager_type === self::TYPE_ADMIN; }

        public function getId(): int { return $this->id; }
        
        public function getType(): string { return $this->usager_type; }
                
        public function getCodeNom(): string { return '('.$this->code.') '.$this->nom; }        
        
        public function getTelephone(): string { return $this->telephone; }
        
        public function getCourriel(): string { return $this->courriel; }
        
        public function getAdresse(): string { return $this->adresse; }
        
        public static function getAllTypes(): array {
            try {
                $sql = "SELECT code FROM usager_type";
                $con = Database::getInstance()->prepare($sql);
                $con->execute();            
                $usagerTypes = [];
                while ($row = $con->fetch(PDO::FETCH_ASSOC)) {
                    $usagerTypes[] = $row['code'];
                }
                return $usagerTypes;            
            } catch (Exception $e) {
                throw $e;
            }
        }

        public static function getByType(?string $type = ''): array {
            try {
                $sql = "SELECT * FROM vue_usagers";
                $params = [];                
                if ($type !== '') {
                    $sql .= " WHERE usager_type = :type";
                    $params[':type'] = $type;
                }                
                $con = Database::getInstance()->prepare($sql);
                $con->execute($params);

                $usagers = [];                
                foreach ($con->fetchAll() as $data) {
                    $usagers[] = self::create($data);
                }
                return $usagers;
            } catch (Exception $e) {
                throw $e;
            }
        }

        private function setId(int $id): void { $this->id = $id; }

        public static function create(array $data): Usager {
            try {
                $usager = new self(
                    $data['code'],
                    $data['password_hash'],
                    $data['usager_type'], 
                    $data['nom'],
                    $data['telephone'],
                    $data['courriel'],
                    $data['adresse']
                );
                if (isset($data['id']) && $data['id'] > 0) {
                    $usager->setId($data['id']);                                
                }else{
                    $usager->setId($usager->insert());
                }            
                return $usager;
            } catch (Exception $e) {
                throw $e;
            }
        }       

        private function insert(): int {
            try {
                $sql = "CALL creer_usager(
                    :code, :password, :type, :nom, :telephone,
                    :courriel, :adresse, @id_usager
                )";  
                $con = Database::getInstance()->prepare($sql);             
                $con->execute([
                    ':code' => $this->code,
                    ':password' => $this->password_hash,
                    ':type' => $this->usager_type,
                    ':nom' => $this->nom,
                    ':telephone' => $this->telephone,
                    ':courriel' => $this->courriel,
                    ':adresse' => $this->adresse
                ]);
                $result = Database::getInstance()->query("SELECT @id_usager as id")->fetch();            
                return (int)$result['id']; 
            } catch (\PDOException $e) {
                $errorInfo = $e->errorInfo;
                if (isset($errorInfo[2])) {
                    throw new Exception($errorInfo[2]);
                }
                throw new Exception($e->getMessage());               
            }
        }

        public function verifierMotDePasse(string $password): bool {
            return password_verify($password, $this->password_hash);
        }

        public static function getByCode(string $code): ?Usager {
            try {
                $sql = "SELECT * FROM vue_usagers WHERE code = :code LIMIT 1";
                $con = Database::getInstance()->prepare($sql);
                $con->execute([':code' => $code]);                
                $data = $con->fetch(PDO::FETCH_ASSOC);
                return $data ? self::create($data) : null;                
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        public static function getById(string $id): ?Usager {
            try {
                $sql = "SELECT * FROM vue_usagers WHERE id = :id LIMIT 1";
                $con = Database::getInstance()->prepare($sql);
                $con->execute([':id' => $id]);                
                $data = $con->fetch(PDO::FETCH_ASSOC);
                return $data ? self::create($data) : null;                
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
?>
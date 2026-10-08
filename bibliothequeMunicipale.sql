DROP DATABASE IF EXISTS bibliothequeMunicipale;
CREATE DATABASE bibliothequeMunicipale;
USE bibliothequeMunicipale;

CREATE TABLE usager_type (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(20) UNIQUE NOT NULL
);

INSERT INTO usager_type (code) VALUES
('Membre'),
('Employe'), 
('Admin');

CREATE TABLE usager (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(6) COLLATE utf8mb4_bin UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  usager_type_id INT NOT NULL,
  nom VARCHAR(100) NULL, 
  telephone VARCHAR(15) NULL,    
  courriel VARCHAR(100) NULL,
  adresse VARCHAR(200) NULL,
  FOREIGN KEY (usager_type_id) REFERENCES usager_type(id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE VIEW vue_usagers AS
SELECT 
  usager.id,
  usager.code,
  usager.password_hash,
  usager_type.code AS usager_type,
  usager.nom,
  usager.telephone,
  usager.courriel,
  usager.adresse
FROM usager usager
INNER JOIN usager_type ON usager.usager_type_id = usager_type.id
ORDER BY 
  CASE usager_type.code
    WHEN 'Membre' THEN 1
    WHEN 'Employe' THEN 2
    WHEN 'Admin' THEN 3
    ELSE 4
  END ASC;
  
DELIMITER $$
CREATE PROCEDURE creer_usager(
  IN p_code VARCHAR(255),
  IN p_password_hash VARCHAR(255),
  IN p_usager_type VARCHAR(255),  
  IN p_nom VARCHAR(255),
  IN p_telephone VARCHAR(255),
  IN p_courriel VARCHAR(255),
  IN p_adresse VARCHAR(255),
  OUT p_id INT  
)
BEGIN
  DECLARE v_type_id INT;

  IF p_code IS NULL OR p_code = '' THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Le code est obligatoire';
  END IF;

  IF EXISTS (SELECT 1 FROM usager WHERE code = p_code) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Le code existe déjà';
  END IF;

  IF LENGTH(p_code) > 6 THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Le code ne peut pas dépasser 6 caractères';
  END IF;

  IF p_password_hash IS NULL OR p_password_hash = '' THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Le mot de passe est obligatoire';
  END IF;

  IF LENGTH(p_nom) > 100 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Le nom ne peut pas dépasser 100 caractères';
  END IF; 

  IF LENGTH(p_courriel) > 100 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Le courriel ne peut pas dépasser 100 caractères';
  END IF;

  IF LENGTH(p_telephone) > 15 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Le téléphone ne peut pas dépasser 15 caractères';
  END IF;  

  IF LENGTH(p_adresse) > 200 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'L''adresse ne peut pas dépasser 200 caractères';
  END IF;  
  
  SELECT id INTO v_type_id FROM usager_type WHERE code = p_usager_type;
  
  IF v_type_id IS NULL THEN
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Type d''usager non trouvé';
  END IF;
  
  INSERT INTO usager (
    code, 
    password_hash, 
    usager_type_id,  
    nom, 
    telephone, 
    courriel,  
    adresse
  ) VALUES (
    p_code,
    p_password_hash,
    v_type_id,
    p_nom,
    p_telephone,
    p_courriel,
    p_adresse
  );

  SET p_id = LAST_INSERT_ID();
END$$
DELIMITER ;


CREATE TABLE document_categorie (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO document_categorie (code) VALUES
('roman'),
('bande dessinée'), 
('jeux vidéo'),
('DVD'), 
('Blu-ray'), 
('CD');

CREATE TABLE document_type (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO document_type (code) VALUES
('enfant'),
('ado'), 
('adulte');

CREATE TABLE document_genre (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO document_genre (code) VALUES
('comédie'),
('drame'), 
('horreur'),
('sci-fi'), 
('documentaire');

CREATE TABLE document (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(6) COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL,
  titre VARCHAR(200) NULL,
  description VARCHAR(200) NULL,
  auteur VARCHAR(100) NULL,
  annee_publication VARCHAR(6) NULL,
  identifiant_international VARCHAR(200) COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL,
  document_categorie_id INT NOT NULL,
  document_type_id INT NOT NULL,
  document_genre_id INT NOT NULL,
  pret_usager_id INT NULL,
  date_retour_prevue DATETIME NULL,
  reserver_usager_id INT NULL, 
  FOREIGN KEY (document_categorie_id) REFERENCES document_categorie(id),
  FOREIGN KEY (document_type_id) REFERENCES document_type(id),
  FOREIGN KEY (document_genre_id) REFERENCES document_genre(id),
  FOREIGN KEY (pret_usager_id) REFERENCES usager(id),
  FOREIGN KEY (reserver_usager_id) REFERENCES usager(id)
);

CREATE VIEW vue_documents AS
SELECT 
  document.id,
  CONCAT('(', document.code, ') ', COALESCE(document.titre, '')) AS titre,
  document.description,
  document.auteur,
  document.annee_publication,
  document.identifiant_international,
  document_categorie.code AS document_categorie,
  document_type.code AS document_type,
  document_genre.code AS document_genre,
  document.pret_usager_id,
  document.date_retour_prevue,
  document.reserver_usager_id,
  CASE 
    WHEN document.pret_usager_id IS NOT NULL AND document.date_retour_prevue < UTC_TIMESTAMP() THEN 'Retard'
    WHEN document.pret_usager_id IS NOT NULL THEN 'Prêté'
    WHEN document.reserver_usager_id IS NOT NULL THEN 'Réservé'
    ELSE 'Disponible'
  END AS statut,
  CASE 
    WHEN document.pret_usager_id IS NOT NULL AND document.date_retour_prevue < UTC_TIMESTAMP() THEN 1
    WHEN document.pret_usager_id IS NOT NULL THEN 2
    WHEN document.reserver_usager_id IS NOT NULL THEN 3
    ELSE 4
  END AS ordre_affichage
FROM document
INNER JOIN document_categorie ON document.document_categorie_id = document_categorie.id
INNER JOIN document_type ON document.document_type_id = document_type.id
INNER JOIN document_genre ON document.document_genre_id = document_genre.id
ORDER BY ordre_affichage ASC, document.date_retour_prevue ASC;

CREATE TABLE transaction (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_transaction DATETIME DEFAULT (UTC_TIMESTAMP()),
  document_id INT NOT NULL,
  type_action ENUM('reservation', 'reservation_annule', 'pret', 'pret_annule', 'retour') NOT NULL,
  usager_id INT NOT NULL, 
  pret_usager_id INT NULL,
  date_retour_prevue DATETIME NULL,
  reserver_usager_id INT NULL,
  FOREIGN KEY (usager_id) REFERENCES usager(id),
  FOREIGN KEY (pret_usager_id) REFERENCES usager(id),
  FOREIGN KEY (reserver_usager_id) REFERENCES usager(id),
  FOREIGN KEY (document_id) REFERENCES document(id)
);

CREATE VIEW vue_transactions AS
SELECT 
  transaction.id,
  transaction.date_transaction,
  CONCAT('(', document.code, ') ', COALESCE(document.titre, '')) AS document,
  transaction.type_action,
  CONCAT(usager_type.code, ' (', usager.code, ') ', COALESCE(usager.nom, '')) AS usager,
  CONCAT('(', pret_usager.code, ') ', COALESCE(pret_usager.nom, '')) AS pret_usager,
  transaction.date_retour_prevue,
  CONCAT('(', reserver_usager.code, ') ', COALESCE(reserver_usager.nom, '')) AS reserver_usager
FROM transaction
INNER JOIN document ON transaction.document_id = document.id
INNER JOIN usager ON transaction.usager_id = usager.id
INNER JOIN usager_type ON usager.usager_type_id = usager_type.id
LEFT JOIN usager AS pret_usager ON transaction.pret_usager_id = pret_usager.id
LEFT JOIN usager AS reserver_usager ON transaction.reserver_usager_id = reserver_usager.id
ORDER BY transaction.id DESC;



-- Romans pour adultes
INSERT INTO document (code, titre, description, auteur, annee_publication, identifiant_international, document_categorie_id, document_type_id, document_genre_id) VALUES
('ROM001', 'Les Misérables', 'L''histoire de Jean Valjean dans la France du XIXe siècle', 'Victor Hugo', '1862', '9782253006334', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('ROM002', 'Le Comte de Monte-Cristo', 'Une histoire de vengeance et de rédemption', 'Alexandre Dumas', '1844', '9782070412396', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('ROM003', 'Germinal', 'La vie des mineurs au XIXe siècle', 'Émile Zola', '1885', '9782070417605', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('ROM004', 'Fondation', 'Le début de la saga de science-fiction', 'Isaac Asimov', '1951', '9782290015437', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM005', 'Dune', 'Sur la planète Arrakis, le désert cache la plus précieuse des épices', 'Frank Herbert', '1965', '9782266235243', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM006', 'Ubik', 'Un classique de la science-fiction psychédélique', 'Philip K. Dick', '1969', '9782290021436', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM007', 'L''Étranger', 'Le chef-d''œuvre de l''existentialisme', 'Albert Camus', '1942', '9782070360423', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('ROM008', 'La Peste', 'Une allégorie de la résistance face au mal', 'Albert Camus', '1947', '9782070365435', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('ROM009', '1984', 'Le roman dystopique par excellence', 'George Orwell', '1949', '9782070368221', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM010', 'Le Meilleur des mondes', 'Une vision cauchemardesque du futur', 'Aldous Huxley', '1932', '9782266129666', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),

-- Romans pour adolescents
('ROM011', 'Harry Potter à l''école des sorciers', 'Le début des aventures du jeune sorcier', 'J.K. Rowling', '1997', '9782070584624', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('ROM012', 'Hunger Games', 'Katniss Everdeen dans l''arène', 'Suzanne Collins', '2008', '9782266194252', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM013', 'Le Labyrinthe', 'Des adolescents piégés dans un labyrinthe mortel', 'James Dashner', '2009', '9782226256895', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM014', 'Divergente', 'Dans un monde divisé en factions', 'Veronica Roth', '2011', '9782226285438', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('ROM015', 'Nos étoiles contraires', 'L''histoire d''amour de deux adolescents malades', 'John Green', '2012', '9782226259834', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'drame')),

-- Romans pour enfants
('ROM016', 'Le Petit Prince', 'Un conte philosophique pour petits et grands', 'Antoine de Saint-Exupéry', '1943', '9782070408504', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('ROM017', 'Charlie et la chocolaterie', 'La visite magique d''une chocolaterie', 'Roald Dahl', '1964', '9782070612635', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('ROM018', 'Matilda', 'Une petite fille surdouée et ses pouvoirs', 'Roald Dahl', '1988', '9782070612598', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('ROM019', 'Le Bon Gros Géant', 'L''amitié entre une orpheline et un géant', 'Roald Dahl', '1982', '9782070612642', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('ROM020', 'Alice au pays des merveilles', 'Les aventures fantastiques d''Alice', 'Lewis Carroll', '1865', '9782070507852', (SELECT id FROM document_categorie WHERE code = 'roman'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),

-- Bandes dessinées
('BD001', 'Astérix le Gaulois', 'Les aventures du célèbre Gaulois', 'René Goscinny', '1961', '9782012101413', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('BD002', 'Tintin au Tibet', 'Tintin à la recherche de Tchang', 'Hergé', '1960', '9782203001173', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'drame')),
('BD003', 'Le Lotus bleu', 'Tintin en Chine', 'Hergé', '1936', '9782203001128', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'drame')),
('BD004', 'Les Schtroumpfs noirs', 'La première aventure des Schtroumpfs', 'Peyo', '1963', '9782800102823', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('BD005', 'Gaston - Gare aux gaffes', 'Les gaffes du célèbre Gaston Lagaffe', 'André Franquin', '1960', '9782800112488', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('BD006', 'Le Combat des chefs', 'Astérix et le combat de chefs', 'René Goscinny', '1966', '9782864971519', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('BD007', 'L''Affaire Tournesol', 'Tintin et les submarines', 'Hergé', '1956', '9782203001159', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'drame')),
('BD008', 'Blake et Mortimer - Le Secret de l''Espadon', 'Une aventure d''espionnage', 'Edgar P. Jacobs', '1950', '9782870970102', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('BD009', 'XIII - Le Jour du soleil noir', 'Un thriller d''espionnage', 'Jean Van Hamme', '1984', '9782800102519', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('BD010', 'Largo Winch - L''Héritier', 'Un homme d''affaires hors norme', 'Jean Van Hamme', '1990', '9782800121664', (SELECT id FROM document_categorie WHERE code = 'bande dessinée'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),

-- DVD/Blu-ray
('DVD01', 'Inception', 'Un voleur qui s''infiltre dans les rêves', 'Christopher Nolan', '2010', 'DVD-INCEP-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('DVD02', 'Le Seigneur des Anneaux', 'La quête pour détruire l''anneau unique', 'Peter Jackson', '2001', 'DVD-LOTR-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('DVD03', 'Intouchables', 'L''amitié improbable entre un tétraplégique et son aide-soignant', 'Olivier Nakache', '2011', 'DVD-INTOU-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('DVD04', 'Star Wars - Un nouvel espoir', 'Le début de la saga légendaire', 'George Lucas', '1977', 'DVD-SW4-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('DVD05', 'Retour vers le futur', 'Un adolescent voyage dans le temps', 'Robert Zemeckis', '1985', 'DVD-BTTF-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('DVD06', 'Toy Story', 'La vie secrète des jouets', 'John Lasseter', '1995', 'DVD-TOYS-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('DVD07', 'Le Roi Lion', 'L''histoire de Simba', 'Roger Allers', '1994', 'DVD-LION-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'drame')),
('DVD08', 'Interstellar', 'La quête d''une nouvelle planète habitable', 'Christopher Nolan', '2014', 'DVD-INTER-001', (SELECT id FROM document_categorie WHERE code = 'DVD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),

-- Jeux vidéo
('JV001', 'The Legend of Zelda', 'L''épopée de Link dans Hyrule', 'Nintendo', '2023', 'JV-ZELDA-001', (SELECT id FROM document_categorie WHERE code = 'jeux vidéo'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('JV002', 'Super Mario Odyssey', 'Mario voyage à travers le monde', 'Nintendo', '2017', 'JV-MARIO-001', (SELECT id FROM document_categorie WHERE code = 'jeux vidéo'), (SELECT id FROM document_type WHERE code = 'enfant'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('JV003', 'The Last of Us', 'Survie dans un monde post-apocalyptique', 'Naughty Dog', '2013', 'JV-LAST-001', (SELECT id FROM document_categorie WHERE code = 'jeux vidéo'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),
('JV004', 'God of War', 'Le voyage de Kratos dans la mythologie nordique', 'Santa Monica', '2018', 'JV-GOW-001', (SELECT id FROM document_categorie WHERE code = 'jeux vidéo'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'drame')),

-- CD
('CD001', 'Thriller', 'L''album légendaire de Michael Jackson', 'Michael Jackson', '1982', 'CD-THR-001', (SELECT id FROM document_categorie WHERE code = 'CD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('CD002', 'The Dark Side of the Moon', 'L''album concept des Pink Floyd', 'Pink Floyd', '1973', 'CD-DARK-001', (SELECT id FROM document_categorie WHERE code = 'CD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'sci-fi')),
('CD003', 'Abbey Road', 'Le dernier album enregistré par les Beatles', 'The Beatles', '1969', 'CD-ABBY-001', (SELECT id FROM document_categorie WHERE code = 'CD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'comédie')),
('CD004', 'Nevermind', 'L''album qui a popularisé le grunge', 'Nirvana', '1991', 'CD-NEVER-001', (SELECT id FROM document_categorie WHERE code = 'CD'), (SELECT id FROM document_type WHERE code = 'ado'), (SELECT id FROM document_genre WHERE code = 'drame')),
('CD005', 'Born in the U.S.A.', 'L''album emblématique de Bruce Springsteen', 'Bruce Springsteen', '1984', 'CD-BORN-001', (SELECT id FROM document_categorie WHERE code = 'CD'), (SELECT id FROM document_type WHERE code = 'adulte'), (SELECT id FROM document_genre WHERE code = 'comédie'));
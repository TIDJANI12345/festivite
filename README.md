| Fichier PHP         | Description                                     |
| ------------------- | ----------------------------------------------- |
| index.php           | Page d’accueil avec présentation des activités  |
| foot.php            | Gestion du tournoi : tirage, score, classement  |
| sortie.php          | Affichage et gestion de la sortie pédagogique   |
| soiree.php          | Présentation de la soirée de fin d’année        |
| admin.php           | Espace admin (connexion, saisie, gestions)      |
| includes/header.php | Header du site avec menu                        |
| includes/footer.php | Pied de page                                    |
| connexion.php       | Système de connexion (pour l’admin par exemple) |



Les tables 

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
role ENUM('admin', 'etudiant') DEFAULT 'etudiant',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipes (
id INT AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(100) NOT NULL
);

CREATE TABLE poules (
id INT AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(50) NOT NULL
);

CREATE TABLE equipe_poule (
id INT AUTO_INCREMENT PRIMARY KEY,
equipe_id INT NOT NULL,
poule_id INT NOT NULL,
FOREIGN KEY (equipe_id) REFERENCES equipes(id) ON DELETE CASCADE,
FOREIGN KEY (poule_id) REFERENCES poules(id) ON DELETE CASCADE
);

CREATE TABLE matchs (
id INT AUTO_INCREMENT PRIMARY KEY,
poule_id INT NOT NULL,
equipe1_id INT NOT NULL,
equipe2_id INT NOT NULL,
date_match DATETIME DEFAULT NULL,
score1 INT DEFAULT NULL,
score2 INT DEFAULT NULL,
statut ENUM('programmé', 'terminé') DEFAULT 'programmé',
FOREIGN KEY (poule_id) REFERENCES poules(id),
FOREIGN KEY (equipe1_id) REFERENCES equipes(id),
FOREIGN KEY (equipe2_id) REFERENCES equipes(id)
);

CREATE TABLE joueurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    poste VARCHAR(50) DEFAULT NULL,
    equipe_id INT NOT NULL,
    FOREIGN KEY (equipe_id) REFERENCES equipes(id)
);

CREATE TABLE sorties (
id INT AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(100) NOT NULL,
description TEXT,
date DATE,
lieu VARCHAR(100)
);

CREATE TABLE soirees (
id INT AUTO_INCREMENT PRIMARY KEY,
nom VARCHAR(100) NOT NULL,
description TEXT,
date DATE,
lieu VARCHAR(100),
programme TEXT
);

CREATE TABLE sortie_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sortie_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  commentaire TEXT DEFAULT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sortie_id) REFERENCES sorties(id) ON DELETE CASCADE
);

CREATE TABLE soiree_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sortie_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  commentaire TEXT DEFAULT NULL,
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
  FOREIGN KEY (soiree_id) REFERENCES soirees(id) ON DELETE CASCADE
);

CREATE TABLE match_finale (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipe1_id INT NOT NULL,
    equipe2_id INT NOT NULL,
    score1 INT DEFAULT NULL,
    score2 INT DEFAULT NULL,
    statut ENUM('à venir', 'terminé') DEFAULT 'à venir',
    date_match DATETIME DEFAULT NULL,

    FOREIGN KEY (equipe1_id) REFERENCES equipes(id),
    FOREIGN KEY (equipe2_id) REFERENCES equipes(id)
);


-- 1. Ajouter les poules
INSERT INTO poules (nom) VALUES ('Poule A'), ('Poule B');

-- 2. Ajouter les équipes
INSERT INTO equipes (nom) VALUES 
('CG2'), 
('EA3'), 
('Tronc Commun L1'), 
('CG3'), 
('SIL2');

-- 3. Lier les équipes aux poules
-- Supposons : ID poules = 1 (Poule A), 2 (Poule B)
INSERT INTO equipe_poule (equipe_id, poule_id) VALUES 
(1, 1),  -- CG2
(2, 1),  -- EA3
(3, 1),  -- Tronc Commun L1
(4, 2),  -- CG3
(5, 2);  -- SIL2

-- 4. Planifier les matchs
INSERT INTO matchs (poule_id, equipe1_id, equipe2_id, statut) VALUES 
(1, 1, 2, 'à jouer'), -- CG2 vs EA3
(1, 1, 3, 'à jouer'), -- CG2 vs Tronc Commun L1
(1, 2, 3, 'à jouer'), -- EA3 vs Tronc Commun L1
(2, 4, 5, 'à jouer'), -- CG3 vs SIL2
(2, 5, 4, 'à jouer'); -- SIL2 vs CG3 (retour)



INSERT INTO joueurs (nom, equipe_id) VALUES 
('AKOUAKOU Basile', 1),
('ADELEYE Rachard', 1),
('KAMMANNI Espérance', 1),
('LIMA Emmanuel', 1),
('NASSIROU Nourhoum', 1),
('MAÏDU Rine', 1),
('SODATINOU Kelly', 1),
('ZANVO Steavy', 1);

INSERT INTO joueurs (nom, equipe_id) VALUES 
('ABBALDHOUN Isaac', 2),
('OLONI Philibert', 2),
('MOUSSA Malfouz', 2),
('KOKOUN Kenneth', 2),
('AGBO Angelo', 2),
('SANNI Raphaël', 2),
('KANCHI Gautier', 2),
('MENSAH Moïse', 2);


INSERT INTO joueurs (nom, equipe_id) VALUES 
('HOUNKPEN Mélanise', 3),
('HOUNTONDJI Giovani', 3),
('FASSINOU Justin', 3),
('HOUNDAKINNOU Jean-Eudes', 3),
('HOUMENOU Jacques', 3),
('ZINSOU Jacob', 3),
('SOULEYMANE Fadel', 3),
('HOUNGBODI Claude', 3);


INSERT INTO joueurs (nom, equipe_id) VALUES 
('AMEZAN Franck', 4),
('DOGNON Fabrice', 4),
('GADO Salim', 4),
('MAMA Kouroukou', 4),
('ANAGO Shalom', 4),
('ULRICH Fernand', 4),
('ADIN James', 4),
('ZINSOU Gildas', 4);

INSERT INTO joueurs (nom, equipe_id) VALUES 
('AIDEDO Omer', 5),
('TOSSOU Collins', 5),
('MOUVI Blaise', 5),
('ZOUNMATON Cyrus', 5),
('TIDJANI Abdoul Aziz ', 5),
('YACOUBOU Oubeid' ,5),
('MAMAN Lawali Kabirou', 5),
('TIDJANI Mohamed', 5);

CREATE TABLE soiree_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL,
  commentaire TEXT DEFAULT NULL,
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reservation_suivi;
DROP TABLE IF EXISTS reservation_materiel;
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS reservation;
DROP TABLE IF EXISTS creneau;
DROP TABLE IF EXISTS equipement_inclus;
DROP TABLE IF EXISTS studio_photo;
DROP TABLE IF EXISTS studio;
DROP TABLE IF EXISTS materiel;
DROP TABLE IF EXISTS horaire;
DROP TABLE IF EXISTS password_reset;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS type_studio;
DROP TABLE IF EXISTS role;

CREATE TABLE role (
    id_role      INT AUTO_INCREMENT PRIMARY KEY,
    libelle      VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE type_studio (
    id_type      INT AUTO_INCREMENT PRIMARY KEY,
    libelle      VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE utilisateur (
    id_utilisateur     INT AUTO_INCREMENT PRIMARY KEY,
    id_role            INT NOT NULL,
    nom                VARCHAR(80)  NOT NULL,
    prenom             VARCHAR(80)  NOT NULL,
    email              VARCHAR(180) NOT NULL UNIQUE,
    mot_de_passe_hash  VARCHAR(255) NOT NULL,
    telephone          VARCHAR(20),
    email_verifie      TINYINT(1) NOT NULL DEFAULT 0,
    actif              TINYINT(1) NOT NULL DEFAULT 1,
    date_creation      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_utilisateur_role FOREIGN KEY (id_role) REFERENCES role(id_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE password_reset (
    id_reset        INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur  INT NOT NULL,
    token           VARCHAR(255) NOT NULL,
    expire_le       DATETIME NOT NULL,
    CONSTRAINT fk_reset_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE studio (
    id_studio           INT AUTO_INCREMENT PRIMARY KEY,
    id_type             INT NOT NULL,
    nom                 VARCHAR(120) NOT NULL,
    description         TEXT,
    capacite            INT NOT NULL,
    surface_m2          INT,
    tarif_horaire_cents INT NOT NULL,
    duree_min_heures    INT NOT NULL DEFAULT 2,
    conditions          TEXT,
    actif               TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_studio_type FOREIGN KEY (id_type) REFERENCES type_studio(id_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE studio_photo (
    id_photo    INT AUTO_INCREMENT PRIMARY KEY,
    id_studio   INT NOT NULL,
    url         VARCHAR(255) NOT NULL,
    position    INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_photo_studio FOREIGN KEY (id_studio) REFERENCES studio(id_studio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE equipement_inclus (
    id_equipement INT AUTO_INCREMENT PRIMARY KEY,
    id_studio     INT NOT NULL,
    libelle       VARCHAR(120) NOT NULL,
    CONSTRAINT fk_equip_studio FOREIGN KEY (id_studio) REFERENCES studio(id_studio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE creneau (
    id_creneau   INT AUTO_INCREMENT PRIMARY KEY,
    id_studio    INT NOT NULL,
    date         DATE NOT NULL,
    heure_debut  TIME NOT NULL,
    heure_fin    TIME NOT NULL,
    disponible   TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_creneau_studio FOREIGN KEY (id_studio) REFERENCES studio(id_studio) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE materiel (
    id_materiel         INT AUTO_INCREMENT PRIMARY KEY,
    nom                 VARCHAR(120) NOT NULL,
    description         TEXT,
    prix_unitaire_cents INT NOT NULL,
    actif               TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reservation (
    id_reservation      INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur      INT NOT NULL,
    id_studio           INT NOT NULL,
    reference           VARCHAR(20) NOT NULL UNIQUE,
    date_tournage       DATE NOT NULL,
    heure_debut         TIME NOT NULL,
    duree_heures        INT NOT NULL,
    nb_personnes        INT NOT NULL,
    mode_retrait        VARCHAR(20) NOT NULL DEFAULT 'sur_place',
    livraison_km        INT NOT NULL DEFAULT 0,
    prix_studio_cents   INT NOT NULL,
    reduction_cents     INT NOT NULL DEFAULT 0,
    prix_materiel_cents INT NOT NULL DEFAULT 0,
    livraison_cents     INT NOT NULL DEFAULT 0,
    total_cents         INT NOT NULL,
    statut              VARCHAR(30) NOT NULL DEFAULT 'en_attente',
    date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_resa_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    CONSTRAINT fk_resa_studio      FOREIGN KEY (id_studio)      REFERENCES studio(id_studio),
    CONSTRAINT chk_statut CHECK (statut IN
        ('en_attente','confirmee','en_preparation','en_cours','terminee','annulee','attente_retour_materiel'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reservation_materiel (
    id_reservation      INT NOT NULL,
    id_materiel         INT NOT NULL,
    quantite            INT NOT NULL DEFAULT 1,
    prix_unitaire_cents INT NOT NULL,
    PRIMARY KEY (id_reservation, id_materiel),
    CONSTRAINT fk_rm_reservation FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation) ON DELETE CASCADE,
    CONSTRAINT fk_rm_materiel    FOREIGN KEY (id_materiel)    REFERENCES materiel(id_materiel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reservation_suivi (
    id_suivi        INT AUTO_INCREMENT PRIMARY KEY,
    id_reservation  INT NOT NULL,
    statut          VARCHAR(30) NOT NULL,
    date_changement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commentaire     TEXT,
    CONSTRAINT fk_suivi_reservation FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE avis (
    id_avis          INT AUTO_INCREMENT PRIMARY KEY,
    id_reservation   INT NOT NULL UNIQUE,
    id_utilisateur   INT NOT NULL,
    id_studio        INT NOT NULL,
    note             TINYINT NOT NULL,
    commentaire      TEXT,
    statut_moderation VARCHAR(20) NOT NULL DEFAULT 'en_attente',
    date_creation    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_avis_reservation FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation) ON DELETE CASCADE,
    CONSTRAINT fk_avis_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    CONSTRAINT fk_avis_studio      FOREIGN KEY (id_studio)      REFERENCES studio(id_studio),
    CONSTRAINT chk_note CHECK (note BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE horaire (
    id_horaire      INT AUTO_INCREMENT PRIMARY KEY,
    jour_semaine    VARCHAR(10) NOT NULL,
    heure_ouverture TIME,
    heure_fermeture TIME,
    ferme           TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_resa_statut     ON reservation(statut);
CREATE INDEX idx_resa_date       ON reservation(date_tournage);
CREATE INDEX idx_creneau_dispo   ON creneau(id_studio, date, disponible);
CREATE INDEX idx_avis_moderation ON avis(statut_moderation);

SET FOREIGN_KEY_CHECKS = 1;

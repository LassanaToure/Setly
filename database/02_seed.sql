SET NAMES utf8mb4;

INSERT INTO role (libelle) VALUES
    ('guest'), ('client'), ('employe'), ('administrateur');

INSERT INTO type_studio (libelle) VALUES
    ('podcast'), ('video'), ('photo');

INSERT INTO utilisateur (id_role, nom, prenom, email, mot_de_passe_hash, telephone, email_verifie, actif) VALUES
    (4, 'Touré',   'Lassana', 'admin@setly.fr',    '$2b$10$Qri1hRvrIdgJlWgJ0NQ4J.ax3xzZqdqqCWrBk2E3DIEoSDZIi/kqC', '0140000000', 1, 1),
    (3, 'Martin',  'Camille', 'employe@setly.fr',  '$2b$10$Qri1hRvrIdgJlWgJ0NQ4J.ax3xzZqdqqCWrBk2E3DIEoSDZIi/kqC', '0140000001', 1, 1),
    (2, 'Dubois',  'Léa',     'lea@example.com',    '$2b$10$Qri1hRvrIdgJlWgJ0NQ4J.ax3xzZqdqqCWrBk2E3DIEoSDZIi/kqC', '0612345678', 1, 1),
    (2, 'Nguyen',  'Hugo',    'hugo@example.com',   '$2b$10$Qri1hRvrIdgJlWgJ0NQ4J.ax3xzZqdqqCWrBk2E3DIEoSDZIi/kqC', '0698765432', 1, 1);

INSERT INTO studio (id_type, nom, description, capacite, surface_m2, tarif_horaire_cents, duree_min_heures, conditions, actif) VALUES
    (1, 'Studio Podcast Onde', 'Studio podcast insonorisé, table ronde 4 places, traitement acoustique professionnel et éclairage d''ambiance. Idéal pour enregistrements audio et vidéo courts.', 4, 18, 4500, 2, 'Arrivée 15 min avant le créneau. Aucune nourriture dans la cabine. Matériel fourni à restituer en fin de session.', 1),
    (2, 'Studio Vidéo Nord',   'Grand plateau vidéo avec fond vert cyclo, grill d''éclairage et régie. Parfait pour interviews, clips et formats courts.', 10, 60, 5500, 2, 'Accès quai de déchargement sur réservation. Port de chaussures propres obligatoire sur le cyclo. Fumée et flammes interdites.', 1),
    (3, 'Studio Photo Lumen',  'Studio photo lumière du jour avec fonds interchangeables, flashs de studio et coin maquillage. Lumineux et modulable.', 6, 40, 6000, 2, 'Les fonds papier endommagés sont facturés. Maquillage autorisé au coin dédié uniquement.', 1);

INSERT INTO studio_photo (id_studio, url, position) VALUES
    (1, '/assets/img/studios/podcast-1.jpg', 0),
    (1, '/assets/img/studios/podcast-2.jpg', 1),
    (2, '/assets/img/studios/video-1.jpg', 0),
    (2, '/assets/img/studios/video-2.jpg', 1),
    (3, '/assets/img/studios/photo-1.jpg', 0),
    (3, '/assets/img/studios/photo-2.jpg', 1);

INSERT INTO equipement_inclus (id_studio, libelle) VALUES
    (1, '4 micros Shure SM7B'), (1, 'Table de mixage Rodecaster'), (1, 'Casques de monitoring'),
    (2, 'Fond vert cyclo'), (2, 'Kit éclairage LED 3 points'), (2, 'Régie et moniteurs'),
    (3, '2 flashs de studio'), (3, 'Boîtes à lumière et parapluies'), (3, 'Fonds papier (3 coloris)');

INSERT INTO materiel (nom, description, prix_unitaire_cents, actif) VALUES
    ('Caméra Sony FX3', 'Caméra cinéma plein format 4K.', 8000, 1),
    ('Micro-cravate HF', 'Micro sans fil, kit émetteur/récepteur.', 2500, 1),
    ('Panneau LED Aputure 300x', 'Éclairage LED bi-couleur puissant.', 3500, 1),
    ('Trépied vidéo pro', 'Trépied fluide avec rotule.', 1500, 1),
    ('Objectif 24-70mm f/2.8', 'Zoom standard lumineux.', 3000, 1);

INSERT INTO horaire (jour_semaine, heure_ouverture, heure_fermeture, ferme) VALUES
    ('lundi',    '09:00:00', '19:00:00', 0),
    ('mardi',    '09:00:00', '19:00:00', 0),
    ('mercredi', '09:00:00', '19:00:00', 0),
    ('jeudi',    '09:00:00', '19:00:00', 0),
    ('vendredi', '09:00:00', '20:00:00', 0),
    ('samedi',   '10:00:00', '18:00:00', 0),
    ('dimanche', NULL, NULL, 1);

INSERT INTO creneau (id_studio, date, heure_debut, heure_fin, disponible) VALUES
    (1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '13:00:00', 1),
    (1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '18:00:00', 1),
    (1, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '09:00:00', '13:00:00', 1),
    (1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '09:00:00', '17:00:00', 1),
    (2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '13:00:00', 1),
    (2, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 1),
    (2, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '10:00:00', '18:00:00', 1),
    (3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '10:00:00', '14:00:00', 1),
    (3, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '14:00:00', '18:00:00', 1),
    (3, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', '17:00:00', 1);

INSERT INTO reservation
    (id_utilisateur, id_studio, reference, date_tournage, heure_debut, duree_heures, nb_personnes,
     mode_retrait, livraison_km, prix_studio_cents, reduction_cents, prix_materiel_cents, livraison_cents, total_cents, statut, date_creation)
VALUES
    (3, 3, 'SE-10001', DATE_SUB(CURDATE(), INTERVAL 10 DAY), '10:00:00', 4, 3,
     'sur_place', 0, 24000, 0, 3000, 0, 27000, 'terminee', DATE_SUB(NOW(), INTERVAL 15 DAY));

INSERT INTO reservation
    (id_utilisateur, id_studio, reference, date_tournage, heure_debut, duree_heures, nb_personnes,
     mode_retrait, livraison_km, prix_studio_cents, reduction_cents, prix_materiel_cents, livraison_cents, total_cents, statut, date_creation)
VALUES
    (4, 2, 'SE-10002', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '09:00:00', 8, 6,
     'livraison', 12, 44000, 4400, 8000, 1208, 48808, 'confirmee', DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO reservation_materiel (id_reservation, id_materiel, quantite, prix_unitaire_cents) VALUES
    (2, 1, 1, 8000);

INSERT INTO reservation_materiel (id_reservation, id_materiel, quantite, prix_unitaire_cents) VALUES
    (1, 2, 1, 2500), (1, 4, 1, 500);

INSERT INTO reservation_suivi (id_reservation, statut, commentaire, date_changement) VALUES
    (1, 'en_attente',     'Réservation créée par le client.', DATE_SUB(NOW(), INTERVAL 15 DAY)),
    (1, 'confirmee',      'Confirmée par l''équipe Setly.',    DATE_SUB(NOW(), INTERVAL 14 DAY)),
    (1, 'en_preparation', 'En préparation du tournage.',       DATE_SUB(NOW(), INTERVAL 12 DAY)),
    (1, 'en_cours',       'Prestation en cours.',              DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (1, 'terminee',       'Prestation terminée.',              DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (2, 'en_attente',     'Réservation créée par le client.',  DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (2, 'confirmee',      'Confirmée par l''équipe Setly.',    DATE_SUB(NOW(), INTERVAL 1 DAY));

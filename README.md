# Setly - Plateforme de réservation de studios de tournage

Application web complète (public, client, employé, administrateur) permettant de réserver en ligne des studios de tournage (podcast, vidéo, photo) et de louer du matériel en option. Projet réalisé dans le cadre du **Titre Professionnel Développeur Web et Web Mobile (DWWM)**.

- **Back-end :** PHP 8.3 pur (sans framework) + PDO + MySQL 8
- **NoSQL :** MongoDB 7 (statistiques administrateur)
- **Front-end :** HTML5, CSS3 responsive, JavaScript vanilla (filtres sans rechargement, calcul de prix en direct, validation renforcée)
- **Architecture :** front controller unique, routeur avec niveaux d'accès, modèles / services / contrôleurs / vues
- **Conteneurisation :** Docker Compose (PHP-Apache, MySQL, MongoDB, phpMyAdmin)

---

## Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Docker + Docker Compose)
- Port `8080` (application), `8081` (phpMyAdmin), `3307` (MySQL), `27018` (MongoDB) libres

---

## Installation locale (pas à pas)

```bash
# 1. Cloner le dépôt
git clone https://github.com/LassanaToure/Setly.git
cd Setly

# 2. (optionnel) copier le fichier d'environnement d'exemple
cp .env.example .env

# 3. Construire et démarrer les conteneurs
docker compose up -d --build
#   ou :  make up

# 4. Alimenter la base NoSQL (statistiques) depuis MySQL
docker compose exec app php database/sync_mongo.php
#   ou :  make sync-mongo
```

L'application est alors disponible sur **http://localhost:8080**.
phpMyAdmin est disponible sur **http://localhost:8081** (utilisateur `setly` / mot de passe `setly_pass`).

> La base MySQL est créée et peuplée automatiquement au premier démarrage via les scripts
> `database/01_schema.sql` (structure) et `database/02_seed.sql` (jeu de données de démonstration).

---

## Alternative sans Docker (Laragon ou PHP + MySQL natif)

L'application fonctionne aussi sans Docker, avec PHP 8 et MySQL (par exemple via Laragon). MongoDB n'est pas requis : la page de statistiques s'affiche simplement « sans données » si l'extension n'est pas présente.

```bash
# 1. Créer la base et l'utilisateur, puis importer la structure et les données
mysql -u root < database/00_setup_local.sql
mysql -u root setly < database/01_schema.sql
mysql -u root setly < database/02_seed.sql

# 2. Lancer le serveur web intégré de PHP (depuis le dossier Setly/)
php -S 127.0.0.1:8000 -t public router.php
```

L'application est alors disponible sur **http://127.0.0.1:8000**. Les identifiants de base par défaut (`setly` / `setly_pass` / base `setly`) correspondent à ceux créés par `00_setup_local.sql`.

## Comptes de démonstration

Mot de passe commun : **`Setly@2025!`**

| Rôle            | Email              |
|-----------------|--------------------|
| Administrateur  | `admin@setly.fr`   |
| Employé         | `employe@setly.fr` |
| Client          | `lea@example.com`  |
| Client          | `hugo@example.com` |

> Aucun compte administrateur ne peut être créé depuis l'application (règle de sécurité du cahier des charges).

---

## Commandes utiles (Makefile)

| Commande          | Effet                                             |
|-------------------|---------------------------------------------------|
| `make up`         | Démarre les conteneurs                            |
| `make down`       | Arrête les conteneurs                             |
| `make sync-mongo` | Régénère les statistiques MongoDB depuis MySQL    |
| `make reset-db`   | Réinitialise complètement la base                 |
| `make logs`       | Affiche les logs de l'application                 |
| `make shell`      | Ouvre un shell dans le conteneur applicatif       |

---

## Architecture du projet

```
Setly/
├── docker-compose.yml          # Orchestration des 4 services (dev local)
├── render.yaml                 # Configuration de déploiement Render
├── router.php                  # Routeur du serveur intégré PHP (lancement sans Docker)
├── docker/php/                 # Dockerfile, vhost.conf, entrypoint.sh
├── public/
│   ├── index.php               # Front controller (point d'entrée unique)
│   ├── .htaccess               # Réécriture d'URL vers le front controller
│   └── assets/                 # CSS, JS (validation, filtres, tarif, stats), images
├── config/
│   ├── config.php              # Configuration (BDD, règles métier)
│   └── routes.php              # Table de routes + niveaux d'accès
├── src/
│   ├── Core/                   # Router, Database (PDO), Mongo, Session, Csrf, Auth, Validator, Security, View…
│   ├── Model/                  # Accès aux données (requêtes préparées)
│   ├── Service/                # Logique métier (TarifService, ReservationService, StatsService)
│   └── Controller/             # Contrôleurs par espace (Public, Auth, Client, Employe, Admin)
├── views/                      # Templates PHP + partials réutilisés
└── database/
    ├── 00_setup_local.sql      # Création base + utilisateur (local, sans Docker)
    ├── 01_schema.sql           # Création des tables
    ├── 02_seed.sql             # Données de démonstration
    └── sync_mongo.php          # Synchronisation MySQL → MongoDB
```

---

## Sécurité mise en place

- Requêtes **préparées PDO** partout (anti-injection SQL)
- Mots de passe **hachés** (`password_hash` / `password_verify`)
- **Token CSRF** sur tous les formulaires POST
- **Validation serveur** de toutes les entrées + **échappement à l'affichage** (`htmlspecialchars`)
- **Sessions durcies** : régénération de l'ID à la connexion, cookies HttpOnly / SameSite (Secure en production)
- **En-têtes de sécurité** : CSP, X-Content-Type-Options, X-Frame-Options, Referrer-Policy
- **Prix recalculé côté serveur** (jamais lu depuis le navigateur)
- **Transaction + verrou** `SELECT … FOR UPDATE` pour empêcher la double-réservation d'un créneau
- Politique de mot de passe : **10 caractères min., 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial** (front **et** serveur)

---

## Déploiement en ligne (Render + Aiven + MongoDB Atlas)

L'application se déploie sur **Render** (hébergement de l'application via le `Dockerfile`), avec une base **MySQL managée sur Aiven** et une base **MongoDB sur Atlas** pour les statistiques. HTTPS est fourni automatiquement par Render.

### 1. Base MySQL (Aiven)

1. Créer un service **MySQL** sur [aiven.io](https://aiven.io) (plan gratuit).
2. Noter l'hôte, le port, l'utilisateur, le mot de passe et le nom de la base, puis télécharger le certificat **`ca.pem`**.
3. Importer la structure et les données depuis un client MySQL (SSL requis) :

```bash
mysql --host=<host-aiven> --port=<port> --user=<user> --password=<pass> \
      --ssl-ca=ca.pem <nom-base> < database/01_schema.sql
mysql --host=<host-aiven> --port=<port> --user=<user> --password=<pass> \
      --ssl-ca=ca.pem <nom-base> < database/02_seed.sql
```

### 2. Base MongoDB (Atlas)

Créer un cluster gratuit sur [MongoDB Atlas](https://www.mongodb.com/atlas), récupérer l'URI `mongodb+srv://...` et autoriser l'accès réseau (0.0.0.0/0 pour un projet d'école).

### 3. Application (Render)

1. Sur [render.com](https://render.com), **New → Web Service**, connecter le dépôt GitHub `Setly`.
2. Runtime **Docker**, chemin du Dockerfile `docker/php/Dockerfile` (le fichier `render.yaml` fournit déjà cette configuration).
3. Renseigner les variables d'environnement :

| Variable      | Valeur                                                        |
|---------------|--------------------------------------------------------------|
| `APP_ENV`     | `production`                                                  |
| `APP_URL`     | l'URL Render (ex. `https://setly.onrender.com`)               |
| `DB_HOST`     | hôte Aiven                                                     |
| `DB_PORT`     | port Aiven                                                     |
| `DB_NAME`     | nom de la base Aiven                                           |
| `DB_USER`     | utilisateur Aiven                                             |
| `DB_PASSWORD` | mot de passe Aiven                                            |
| `DB_SSL_CA`   | **contenu** du certificat `ca.pem` Aiven, collé tel quel      |
| `MONGODB_URI` | URI MongoDB Atlas (`mongodb+srv://...`)                       |
| `MONGO_DB`    | nom de la base Mongo (ex. `Setly`)                            |

   Le certificat Aiven n'est pas un fichier à committer : on colle son contenu dans la variable `DB_SSL_CA`, et l'application l'écrit dans un fichier temporaire au démarrage (voir `src/Core/Database.php`).

4. Déployer. Une fois en ligne, alimenter les statistiques via le shell Render :

```bash
php database/sync_mongo.php
```

En production (`APP_ENV=production`), les cookies de session passent automatiquement en `Secure` (voir `src/Core/Session.php`), et la connexion MySQL utilise le TLS d'Aiven (`DB_SSL_CA`).

> Pour un test local complet sans ces services, la pile Docker auto-hébergée reste disponible : `docker compose up -d --build` (voir plus haut).

---

## Gestion de projet

- Suivi des tâches sur un tableau Kanban Notion public : https://oil-look-41e.notion.site/41a1a7f1505b811eb428fa358b2a57ce
- Dépôt Git public avec stratégie de branches : `main` (stable) et `develop` (intégration).

---

## Documentation

Le **dossier projet** complet (fourni avec le rendu, en dehors de ce dépôt) détaille l'ensemble des
compétences du référentiel : environnement, maquettes et wireframes, interfaces statiques et dynamiques,
modèle de données (MCD / MLD / MPD), composants SQL et NoSQL, composants métier, sécurité,
jeu d'essai et veille sécurité.

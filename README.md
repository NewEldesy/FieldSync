# FieldSync

Outil de gestion des visites de terrain pour équipes techniques (installation, maintenance, configuration réseau).

## Fonctionnalités actuelles

- **Planning des visites :** création, suivi statut (en attente / confirmée / annulée), vue liste et vue Kanban
- **Gestion des équipes :** équipes, membres, rôles
- **Notifications automatiques :** SMS via Twilio, email via Gmail SMTP
- **Calendrier :** vue mensuelle des visites planifiées
- **Tableau de bord :** statistiques, graphique hebdomadaire (Chart.js), planning du jour
- **Administration :** gestion des utilisateurs et paramètres système
- **Authentification :** login, register, profil utilisateur, rôles (admin / membre)

## Stack technique

- **Backend** : PHP 8.2, architecture MVC maison (sans framework)
- **Base de données** : MySQL (PDO)
- **Frontend** : Bootstrap 5.3, Chart.js 4.4, Font Awesome 6
- **SMS** : Twilio SDK v8
- **Email** : PHPMailer v7
- **Serveur local** : XAMPP

## Installation

### Prérequis
- PHP 8.2+
- MySQL 5.7+
- Composer

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/<votre-utilisateur>/fieldsync.git
cd fieldsync

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer l'environnement
cp .env.example .env
# Éditer .env avec vos valeurs (DB, Twilio, SMTP)

# 4. Importer la base de données
mysql -u root -p fieldsync < database/fieldsync.sql

# 5. Lancer via XAMPP ou tout serveur Apache/PHP
```

### Configuration `.env`

```env
# Base de données
DB_HOST=localhost
DB_NAME=fieldsync
DB_USER=root
DB_PASSWORD=

# Email (SMTP Gmail)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@gmail.com
MAIL_PASSWORD=votre_app_password

# SMS - Twilio
SMS_PROVIDER=twilio
SMS_ACCOUNT_SID=ACxxxxxxxxxxxxxxxx
SMS_AUTH_TOKEN=xxxxxxxxxxxxxxxx
SMS_MESSAGING_SERVICE_SID=MGxxxxxxxxxxxxxxxx

# Cron
SMS_CRON_KEY=votre_cle_secrete
```

## Cron jobs (notifications automatiques)

```bash
# Traiter les SMS en attente toutes les 5 minutes
*/5 * * * * curl "https://votre-domaine.com/index.php?controller=sms&action=processPendingSms&api_key=CRON_KEY"

# Rappels avant visite toutes les 30 minutes
*/30 * * * * curl "https://votre-domaine.com/index.php?controller=sms&action=sendVisitReminders&api_key=CRON_KEY"

# Résumé quotidien chaque jour à 7h00
0 7 * * * curl "https://votre-domaine.com/index.php?controller=sms&action=sendDailySummary&api_key=CRON_KEY"
```

## Roadmap

- [ ] Module Dépenses chantier (suivi comptable, validation manager)
- [ ] Rapport de chantier (compte-rendu post-visite, photos, signature client)
- [ ] Module Clients / Chantiers (CRM terrain)
- [ ] Dashboard manager enrichi
- [ ] Export PDF des rapports

## Licence

Projet privé tous droits réservés.
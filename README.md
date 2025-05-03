# FieldSync - Système de Gestion des Visites de Terrain

## 📋 Description

FieldSync est une application web complète conçue pour gérer efficacement les visites de terrain, les équipes et les notifications. Elle permet aux organisations de planifier, suivre et optimiser leurs opérations sur le terrain grâce à un système centralisé.

## ✨ Fonctionnalités principales

- **Gestion des visites** : Planification, modification et suivi des visites de terrain
- **Calendrier interactif** : Visualisation des visites et disponibilités
- **Système de notifications** : Alertes par email, SMS et dans l'application
- **Gestion des équipes** : Organisation des membres et attribution des visites
- **Tableau de bord** : Vue d'ensemble des activités et statistiques
- **Système d'authentification** : Gestion sécurisée des utilisateurs et des rôles

## 🔧 Prérequis

- PHP 8.2 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)
- Composer (pour les dépendances)
- Extension PDO PHP activée
- Extension cURL PHP (pour les notifications SMS)

## 🚀 Installation

1. **Cloner le dépôt**
   `git clone https://github.com/NewEldesy/fieldsync.git
   cd fieldsync`
   
3. **Configurer la base de données**

- Créer une base de données MySQL
- Importer les fichiers SQL du dossier `database/`

3. **Configurer l'application**

- Modifier les paramètres dans `config/config.php` et `config/database.php`

4. **Accéder à l'application**

- Ouvrir votre navigateur et accéder à `http://localhost/fieldsync/`
- Se connecter avec les identifiants par défaut :
  - Utilisateur : `admin@example.com`
  - Mot de passe : `admin123`
- **Important** : Changer le mot de passe par défaut immédiatement après la première connexion

## 📁 Structure du projet

```
fieldsync/
├── config/                 # Fichiers de configuration
├── controllers/            # Contrôleurs MVC
├── cron/                   # Scripts pour les tâches planifiées
├── database/               # Scripts SQL
├── helpers/                # Fonctions utilitaires
├── models/                 # Modèles de données
├── public/                 # Ressources publiques (CSS, JS, images)
├── views/                  # Vues de l'application
│   ├── auth/               # Pages d'authentification
│   ├── calendar/           # Pages du calendrier
│   ├── dashboard/          # Pages du tableau de bord
│   ├── layout/             # Éléments de mise en page communs
│   ├── notifications/      # Pages de gestion des notifications
│   ├── sms/                # Pages de configuration SMS
│   └── visits/             # Pages de gestion des visites
├── index.php               # Point d'entrée de l'application
└── README.md               # Ce fichier
```

## 📘 Guide d'utilisation

### Gestion des visites

1. **Créer une visite**

  1. Accéder à `Visites > Nouvelle visite`
  2. Remplir le formulaire avec les détails de la visite
  3. Ajouter des participants
  4. Enregistrer la visite

2. **Consulter les visites**

  1. Accéder à `Visites > Liste des visites`
  2. Utiliser les filtres pour afficher les visites à venir, passées ou toutes
  3. Cliquer sur une visite pour voir les détails

3. **Modifier une visite**

  1. Dans la vue détaillée d'une visite, cliquer sur "Modifier"
  2. Mettre à jour les informations nécessaires
  3. Enregistrer les modifications

### Notifications

1. **Configurer les préférences**

  1. Accéder à `Notifications > Paramètres`
  2. Activer/désactiver les types de notifications souhaités
  3. Enregistrer les préférences

2. **Configurer les SMS**

  1. Accéder à `SMS > Paramètres`
  2. Configurer les préférences de notifications SMS
  3. Tester la configuration avec le bouton "Envoyer un SMS de test"

## 💻 Technologies utilisées

- PHP (MVC architecture)
- MySQL
- JavaScript
- Bootstrap 5
- jQuery
- AJAX
- API SMS (pour les notifications)

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forker le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request


## 📄 Licence

Ce projet est sous licence MIT.

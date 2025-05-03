<?php
// Script à exécuter via cron pour envoyer les rappels SMS

// Charger les fichiers nécessaires
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../helpers/functions.php';
require_once '../models/SmsNotification.php';
require_once '../models/UserSmsPreference.php';
require_once '../models/User.php';
require_once '../models/Visit.php';
require_once '../controllers/SmsController.php';

// Initialiser le contrôleur
$database = new Database();
$db = $database->getConnection();
$smsController = new SmsController();

// Envoyer les rappels
$smsController->sendVisitReminders();

echo "Rappels SMS envoyés avec succès.\n";
?>

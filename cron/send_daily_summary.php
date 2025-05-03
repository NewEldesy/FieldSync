<?php
// Script à exécuter via cron pour envoyer les résumés quotidiens

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

// Envoyer les résumés quotidiens
$smsController->sendDailySummary();

echo "Résumés quotidiens envoyés avec succès.\n";
?>

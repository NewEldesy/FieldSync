<?php
// Script à exécuter via cron pour traiter les SMS en attente

// Charger les fichiers nécessaires
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../helpers/functions.php';
require_once '../models/SmsNotification.php';
require_once '../controllers/SmsController.php';

// Initialiser le contrôleur
$database = new Database();
$db = $database->getConnection();
$smsController = new SmsController();

// Traiter les SMS en attente
$smsController->processPendingSms();

echo "SMS en attente traités avec succès.\n";
?>

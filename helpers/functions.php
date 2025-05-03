<?php
// Fonctions utilitaires pour l'application

/**
 * Affiche un message flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupère et supprime un message flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Affiche les messages flash directement
 */
function displayFlashMessages() {
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif;
}

/**
 * Formate une date en français
 */
function formatDateFr($date) {
    $timestamp = strtotime($date);
    $jour_semaine = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    
    $jour = $jour_semaine[date('w', $timestamp)];
    $jour_mois = date('j', $timestamp);
    $mois_nom = $mois[date('n', $timestamp) - 1];
    $annee = date('Y', $timestamp);
    
    return $jour . ' ' . $jour_mois . ' ' . $mois_nom . ' ' . $annee;
}

/**
 * Envoie un email
 */
function sendEmail($to, $subject, $body) {
    // Utilisation de PHPMailer ou autre librairie
    // Exemple simplifié
    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_ADDRESS . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Envoie un SMS (exemple avec Twilio)
 */
function sendSMS($to, $message) {
    // Créer une notification SMS
    $database = new Database();
    $db = $database->getConnection();
    
    $sms = new SmsNotification($db);
    $sms->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $sms->phone = $to;
    $sms->message = $message;
    $sms->status = 'pending';
    $sms->type = 'direct';
    $sms->related_id = 0;
    
    if ($sms->create()) {
        // Tenter d'envoyer immédiatement
        return $sms->send();
    }
    
    return false;
}

/**
 * Génère un token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie un token CSRF
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Nettoie les données d'entrée
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
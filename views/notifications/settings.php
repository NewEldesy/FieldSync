<?php include 'views/layout/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i> Paramètres de notification
                    </h5>
                </div>
                <div class="card-body">
                    <?php displayFlashMessages(); ?>
                    
                    <form method="POST" action="index.php?controller=notification&action=settings">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-4">
                            <h5>Notifications par email</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="email_daily" id="email_daily" 
                                    <?php echo (isset($user_prefs['email_daily']) && $user_prefs['email_daily']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="email_daily">
                                    Recevoir un résumé quotidien par email
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="email_visits" id="email_visits"
                                    <?php echo (isset($user_prefs['email_visits']) && $user_prefs['email_visits']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="email_visits">
                                    Recevoir des notifications par email pour les visites
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Notifications SMS</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sms_reminder" id="sms_reminder"
                                    <?php echo (isset($user_prefs['sms_reminder']) && $user_prefs['sms_reminder']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="sms_reminder">
                                    Recevoir des rappels SMS pour les visites
                                </label>
                            </div>
                            <div class="mt-2 mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i> 
                                    Pour configurer plus en détail vos préférences SMS, 
                                    <a href="index.php?controller=sms&action=settings">cliquez ici</a>.
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Notifications dans l'application</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="app_notifications" id="app_notifications"
                                    <?php echo (isset($user_prefs['app_notifications']) && $user_prefs['app_notifications']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="app_notifications">
                                    Recevoir des notifications dans l'application
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="team_changes" id="team_changes"
                                    <?php echo (isset($user_prefs['team_changes']) && $user_prefs['team_changes']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="team_changes">
                                    Être notifié des changements dans l'équipe
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=notification&action=index" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Retour aux notifications
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer les préférences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> À propos des notifications
                    </h5>
                </div>
                <div class="card-body">
                    <p>Les notifications vous permettent de rester informé des événements importants :</p>
                    <ul>
                        <li>Nouvelles visites planifiées</li>
                        <li>Modifications des visites existantes</li>
                        <li>Rappels avant les visites</li>
                        <li>Changements dans votre équipe</li>
                        <li>Résumés quotidiens de vos activités</li>
                    </ul>
                    <p class="mb-0">
                        <strong>Note :</strong> Pour recevoir des SMS, assurez-vous d'avoir configuré un numéro de téléphone valide dans votre 
                        <a href="index.php?controller=auth&action=profile">profil</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
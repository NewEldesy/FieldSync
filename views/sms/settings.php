<?php include 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1>Paramètres de notification SMS</h1>
        <p class="text-muted">Configurez comment et quand vous souhaitez recevoir des notifications par SMS.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Préférences SMS</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=sms&action=settings" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="active" name="active" <?= $preference->active ? 'checked' : '' ?>>
                        <label class="form-check-label" for="active">Activer les notifications SMS</label>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Types de notifications</h6>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="visit_reminder" name="visit_reminder" <?= $preference->visit_reminder ? 'checked' : '' ?>>
                        <label class="form-check-label" for="visit_reminder">Rappels de visites</label>
                        <small class="form-text text-muted d-block">Recevez un SMS de rappel avant chaque visite planifiée.</small>
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="visit_confirmation" name="visit_confirmation" <?= $preference->visit_confirmation ? 'checked' : '' ?>>
                        <label class="form-check-label" for="visit_confirmation">Confirmations de visites</label>
                        <small class="form-text text-muted d-block">Recevez un SMS lorsqu'une visite est confirmée.</small>
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="visit_cancellation" name="visit_cancellation" <?= $preference->visit_cancellation ? 'checked' : '' ?>>
                        <label class="form-check-label" for="visit_cancellation">Annulations de visites</label>
                        <small class="form-text text-muted d-block">Recevez un SMS lorsqu'une visite est annulée.</small>
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="daily_summary" name="daily_summary" <?= $preference->daily_summary ? 'checked' : '' ?>>
                        <label class="form-check-label" for="daily_summary">Résumé quotidien</label>
                        <small class="form-text text-muted d-block">Recevez un SMS chaque matin avec le résumé de vos visites du jour.</small>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="team_updates" name="team_updates" <?= $preference->team_updates ? 'checked' : '' ?>>
                        <label class="form-check-label" for="team_updates">Mises à jour d'équipe</label>
                        <small class="form-text text-muted d-block">Recevez un SMS lorsque les disponibilités de votre équipe changent.</small>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="reminder_time" class="form-label">Temps de rappel avant visite (minutes)</label>
                        <select class="form-select" id="reminder_time" name="reminder_time">
                            <option value="15" <?= $preference->reminder_time == 15 ? 'selected' : '' ?>>15 minutes</option>
                            <option value="30" <?= $preference->reminder_time == 30 ? 'selected' : '' ?>>30 minutes</option>
                            <option value="60" <?= $preference->reminder_time == 60 ? 'selected' : '' ?>>1 heure</option>
                            <option value="120" <?= $preference->reminder_time == 120 ? 'selected' : '' ?>>2 heures</option>
                            <option value="1440" <?= $preference->reminder_time == 1440 ? 'selected' : '' ?>>1 jour</option>
                        </select>
                        <small class="form-text text-muted">Combien de temps avant une visite souhaitez-vous recevoir un rappel par SMS.</small>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les préférences
                        </button>
                        <a href="index.php?controller=sms&action=testSms" class="btn btn-outline-secondary">
                            <i class="fas fa-paper-plane"></i> Envoyer un SMS de test
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Historique des SMS</h5>
            </div>
            <div class="card-body">
                <?php if ($sms_history->rowCount() > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php while ($sms = $sms_history->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?= $sms['status'] == 'sent' ? 'success' : ($sms['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                        <?= $sms['status'] == 'sent' ? 'Envoyé' : ($sms['status'] == 'pending' ? 'En attente' : 'Échec') ?>
                                    </span>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($sms['created_at'])) ?></small>
                                </div>
                                <p class="mb-1 mt-2"><?= $sms['message'] ?></p>
                                <?php if ($sms['sent_at']): ?>
                                    <small class="text-muted">Envoyé le <?= date('d/m/Y à H:i', strtotime($sms['sent_at'])) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted py-4">Aucun SMS envoyé pour le moment</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informations</h5>
            </div>
            <div class="card-body">
                <p>Les SMS sont envoyés via notre service de messagerie sécurisé. Les tarifs standards de votre opérateur peuvent s'appliquer pour la réception des SMS.</p>
                <p>Votre numéro de téléphone actuel : 
                    <?php
                    $user = new User($this->db);
                    $user->id = $_SESSION['user_id'];
                    $user->readOne();
                    echo !empty($user->phone) ? '<strong>' . $user->phone . '</strong>' : '<span class="text-danger">Non configuré</span>';
                    ?>
                </p>
                <?php if (empty($user->phone)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Vous devez configurer votre numéro de téléphone dans votre <a href="index.php?controller=auth&action=profile">profil</a> pour recevoir des SMS.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

<?php
// Titre de la page
$pageTitle = "Mon profil";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-user-circle"></i> Mon profil</h1>
        <p class="text-muted">Gérez vos informations personnelles et vos préférences.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit"></i> Informations personnelles</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=auth&action=profile" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               value="<?= htmlspecialchars($user->name) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?= htmlspecialchars($user->email) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Numéro de téléphone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required 
                               value="<?= htmlspecialchars($user->phone) ?>"
                               placeholder="Ex: +33612345678">
                        <div class="form-text">Utilisé pour les notifications SMS.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="index.php?controller=auth&action=changePassword" class="btn btn-outline-secondary">
                            <i class="fas fa-key"></i> Changer de mot de passe
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Sécurité du compte</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Dernière connexion</label>
                    <p class="form-control-plaintext">
                        <?= isset($user->last_login) ? date('d/m/Y H:i', strtotime($user->last_login)) : 'Information non disponible' ?>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Compte créé le</label>
                    <p class="form-control-plaintext">
                        <?= date('d/m/Y', strtotime($user->created_at)) ?>
                    </p>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Pour des raisons de sécurité, nous vous recommandons de changer régulièrement votre mot de passe.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Préférences</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="index.php?controller=notification&action=settings" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="fas fa-bell"></i> Notifications</h6>
                            <small><i class="fas fa-chevron-right"></i></small>
                        </div>
                        <p class="mb-1 text-muted small">Gérer les notifications par email et dans l'application</p>
                    </a>
                    <a href="index.php?controller=sms&action=settings" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="fas fa-sms"></i> Notifications SMS</h6>
                            <small><i class="fas fa-chevron-right"></i></small>
                        </div>
                        <p class="mb-1 text-muted small">Configurer les alertes et rappels par SMS</p>
                    </a>
                    <a href="index.php?controller=calendar&action=preferences" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="fas fa-calendar-alt"></i> Calendrier</h6>
                            <small><i class="fas fa-chevron-right"></i></small>
                        </div>
                        <p class="mb-1 text-muted small">Personnaliser l'affichage du calendrier</p>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Zone de danger</h5>
            </div>
            <div class="card-body">
                <p>Les actions ci-dessous sont irréversibles. Veuillez procéder avec prudence.</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-user-slash"></i> Supprimer mon compte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression de compte -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et toutes vos données seront définitivement supprimées.</p>
                <form id="deleteAccountForm" action="index.php?controller=auth&action=deleteAccount" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Veuillez saisir votre mot de passe pour confirmer</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

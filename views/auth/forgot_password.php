<?php
// Titre de la page
$pageTitle = "Mot de passe oublié";

// Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard&action=index');
    exit;
}

include 'views/layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-key"></i> Récupération de mot de passe</h4>
            </div>
            <div class="card-body">
                <p class="mb-4">Veuillez saisir votre adresse email. Nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
                
                <form action="index.php?controller=auth&action=forgotPassword" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Adresse email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Envoyer le lien de réinitialisation
                        </button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="index.php?controller=auth&action=login">Retour à la page de connexion</a>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h5><i class="fas fa-info-circle"></i> Vous n'avez pas reçu d'email ?</h5>
                <ul class="mb-0">
                    <li>Vérifiez votre dossier de spam ou de courrier indésirable.</li>
                    <li>Assurez-vous que l'adresse email saisie est correcte.</li>
                    <li>Si vous continuez à rencontrer des problèmes, contactez notre support.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

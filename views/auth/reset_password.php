<?php
// Titre de la page
$pageTitle = "Réinitialisation du mot de passe";

// Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard&action=index');
    exit;
}

// Vérifier si le token est présent
if (!isset($_GET['token']) || empty($_GET['token'])) {
    setFlashMessage('danger', 'Token de réinitialisation invalide ou expiré.');
    header('Location: index.php?controller=auth&action=forgotPassword');
    exit;
}

$token = $_GET['token'];

include 'views/layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-key"></i> Réinitialisation du mot de passe</h4>
            </div>
            <div class="card-body">
                <form action="index.php?controller=auth&action=resetPassword" method="post" id="resetPasswordForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <ul class="mb-0">
                                <li>Minimum 6 caractères</li>
                                <li>Au moins une lettre majuscule</li>
                                <li>Au moins un chiffre</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Réinitialiser le mot de passe
                        </button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="index.php?controller=auth&action=login">Retour à la page de connexion</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
    
    // Form validation
    const form = document.getElementById('resetPasswordForm');
    
    form.addEventListener('submit', function(event) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword !== confirmPassword) {
            event.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return;
        }
        
        if (newPassword.length < 6) {
            event.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return;
        }
        
        // Vérifier qu'il y a au moins une majuscule
        if (!/[A-Z]/.test(newPassword)) {
            event.preventDefault();
            alert('Le mot de passe doit contenir au moins une lettre majuscule.');
            return;
        }
        
        // Vérifier qu'il y a au moins un chiffre
        if (!/[0-9]/.test(newPassword)) {
            event.preventDefault();
            alert('Le mot de passe doit contenir au moins un chiffre.');
            return;
        }
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>

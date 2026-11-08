<?php
$pageTitle = "Inscription";
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard&action=index');
    exit;
}
include 'views/layout/header.php';
?>

<div class="auth-card" style="max-width:520px">
    <div class="auth-card-header">
        <div class="auth-logo-circle">
            <i class="fas fa-sync-alt"></i>
        </div>
        <div class="auth-brand"><?= APP_NAME ?></div>
        <div class="auth-subtitle">Créez votre compte</div>
    </div>

    <div class="auth-card-body">
        <form action="index.php?controller=auth&action=register" method="post" id="registerForm">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold small">Nom complet</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="name" name="name" required
                           placeholder="Jean Dupont"
                           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required
                           placeholder="votre@email.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <div class="form-text small">Jamais partagé avec des tiers.</div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-semibold small">Téléphone</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" class="form-control" id="phone" name="phone" required
                           placeholder="+22656964335"
                           value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                </div>
                <div class="form-text small">Utilisé pour les notifications SMS.</div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold small">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text small">Minimum 6 caractères.</div>
                </div>
                <div class="col-md-6">
                    <label for="password_confirm" class="form-label fw-semibold small">Confirmer</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                <label class="form-check-label small" for="terms">
                    J'accepte les <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">conditions d'utilisation</a>
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2">
                    <i class="fas fa-user-plus me-2"></i>Créer mon compte
                </button>
            </div>
        </form>
    </div>

    <div class="auth-card-footer">
        <span class="text-muted small">Déjà un compte ?</span>
        <a href="index.php?controller=auth&action=login" class="ms-1 small fw-semibold">Se connecter</a>
    </div>
</div>

<!-- Modal CGU -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Conditions d'utilisation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Acceptation des conditions</h6>
                <p class="small">En vous inscrivant à FieldSync, vous acceptez d'être lié par les présentes conditions d'utilisation.</p>
                <h6>2. Utilisation du service</h6>
                <p class="small">Vous vous engagez à utiliser le service conformément à toutes les lois applicables.</p>
                <h6>3. Confidentialité</h6>
                <p class="small">Nous respectons votre vie privée et protégeons vos données personnelles.</p>
                <h6>4. Notifications SMS</h6>
                <p class="small">En fournissant votre numéro de téléphone, vous acceptez de recevoir des notifications SMS liées à vos visites de terrain.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">J'ai compris</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    var pw = document.getElementById('password');
    var icon = this.querySelector('i');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
});
document.getElementById('registerForm').addEventListener('submit', function (e) {
    var pw  = document.getElementById('password').value;
    var pw2 = document.getElementById('password_confirm').value;
    if (pw !== pw2) { e.preventDefault(); alert('Les mots de passe ne correspondent pas.'); return; }
    if (pw.length < 6) { e.preventDefault(); alert('Le mot de passe doit contenir au moins 6 caractères.'); }
});
</script>

<?php include 'views/layout/footer.php'; ?>

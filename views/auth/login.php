<?php
$pageTitle = "Connexion";
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=dashboard&action=index');
    exit;
}
include 'views/layout/header.php';
?>

<div class="auth-card">
    <div class="auth-card-header">
        <div class="auth-logo-circle">
            <i class="fas fa-sync-alt"></i>
        </div>
        <div class="auth-brand"><?= APP_NAME ?></div>
        <div class="auth-subtitle">Connectez-vous à votre espace</div>
    </div>

    <div class="auth-card-body">
        <form action="index.php?controller=auth&action=login" method="post">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" required
                           placeholder="votre@email.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold small mb-0">Mot de passe</label>
                    <a href="index.php?controller=auth&action=forgotPassword" class="small">Mot de passe oublié ?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small" for="remember">Se souvenir de moi</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </div>
        </form>
    </div>

    <div class="auth-card-footer">
        <span class="text-muted small">Pas encore de compte ?</span>
        <a href="index.php?controller=auth&action=register" class="ms-1 small fw-semibold">S'inscrire</a>
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
</script>

<?php include 'views/layout/footer.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>

<?php
/* Page title for topbar */
$_topbar_titles = [
    'dashboard'    => 'Tableau de bord',
    'visit'        => 'Visites',
    'calendar'     => 'Calendrier',
    'team'         => 'Équipes',
    'notification' => 'Notifications',
    'sms'          => 'Paramètres SMS',
    'auth'         => 'Mon profil',
    'admin'        => 'Administration',
];
$_topbar_title = $_topbar_titles[$controller ?? ''] ?? APP_NAME;

/* First letter of user name for avatar */
$_avatar_letter = strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1));
?>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ═══ SIDEBAR ═══ -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo"><i class="fas fa-sync-alt"></i></div>
        <span class="sidebar-brand"><?= APP_NAME ?></span>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-section">Menu</li>
        <li>
            <a class="nav-link <?= ($controller ?? '') === 'dashboard' ? 'active' : '' ?>"
               href="index.php?controller=dashboard&action=index">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?= ($controller ?? '') === 'visit' ? 'active' : '' ?>"
               href="index.php?controller=visit&action=index">
                <i class="fas fa-map-marker-alt"></i>
                <span>Visites</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?= ($controller ?? '') === 'calendar' ? 'active' : '' ?>"
               href="index.php?controller=calendar&action=index">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendrier</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?= ($controller ?? '') === 'team' ? 'active' : '' ?>"
               href="index.php?controller=team&action=index">
                <i class="fas fa-users"></i>
                <span>Équipes</span>
            </a>
        </li>
        <li>
            <a class="nav-link <?= ($controller ?? '') === 'notification' ? 'active' : '' ?>"
               href="index.php?controller=notification&action=index">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
        </li>

        <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
        <li class="nav-section">Admin</li>
        <li>
            <a class="nav-link <?= ($controller ?? '') === 'admin' ? 'active' : '' ?>"
               href="index.php?controller=admin&action=index">
                <i class="fas fa-shield-alt"></i>
                <span>Administration</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-avatar"><?= $_avatar_letter ?></div>
        <div class="flex-fill overflow-hidden">
            <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
            <div class="sidebar-user-role"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></div>
        </div>
        <a href="index.php?controller=auth&action=logout" class="sidebar-logout" title="Déconnexion">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</nav>

<!-- ═══ MAIN WRAPPER ═══ -->
<div class="main-wrapper" id="mainWrapper">

    <!-- ── TOPBAR ── -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" title="Réduire/agrandir le menu">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topbar-title"><?= $_topbar_title ?></span>

        <div class="topbar-actions">
            <?php
            /* Notifications count */
            $_notif_db = new Database();
            $_notif_conn = $_notif_db->getConnection();
            $_notif_model = new Notification($_notif_conn);
            $_notif_unread = $_notif_model->readUnreadByUser($_SESSION['user_id'])->rowCount();
            ?>
            <div class="dropdown">
                <button class="topbar-btn" data-bs-toggle="dropdown" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if ($_notif_unread > 0): ?>
                    <span class="badge-dot"></span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width:300px">
                    <li><h6 class="dropdown-header">Notifications <?= $_notif_unread > 0 ? "({$_notif_unread} non lues)" : '' ?></h6></li>
                    <?php
                    $_recent = $_notif_model->readByUser($_SESSION['user_id'], 5);
                    $_has_notif = false;
                    while ($_row = $_recent->fetch(PDO::FETCH_ASSOC)):
                        $_has_notif = true;
                    ?>
                    <li>
                        <a class="dropdown-item <?= $_row['is_read'] ? '' : 'notif-unread' ?>"
                           href="index.php?controller=notification&action=markAsRead&id=<?= $_row['id'] ?>">
                            <small class="text-muted d-block"><?= date('d/m H:i', strtotime($_row['created_at'])) ?></small>
                            <?= htmlspecialchars($_row['title']) ?>
                        </a>
                    </li>
                    <?php endwhile; ?>
                    <?php if (!$_has_notif): ?>
                    <li><span class="dropdown-item text-muted">Aucune notification</span></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center small" href="index.php?controller=notification&action=index">Tout voir</a></li>
                </ul>
            </div>

            <!-- User menu -->
            <div class="dropdown">
                <button class="topbar-btn d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="sidebar-avatar" style="width:30px;height:30px;font-size:.75rem"><?= $_avatar_letter ?></div>
                    <span class="d-none d-md-inline" style="font-size:.85rem"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                    <i class="fas fa-chevron-down" style="font-size:.65rem;opacity:.6"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="index.php?controller=auth&action=profile">
                            <i class="fas fa-user-circle me-2 text-muted"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?controller=notification&action=settings">
                            <i class="fas fa-cog me-2 text-muted"></i> Préférences notifications
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?controller=sms&action=settings">
                            <i class="fas fa-sms me-2 text-muted"></i> Paramètres SMS
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="index.php?controller=auth&action=logout">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- ── CONTENT ── -->
    <main class="main-content">
        <?php
        $flash = getFlashMessage();
        if ($flash): ?>
        <div class="flash-container">
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-0" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>

<?php else: ?>

    <!-- ═══ AUTH LAYOUT (non connecté) ═══ -->
    <div class="auth-wrapper">
        <div class="auth-container w-100 d-flex justify-content-center">
        <?php
        $flash = getFlashMessage();
        if ($flash): ?>
        <div style="position:fixed;top:1rem;left:50%;transform:translateX(-50%);z-index:9999;min-width:320px">
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show shadow" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>

<?php endif; ?>

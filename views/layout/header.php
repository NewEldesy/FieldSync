<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Navigation pour les utilisateurs connectés -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.php?controller=dashboard&action=index"><?= APP_NAME ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= $controller === 'dashboard' ? 'active' : '' ?>" href="index.php?controller=dashboard&action=index">
                                <i class="fas fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller === 'calendar' ? 'active' : '' ?>" href="index.php?controller=calendar&action=index">
                                <i class="fas fa-calendar-alt"></i> Calendrier
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $controller === 'visit' ? 'active' : '' ?>" href="index.php?controller=visit&action=index">
                                <i class="fas fa-map-marker-alt"></i> Visites
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php
                                // Récupérer le nombre de notifications non lues
                                $database = new Database();
                                $db = $database->getConnection();
                                $notification = new Notification($db);
                                $unread_stmt = $notification->readUnreadByUser($_SESSION['user_id']);
                                $unread_count = $unread_stmt->rowCount();
                                
                                if ($unread_count > 0): ?>
                                    <span class="badge bg-danger"><?= $unread_count ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="width: 300px;">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <?php
                                $recent_notifications = $notification->readByUser($_SESSION['user_id'], 5);
                                $has_notifications = false;
                                
                                while ($row = $recent_notifications->fetch(PDO::FETCH_ASSOC)):
                                    $has_notifications = true;
                                ?>
                                    <li>
                                        <a class="dropdown-item <?= $row['is_read'] ? '' : 'bg-light' ?>" href="index.php?controller=notification&action=markAsRead&id=<?= $row['id'] ?>">
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small>
                                            <div><?= $row['title'] ?></div>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                                
                                <?php if (!$has_notifications): ?>
                                    <li><span class="dropdown-item">Aucune notification</span></li>
                                <?php endif; ?>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-center" href="index.php?controller=notification&action=index">
                                        Voir toutes les notifications
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> <?= $_SESSION['user_name'] ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=auth&action=profile">
                                        <i class="fas fa-user-circle"></i> Profil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=notification&action=settings">
                                        <i class="fas fa-cog"></i> Paramètres de notification
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=sms&action=settings">
                                        <i class="fas fa-sms"></i> Paramètres SMS
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="index.php?controller=auth&action=logout">
                                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>
    
    <div class="container mt-4">
        <?php
        // Afficher les messages flash
        $flash = getFlashMessage();
        if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

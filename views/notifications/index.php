<?php include 'views/layout/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-bell me-2"></i> Mes notifications</h2>
                <div>
                    <a href="index.php?controller=notification&action=settings" class="btn btn-outline-primary me-2">
                        <i class="fas fa-cog me-1"></i> Paramètres
                    </a>
                    <a href="index.php?controller=notification&action=markAllAsRead" class="btn btn-outline-success" 
                       onclick="return confirm('Êtes-vous sûr de vouloir marquer toutes les notifications comme lues ?');">
                        <i class="fas fa-check-double me-1"></i> Tout marquer comme lu
                    </a>
                </div>
            </div>

            <?php displayFlashMessages(); ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifications récentes</h5>
                        <span class="badge bg-light text-dark">
                            <?php 
                            $count = $notifications_stmt->rowCount();
                            echo $count . ' ' . ($count > 1 ? 'notifications' : 'notification');
                            ?>
                        </span>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    <?php 
                    if ($count > 0) {
                        while ($row = $notifications_stmt->fetch(PDO::FETCH_ASSOC)) {
                            $isRead = $row['is_read'] == 1;
                            $notifClass = $isRead ? '' : 'bg-light';
                            $notifIcon = '';
                            $badgeClass = 'bg-secondary';
                            
                            // Déterminer l'icône et la couleur en fonction du type
                            switch ($row['type']) {
                                case 'visit':
                                    $notifIcon = 'fas fa-calendar-check';
                                    $badgeClass = 'bg-primary';
                                    break;
                                case 'reminder':
                                    $notifIcon = 'fas fa-clock';
                                    $badgeClass = 'bg-warning text-dark';
                                    break;
                                case 'team':
                                    $notifIcon = 'fas fa-users';
                                    $badgeClass = 'bg-info';
                                    break;
                                case 'system':
                                    $notifIcon = 'fas fa-cogs';
                                    $badgeClass = 'bg-dark';
                                    break;
                                default:
                                    $notifIcon = 'fas fa-bell';
                            }
                            
                            // Formater la date
                            $date = new DateTime($row['created_at']);
                            $now = new DateTime();
                            $interval = $date->diff($now);
                            
                            if ($interval->days == 0) {
                                if ($interval->h == 0) {
                                    if ($interval->i == 0) {
                                        $timeAgo = "À l'instant";
                                    } else {
                                        $timeAgo = "Il y a " . $interval->i . " minute" . ($interval->i > 1 ? 's' : '');
                                    }
                                } else {
                                    $timeAgo = "Il y a " . $interval->h . " heure" . ($interval->h > 1 ? 's' : '');
                                }
                            } elseif ($interval->days == 1) {
                                $timeAgo = "Hier";
                            } else {
                                $timeAgo = "Il y a " . $interval->days . " jours";
                            }
                    ?>
                    <div class="list-group-item <?php echo $notifClass; ?>">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="notification-icon rounded-circle d-flex justify-content-center align-items-center">
                                        <i class="<?php echo $notifIcon; ?>"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        <?php if (!$isRead): ?>
                                            <span class="badge bg-danger me-1">Nouveau</span>
                                        <?php endif; ?>
                                        <span class="badge <?php echo $badgeClass; ?> me-1">
                                            <?php echo ucfirst($row['type']); ?>
                                        </span>
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </h6>
                                    <p class="mb-1"><?php echo htmlspecialchars($row['message']); ?></p>
                                    <small class="text-muted"><?php echo $timeAgo; ?></small>
                                </div>
                            </div>
                            <div class="d-flex">
                                <?php if (!$isRead): ?>
                                <a href="index.php?controller=notification&action=markAsRead&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-success me-2" title="Marquer comme lu">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($row['related_id']): ?>
                                <a href="index.php?controller=visit&action=view&id=<?php echo $row['related_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        }
                    } else {
                    ?>
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-bell-slash fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Vous n'avez aucune notification pour le moment.</p>
                    </div>
                    <?php } ?>
                </div>
            </div>
            
            <?php if ($count > 0): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i> À propos des notifications</h5>
                    <p class="card-text">
                        Les notifications vous informent des événements importants liés à vos visites et à votre équipe.
                        Vous pouvez configurer vos préférences de notification dans les 
                        <a href="index.php?controller=notification&action=settings">paramètres</a>.
                    </p>
                    <p class="card-text mb-0">
                        <strong>Astuce :</strong> Cliquez sur <i class="fas fa-check"></i> pour marquer une notification comme lue 
                        ou sur <i class="fas fa-eye"></i> pour voir les détails associés.
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.notification-icon {
    width: 40px;
    height: 40px;
    background-color: #e9ecef;
    color: #495057;
}
</style>

<?php include 'views/layout/footer.php'; ?>

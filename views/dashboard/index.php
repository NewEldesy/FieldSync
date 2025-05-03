<?php include 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col">
        <h1>Tableau de bord</h1>
        <p class="text-muted">Bienvenue, <?= $_SESSION['user_name'] ?>. Voici un aperçu de vos activités et de votre planning.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Visites à venir</h5>
                <p class="card-text">
                    <span class="display-4"><?= $upcoming_visits->rowCount() ?></span>
                    <span class="text-muted">cette semaine</span>
                </p>
                <a href="index.php?controller=visit&action=index" class="btn btn-primary">Voir toutes les visites</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Membres disponibles</h5>
                <p class="card-text">
                    <span class="display-4"><?= $team_members->rowCount() ?></span>
                    <span class="text-muted">membres dans l'équipe</span>
                </p>
                <a href="index.php?controller=calendar&action=index" class="btn btn-primary">Voir les disponibilités</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Notifications</h5>
                <p class="card-text">
                    <span class="display-4"><?= $unread_notifications->rowCount() ?></span>
                    <span class="text-muted">non lues</span>
                </p>
                <a href="index.php?controller=notification&action=index" class="btn btn-primary">Voir les notifications</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Planning des visites</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="visitTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="today-tab" data-bs-toggle="tab" data-bs-target="#today" type="button" role="tab" aria-controls="today" aria-selected="true">Aujourd'hui</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tomorrow-tab" data-bs-toggle="tab" data-bs-target="#tomorrow" type="button" role="tab" aria-controls="tomorrow" aria-selected="false">Demain</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="week-tab" data-bs-toggle="tab" data-bs-target="#week" type="button" role="tab" aria-controls="week" aria-selected="false">Cette semaine</button>
                    </li>
                </ul>
                <div class="tab-content" id="visitTabsContent">
                    <div class="tab-pane fade show active" id="today" role="tabpanel" aria-labelledby="today-tab">
                        <?php
                        $today = date('Y-m-d');
                        $has_visits_today = false;
                        
                        // Réinitialiser le curseur
                        $upcoming_visits->execute();
                        
                        while ($row = $upcoming_visits->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['date'] == $today) {
                                $has_visits_today = true;
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title"><?= $row['title'] ?></h5>
                                            <span class="badge bg-<?= $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                <?= $row['status'] == 'confirmed' ? 'Confirmé' : ($row['status'] == 'pending' ? 'En attente' : 'Annulé') ?>
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <i class="fas fa-clock"></i> <?= $row['start_time'] ?> - <?= $row['end_time'] ?><br>
                                            <i class="fas fa-map-marker-alt"></i> <?= $row['location'] ?>
                                        </p>
                                        <a href="index.php?controller=visit&action=view&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        
                        if (!$has_visits_today) {
                            echo '<div class="text-center py-4 text-muted">Aucune visite prévue pour aujourd\'hui</div>';
                        }
                        ?>
                    </div>
                    <div class="tab-pane fade" id="tomorrow" role="tabpanel" aria-labelledby="tomorrow-tab">
                        <?php
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        $has_visits_tomorrow = false;
                        
                        // Réinitialiser le curseur
                        $upcoming_visits->execute();
                        
                        while ($row = $upcoming_visits->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['date'] == $tomorrow) {
                                $has_visits_tomorrow = true;
                                ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title"><?= $row['title'] ?></h5>
                                            <span class="badge bg-  $row['title'] ?></h5>
                                            <span class="badge bg-<?= $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                <?= $row['status'] == 'confirmed' ? 'Confirmé' : ($row['status'] == 'pending' ? 'En attente' : 'Annulé') ?>
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <i class="fas fa-clock"></i> <?= $row['start_time'] ?> - <?= $row['end_time'] ?><br>
                                            <i class="fas fa-map-marker-alt"></i> <?= $row['location'] ?>
                                        </p>
                                        <a href="index.php?controller=visit&action=view&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        
                        if (!$has_visits_tomorrow) {
                            echo '<div class="text-center py-4 text-muted">Aucune visite prévue pour demain</div>';
                        }
                        ?>
                    </div>
                    <div class="tab-pane fade" id="week" role="tabpanel" aria-labelledby="week-tab">
                        <?php
                        $has_visits_week = false;
                        
                        // Réinitialiser le curseur
                        $upcoming_visits->execute();
                        
                        while ($row = $upcoming_visits->fetch(PDO::FETCH_ASSOC)) {
                            $has_visits_week = true;
                            ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title"><?= $row['title'] ?></h5>
                                        <span class="badge bg-<?= $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                            <?= $row['status'] == 'confirmed' ? 'Confirmé' : ($row['status'] == 'pending' ? 'En attente' : 'Annulé') ?>
                                        </span>
                                    </div>
                                    <p class="card-text">
                                        <i class="fas fa-calendar-alt"></i> <?= formatDateFr($row['date']) ?><br>
                                        <i class="fas fa-clock"></i> <?= $row['start_time'] ?> - <?= $row['end_time'] ?><br>
                                        <i class="fas fa-map-marker-alt"></i> <?= $row['location'] ?>
                                    </p>
                                    <a href="index.php?controller=visit&action=view&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                </div>
                            </div>
                            <?php
                        }
                        
                        if (!$has_visits_week) {
                            echo '<div class="text-center py-4 text-muted">Aucune visite prévue pour cette semaine</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <a href="index.php?controller=visit&action=create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Planifier une visite
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Disponibilité de l'équipe</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
                    while ($member = $team_members->fetch(PDO::FETCH_ASSOC)) {
                        // Dans une application réelle, on récupérerait le statut actuel de chaque membre
                        $status = rand(0, 2); // 0 = disponible, 1 = occupé, 2 = absent
                        $status_class = $status == 0 ? 'success' : ($status == 1 ? 'warning' : 'secondary');
                        $status_text = $status == 0 ? 'Disponible' : ($status == 1 ? 'Occupé' : 'Absent');
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold"><?= $member['name'] ?></span><br>
                                <small class="text-muted"><?= $member['role'] ?? 'Membre' ?></small>
                            </div>
                            <span class="badge bg-<?= $status_class ?> rounded-pill"><?= $status_text ?></span>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <div class="mt-3 text-center">
                    <a href="index.php?controller=calendar&action=availability" class="btn btn-outline-primary">
                        <i class="fas fa-clock"></i> Mettre à jour ma disponibilité
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Notifications récentes</h5>
            </div>
            <div class="card-body">
                <?php
                $has_notifications = false;
                
                while ($notification = $all_notifications->fetch(PDO::FETCH_ASSOC)) {
                    $has_notifications = true;
                    $notification_class = $notification['is_read'] ? '' : 'bg-light';
                    ?>
                    <div class="card mb-2 <?= $notification_class ?>">
                        <div class="card-body py-2 px-3">
                            <h6 class="card-title"><?= $notification['title'] ?></h6>
                            <p class="card-text small"><?= $notification['message'] ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
                                </small>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                
                if (!$has_notifications) {
                    echo '<div class="text-center py-4 text-muted">Aucune notification récente</div>';
                }
                ?>
                <div class="mt-3 text-center">
                    <a href="index.php?controller=notification&action=index" class="btn btn-outline-primary">
                        <i class="fas fa-bell"></i> Voir toutes les notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

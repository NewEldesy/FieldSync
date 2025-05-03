<?php
// Titre de la page
$pageTitle = "Détails du " . formatDateFr($date);

include 'views/layout/header.php';

// Formater la date pour l'affichage
$formattedDate = formatDateFr($date);

// Vérifier si c'est aujourd'hui
$isToday = ($date == date('Y-m-d'));

// Jour précédent et suivant
$prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
$nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
?>

<div class="row mb-4">
    <div class="col">
        <h1>
            <?php if ($isToday): ?>
                <span class="badge bg-primary me-2">Aujourd'hui</span>
            <?php endif; ?>
            <i class="fas fa-calendar-day"></i> <?= $formattedDate ?>
        </h1>
        <p class="text-muted">Consultez les visites et disponibilités pour cette journée.</p>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="index.php?controller=calendar&action=date&date=<?= $prevDate ?>" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left"></i> Jour précédent
            </a>
            <a href="index.php?controller=calendar&action=index" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt"></i> Calendrier
            </a>
            <a href="index.php?controller=calendar&action=date&date=<?= $nextDate ?>" class="btn btn-outline-primary">
                Jour suivant <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Visites planifiées</h5>
                <a href="index.php?controller=visit&action=create&date=<?= $date ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Planifier une visite
                </a>
            </div>
            <div class="card-body">
                <?php if ($visits_stmt->rowCount() > 0): ?>
                    <div class="timeline">
                        <?php while ($visit = $visits_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="timeline-item">
                                <div class="timeline-time">
                                    <?= substr($visit['start_time'], 0, 5) ?> - <?= substr($visit['end_time'], 0, 5) ?>
                                </div>
                                <div class="timeline-content">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-1"><?= htmlspecialchars($visit['title']) ?></h5>
                                                <span class="badge bg-<?= $visit['status'] == 'confirmed' ? 'success' : ($visit['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                    <?= $visit['status'] == 'confirmed' ? 'Confirmée' : ($visit['status'] == 'pending' ? 'En attente' : 'Annulée') ?>
                                                </span>
                                            </div>
                                            <p class="card-text text-muted mb-2">
                                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($visit['location']) ?>
                                            </p>
                                            <?php if (!empty($visit['description'])): ?>
                                                <p class="card-text"><?= htmlspecialchars($visit['description']) ?></p>
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div>
                                                    <small class="text-muted">Créée par: <?= htmlspecialchars($visit['creator_name']) ?></small>
                                                </div>
                                                <div>
                                                    <a href="index.php?controller=visit&action=view&id=<?= $visit['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="lead">Aucune visite planifiée pour cette journée.</p>
                        <a href="index.php?controller=visit&action=create&date=<?= $date ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Planifier une visite
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-clock"></i> Mes disponibilités</h5>
                <a href="index.php?controller=calendar&action=availability&date=<?= $date ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Ajouter
                </a>
            </div>
            <div class="card-body">
                <?php
                // Récupérer les disponibilités de l'utilisateur pour cette date
                $user_availabilities_stmt = $availability->readByUserAndDate($_SESSION['user_id'], $date);
                
                if ($user_availabilities_stmt->rowCount() > 0):
                ?>
                    <ul class="list-group list-group-flush">
                        <?php while ($avail = $user_availabilities_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-<?= $avail['status'] == 'available' ? 'success' : ($avail['status'] == 'busy' ? 'warning' : 'secondary') ?> me-2">
                                            <i class="fas fa-<?= $avail['status'] == 'available' ? 'check' : ($avail['status'] == 'busy' ? 'clock' : 'plane') ?>"></i>
                                        </span>
                                        <strong><?= substr($avail['start_time'], 0, 5) ?> - <?= substr($avail['end_time'], 0, 5) ?></strong>
                                        <span class="ms-2 text-muted">
                                            <?= $avail['status'] == 'available' ? 'Disponible' : ($avail['status'] == 'busy' ? 'Occupé' : 'Absent') ?>
                                        </span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="index.php?controller=calendar&action=availability&id=<?= $avail['id'] ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAvailabilityModal" data-id="<?= $avail['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <p>Vous n'avez pas encore défini vos disponibilités pour cette journée.</p>
                        <a href="index.php?controller=calendar&action=availability&date=<?= $date ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter une disponibilité
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Disponibilité de l'équipe</h5>
            </div>
            <div class="card-body">
                <?php if ($availabilities_stmt->rowCount() > 0): ?>
                    <ul class="list-group list-group-flush">
                        <?php 
                        $team_availabilities = [];
                        while ($avail = $availabilities_stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($avail['user_id'] != $_SESSION['user_id']) {
                                $team_availabilities[] = $avail;
                            }
                        }
                        
                        if (count($team_availabilities) > 0):
                        ?>
                            <?php foreach ($team_availabilities as $avail): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($avail['name']) ?></strong>
                                        </div>
                                        <span class="badge bg-<?= $avail['status'] == 'available' ? 'success' : ($avail['status'] == 'busy' ? 'warning' : 'secondary') ?>">
                                            <?= $avail['status'] == 'available' ? 'Disponible' : ($avail['status'] == 'busy' ? 'Occupé' : 'Absent') ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <?= substr($avail['start_time'], 0, 5) ?> - <?= substr($avail['end_time'], 0, 5) ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center py-3 text-muted">Aucun membre de l'équipe n'a défini ses disponibilités pour cette journée.</p>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-center py-3 text-muted">Aucune disponibilité n'a été définie pour cette journée.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression de disponibilité -->
<div class="modal fade" id="deleteAvailabilityModal" tabindex="-1" aria-labelledby="deleteAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAvailabilityModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette disponibilité ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDeleteAvailability" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 120px;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-time {
    position: absolute;
    left: 0;
    width: 100px;
    text-align: right;
    padding-right: 20px;
    font-weight: bold;
    color: #6c757d;
}

.timeline-content {
    margin-left: 140px;
    position: relative;
}

.timeline-content:before {
    content: '';
    position: absolute;
    top: 15px;
    left: -20px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #0d6efd;
    border: 2px solid #fff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression de disponibilité
    const deleteModal = document.getElementById('deleteAvailabilityModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteAvailability');
    
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const availabilityId = button.getAttribute('data-id');
        confirmDeleteBtn.href = 'index.php?controller=calendar&action=deleteAvailability&id=' + availabilityId + '&date=<?= $date ?>';
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>


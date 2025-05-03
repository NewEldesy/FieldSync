<?php
// Titre de la page
$pageTitle = "Gestion des visites";

include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-map-marker-alt"></i> Gestion des visites</h1>
        <p class="text-muted">Consultez et gérez toutes vos visites de terrain.</p>
    </div>
    <div class="col-auto">
        <a href="index.php?controller=visit&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Planifier une visite
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="visitTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                    À venir
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">
                    Passées
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="false">
                    Toutes
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="visitTabsContent">
            <!-- Visites à venir -->
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hasUpcoming = false;
                            $visits_stmt->execute(); // Réinitialiser le curseur
                            
                            while ($visit = $visits_stmt->fetch(PDO::FETCH_ASSOC)) {
                                if (strtotime($visit['date']) >= strtotime(date('Y-m-d'))) {
                                    $hasUpcoming = true;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($visit['title']) ?></td>
                                        <td><?= formatDateFr($visit['date']) ?></td>
                                        <td><?= substr($visit['start_time'], 0, 5) ?> - <?= substr($visit['end_time'], 0, 5) ?></td>
                                        <td><?= htmlspecialchars($visit['location']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $visit['status'] == 'confirmed' ? 'success' : ($visit['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                <?= $visit['status'] == 'confirmed' ? 'Confirmée' : ($visit['status'] == 'pending' ? 'En attente' : 'Annulée') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?controller=visit&action=view&id=<?= $visit['id'] ?>" class="btn btn-outline-primary" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($visit['created_by'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                                                    <a href="index.php?controller=visit&action=edit&id=<?= $visit['id'] ?>" class="btn btn-outline-secondary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger delete-visit" data-id="<?= $visit['id'] ?>" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            
                            if (!$hasUpcoming) {
                                echo '<tr><td colspan="6" class="text-center py-4">Aucune visite à venir.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Visites passées -->
            <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hasPast = false;
                            $visits_stmt->execute(); // Réinitialiser le curseur
                            
                            while ($visit = $visits_stmt->fetch(PDO::FETCH_ASSOC)) {
                                if (strtotime($visit['date']) < strtotime(date('Y-m-d'))) {
                                    $hasPast = true;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($visit['title']) ?></td>
                                        <td><?= formatDateFr($visit['date']) ?></td>
                                        <td><?= substr($visit['start_time'], 0, 5) ?> - <?= substr($visit['end_time'], 0, 5) ?></td>
                                        <td><?= htmlspecialchars($visit['location']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $visit['status'] == 'confirmed' ? 'success' : ($visit['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                <?= $visit['status'] == 'confirmed' ? 'Confirmée' : ($visit['status'] == 'pending' ? 'En attente' : 'Annulée') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="index.php?controller=visit&action=view&id=<?= $visit['id'] ?>" class="btn btn-outline-primary" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($visit['created_by'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                                                    <a href="index.php?controller=visit&action=edit&id=<?= $visit['id'] ?>" class="btn btn-outline-secondary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger delete-visit" data-id="<?= $visit['id'] ?>" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            
                            if (!$hasPast) {
                                echo '<tr><td colspan="6" class="text-center py-4">Aucune visite passée.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Toutes les visites -->
            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Lieu</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hasVisits = false;
                            $visits_stmt->execute(); // Réinitialiser le curseur
                            
                            while ($visit = $visits_stmt->fetch(PDO::FETCH_ASSOC)) {
                                $hasVisits = true;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($visit['title']) ?></td>
                                    <td><?= formatDateFr($visit['date']) ?></td>
                                    <td><?= substr($visit['start_time'], 0, 5) ?> - <?= substr($visit['end_time'], 0, 5) ?></td>
                                    <td><?= htmlspecialchars($visit['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $visit['status'] == 'confirmed' ? 'success' : ($visit['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                            <?= $visit['status'] == 'confirmed' ? 'Confirmée' : ($visit['status'] == 'pending' ? 'En attente' : 'Annulée') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=visit&action=view&id=<?= $visit['id'] ?>" class="btn btn-outline-primary" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($visit['created_by'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                                                <a href="index.php?controller=visit&action=edit&id=<?= $visit['id'] ?>" class="btn btn-outline-secondary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger delete-visit" data-id="<?= $visit['id'] ?>" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            
                            if (!$hasVisits) {
                                echo '<tr><td colspan="6" class="text-center py-4">Aucune visite trouvée.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres et recherche</h5>
    </div>
    <div class="card-body">
        <form action="index.php" method="get" class="row g-3">
            <input type="hidden" name="controller" value="visit">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Titre, lieu..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous</option>
                    <option value="confirmed" <?= isset($_GET['status']) && $_GET['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmées</option>
                    <option value="pending" <?= isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="cancelled" <?= isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : '' ?>>Annulées</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date_range" class="form-label">Période</label>
                <select class="form-select" id="date_range" name="date_range">
                    <option value="">Toutes les dates</option>
                    <option value="today" <?= isset($_GET['date_range']) && $_GET['date_range'] == 'today' ? 'selected' : '' ?>>Aujourd'hui</option>
                    <option value="tomorrow" <?= isset($_GET['date_range']) && $_GET['date_range'] == 'tomorrow' ? 'selected' : '' ?>>Demain</option>
                    <option value="week" <?= isset($_GET['date_range']) && $_GET['date_range'] == 'week' ? 'selected' : '' ?>>Cette semaine</option>
                    <option value="month" <?= isset($_GET['date_range']) && $_GET['date_range'] == 'month' ? 'selected' : '' ?>>Ce mois</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteVisitModal" tabindex="-1" aria-labelledby="deleteVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteVisitModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette visite ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression
    const deleteButtons = document.querySelectorAll('.delete-visit');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const visitId = this.getAttribute('data-id');
            confirmDeleteBtn.href = 'index.php?controller=visit&action=delete&id=' + visitId;
            
            // Afficher la modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteVisitModal'));
            deleteModal.show();
        });
    });
    
    // Conserver l'onglet actif après rechargement de la page
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`[data-bs-target="${hash}"]`);
        if (tab) {
            const tabInstance = new bootstrap.Tab(tab);
            tabInstance.show();
        }
    }
});
</script>

<?php include 'views/layout/footer.php'; ?>


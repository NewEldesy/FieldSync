<?php
// Titre de la page
$pageTitle = "Détails de la visite";

include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-map-marker-alt"></i> Détails de la visite</h1>
        <p class="text-muted">Consultez les informations détaillées de la visite.</p>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="index.php?controller=visit&action=index" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Liste des visites
            </a>
            <?php if ($visit->created_by == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                <a href="index.php?controller=visit&action=edit&id=<?= $visit->id ?>" class="btn btn-outline-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteVisitModal">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations générales</h5>
                <span class="badge bg-<?= $visit->status == 'confirmed' ? 'success' : ($visit->status == 'pending' ? 'warning' : 'danger') ?> fs-6">
                    <?= $visit->status == 'confirmed' ? 'Confirmée' : ($visit->status == 'pending' ? 'En attente' : 'Annulée') ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Titre:</div>
                    <div class="col-md-9"><?= htmlspecialchars($visit->title) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Date:</div>
                    <div class="col-md-9"><?= formatDateFr($visit->date) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Horaire:</div>
                    <div class="col-md-9"><?= substr($visit->start_time, 0, 5) ?> - <?= substr($visit->end_time, 0, 5) ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Lieu:</div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($visit->location) ?>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($visit->location) ?>" target="_blank" class="ms-2 btn btn-sm btn-outline-primary">
                            <i class="fas fa-map"></i> Voir sur la carte
                        </a>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Description:</div>
                    <div class="col-md-9">
                        <?php if (!empty($visit->description)): ?>
                            <p><?= nl2br(htmlspecialchars($visit->description)) ?></p>
                        <?php else: ?>
                            <p class="text-muted">Aucune description fournie.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Créée par:</div>
                    <div class="col-md-9"><?= htmlspecialchars($visit->created_by) ?></div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 fw-bold">Créée le:</div>
                    <div class="col-md-9"><?= date('d/m/Y H:i', strtotime($visit->created_at)) ?></div>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Participants</h5>
            </div>
            <div class="card-body">
                <?php if ($participants_stmt->rowCount() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <?php if ($visit->created_by == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($participant = $participants_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($participant['name']) ?></td>
                                        <td><?= htmlspecialchars($participant['email']) ?></td>
                                        <td><?= htmlspecialchars($participant['phone']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $participant['status'] == 'confirmed' ? 'success' : ($participant['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                                <?= $participant['status'] == 'confirmed' ? 'Confirmé' : ($participant['status'] == 'pending' ? 'En attente' : 'Refusé') ?>
                                            </span>
                                        </td>
                                        <?php if ($visit->created_by == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-success update-status" data-user-id="<?= $participant['user_id'] ?>" data-status="confirmed" title="Confirmer">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger update-status" data-user-id="<?= $participant['user_id'] ?>" data-status="declined" title="Refuser">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center py-3">Aucun participant n'a été ajouté à cette visite.</p>
                <?php endif; ?>
                
                <?php if ($visit->created_by == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                            <i class="fas fa-user-plus"></i> Ajouter un participant
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-comments"></i> Notes et commentaires</h5>
            </div>
            <div class="card-body">
                <!-- Ici, vous pourriez ajouter un système de notes et commentaires -->
                <div class="text-center py-3">
                    <p class="text-muted">Fonctionnalité de notes et commentaires à venir.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Calendrier</h5>
            </div>
            <div class="card-body">
                <div class="d-grid">
                    <a href="index.php?controller=calendar&action=date&date=<?= $visit->date ?>" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-day"></i> Voir cette journée dans le calendrier
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bell"></i> Rappels</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="reminder_email" checked>
                    <label class="form-check-label" for="reminder_email">
                        Recevoir un rappel par email
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="reminder_sms" checked>
                    <label class="form-check-label" for="reminder_sms">
                        Recevoir un rappel par SMS
                    </label>
                </div>
                <div class="mb-3">
                    <label for="reminder_time" class="form-label">Quand</label>
                    <select class="form-select" id="reminder_time">
                        <option value="15">15 minutes avant</option>
                        <option value="30">30 minutes avant</option>
                        <option value="60" selected>1 heure avant</option>
                        <option value="120">2 heures avant</option>
                        <option value="1440">1 jour avant</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="button" class="btn btn-primary" id="saveReminders">
                        <i class="fas fa-save"></i> Enregistrer les préférences
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Documents</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Aucun document n'a été ajouté à cette visite.</p>
                <div class="d-grid">
                    <button type="button" class="btn btn-outline-primary" disabled>
                        <i class="fas fa-upload"></i> Ajouter un document
                    </button>
                </div>
                <small class="text-muted d-block text-center mt-2">Fonctionnalité à venir</small>
            </div>
        </div>
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
                <p>Êtes-vous sûr de vouloir supprimer cette visite ? Cette action est irréversible et toutes les données associées seront perdues.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="index.php?controller=visit&action=delete&id=<?= $visit->id ?>" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de participant -->
<div class="modal fade" id="addParticipantModal" tabindex="-1" aria-labelledby="addParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParticipantModalLabel">Ajouter un participant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addParticipantForm" action="index.php?controller=visit&action=addParticipant" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="visit_id" value="<?= $visit->id ?>">
                    
                    <div class="mb-3">
                        <label for="participant" class="form-label">Sélectionner un utilisateur</label>
                        <select class="form-select" id="participant" name="user_id" required>
                            <option value="">Choisir un utilisateur...</option>
                            <?php
                            // Récupérer tous les utilisateurs qui ne sont pas déjà participants
                            $database = new Database();
                            $db = $database->getConnection();
                            
                            $query = "SELECT u.id, u.name, u.email 
                                      FROM users u 
                                      WHERE u.id NOT IN (
                                          SELECT vp.user_id 
                                          FROM visit_participants vp 
                                          WHERE vp.visit_id = :visit_id
                                      )
                                      ORDER BY u.name ASC";
                            
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':visit_id', $visit->id);
                            $stmt->execute();
                            
                            while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['name']) . ' (' . htmlspecialchars($user['email']) . ')</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="addParticipantForm" class="btn btn-primary">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de mise à jour du statut des participants
    const updateStatusButtons = document.querySelectorAll('.update-status');
    
    updateStatusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const status = this.getAttribute('data-status');
            
            // Redirection vers l'action de mise à jour du statut
            window.location.href = `index.php?controller=visit&action=updateParticipantStatus&visit_id=<?= $visit->id ?>&user_id=${userId}&status=${status}`;
        });
    });
    
    // Gestion des préférences de rappel
    const saveRemindersBtn = document.getElementById('saveReminders');
    
    saveRemindersBtn.addEventListener('click', function() {
        const emailReminder = document.getElementById('reminder_email').checked;
        const smsReminder = document.getElementById('reminder_sms').checked;
        const reminderTime = document.getElementById('reminder_time').value;
        
        // Simuler l'enregistrement des préférences
        alert('Préférences de rappel enregistrées avec succès !');
        // Dans une application réelle, vous enverriez ces données au serveur via AJAX ou un formulaire
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>


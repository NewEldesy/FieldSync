<?php
// Titre de la page
$pageTitle = "Modifier une visite";

include 'views/layout/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-edit"></i> Modifier une visite</h1>
        <p class="text-muted">Modifiez les détails de la visite et gérez les participants.</p>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="index.php?controller=visit&action=view&id=<?= $visit->id ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux détails
            </a>
            <a href="index.php?controller=visit&action=index" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Liste des visites
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Informations de la visite</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=visit&action=edit&id=<?= $visit->id ?>" method="post" id="editVisitForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre de la visite <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?= htmlspecialchars($visit->title) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($visit->description) ?></textarea>
                        <div class="form-text">Décrivez l'objectif de la visite, les points à aborder, etc.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Lieu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" required 
                               value="<?= htmlspecialchars($visit->location) ?>">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" required 
                                   value="<?= $visit->date ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="start_time" class="form-label">Heure de début <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required 
                                   value="<?= $visit->start_time ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_time" class="form-label">Heure de fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required 
                                   value="<?= $visit->end_time ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?= $visit->status == 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="confirmed" <?= $visit->status == 'confirmed' ? 'selected' : '' ?>>Confirmée</option>
                            <option value="cancelled" <?= $visit->status == 'cancelled' ? 'selected' : '' ?>>Annulée</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="participants" class="form-label">Participants <span class="text-danger">*</span></label>
                        <select class="form-select" id="participants" name="participants[]" multiple required>
                            <?php
                            // Réinitialiser le curseur des membres de l'équipe
                            $team_members_stmt->execute();
                            
                            while ($member = $team_members_stmt->fetch(PDO::FETCH_ASSOC)):
                                if ($member['id'] != $_SESSION['user_id']):
                                    $isSelected = in_array($member['id'], $current_participants);
                            ?>
                                <option value="<?= $member['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($member['name']) ?>
                                </option>
                            <?php
                                endif;
                            endwhile;
                            ?>
                        </select>
                        <div class="form-text">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs participants.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=visit&action=view&id=<?= $visit->id ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
            </div>
            <div class="card-body">
                <p>Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.</p>
                <p>Les participants recevront une notification lorsque vous modifierez les détails de la visite.</p>
                <p>Si vous changez le statut de la visite en "Annulée", tous les participants en seront informés.</p>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Disponibilités</h5>
            </div>
            <div class="card-body">
                <p>Vérifiez les disponibilités de votre équipe avant de modifier la date ou l'heure de la visite.</p>
                <div class="d-grid">
                    <a href="index.php?controller=calendar&action=date&date=<?= $visit->date ?>" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-day"></i> Voir les disponibilités
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.getElementById('editVisitForm');
    
    form.addEventListener('submit', function(event) {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const participants = document.getElementById('participants');
        
        if (startTime >= endTime) {
            event.preventDefault();
            alert('L\'heure de fin doit être postérieure à l\'heure de début.');
            return;
        }
        
        if (participants.selectedOptions.length === 0) {
            event.preventDefault();
            alert('Veuillez sélectionner au moins un participant.');
            return;
        }
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>

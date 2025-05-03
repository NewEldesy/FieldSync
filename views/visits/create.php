<?php
// Titre de la page
$pageTitle = "Planifier une visite";

include 'views/layout/header.php';

// Récupérer la date depuis l'URL si elle est fournie
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>

<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-plus"></i> Planifier une visite</h1>
        <p class="text-muted">Créez une nouvelle visite de terrain et invitez des participants.</p>
    </div>
    <div class="col-auto">
        <a href="index.php?controller=visit&action=index" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Informations de la visite</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=visit&action=create" method="post" id="createVisitForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre de la visite <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                        <div class="form-text">Décrivez l'objectif de la visite, les points à aborder, etc.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Lieu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" required 
                               value="<?= isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '' ?>">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" required 
                                   value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : $selectedDate ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="start_time" class="form-label">Heure de début <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required 
                                   value="<?= isset($_POST['start_time']) ? htmlspecialchars($_POST['start_time']) : '09:00' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_time" class="form-label">Heure de fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required 
                                   value="<?= isset($_POST['end_time']) ? htmlspecialchars($_POST['end_time']) : '12:00' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="participants" class="form-label">Participants <span class="text-danger">*</span></label>
                        <select class="form-select" id="participants" name="participants[]" multiple required>
                            <?php while ($member = $team_members_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <?php if ($member['id'] != $_SESSION['user_id']): ?>
                                    <option value="<?= $member['id'] ?>" <?= isset($_POST['participants']) && in_array($member['id'], $_POST['participants']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($member['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </select>
                        <div class="form-text">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs participants.</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=visit&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Créer la visite
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
                <p>Une fois la visite créée, les participants recevront une notification par email et dans l'application.</p>
                <p>Vous pourrez modifier les détails de la visite ultérieurement.</p>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Disponibilités</h5>
            </div>
            <div class="card-body">
                <p>Vérifiez les disponibilités de votre équipe avant de planifier une visite.</p>
                <div class="d-grid">
                    <a href="index.php?controller=calendar&action=date&date=<?= $selectedDate ?>" class="btn btn-outline-primary">
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
    const form = document.getElementById('createVisitForm');
    
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


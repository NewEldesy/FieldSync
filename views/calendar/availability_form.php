<?php
// Titre de la page
$pageTitle = isset($availability->id) ? "Modifier une disponibilité" : "Ajouter une disponibilité";

include 'views/layout/header.php';

// Déterminer si c'est une création ou une modification
$isEdit = isset($availability->id);

// Récupérer la date depuis l'URL ou utiliser la date actuelle
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Si c'est une modification, récupérer les valeurs existantes
if ($isEdit) {
    $availabilityDate = $availability->date;
    $startTime = $availability->start_time;
    $endTime = $availability->end_time;
    $status = $availability->status;
} else {
    $availabilityDate = $selectedDate;
    $startTime = '09:00';
    $endTime = '17:00';
    $status = 'available';
}
?>

<div class="row mb-4">
    <div class="col">
        <h1>
            <i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i> <?= $isEdit ? 'Modifier' : 'Ajouter' ?> une disponibilité
        </h1>
        <p class="text-muted">Indiquez vos disponibilités pour la journée du <?= formatDateFr($availabilityDate) ?>.</p>
    </div>
    <div class="col-auto">
        <a href="index.php?controller=calendar&action=date&date=<?= $availabilityDate ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour au détail de la journée
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><?= $isEdit ? 'Modifier' : 'Ajouter' ?> une disponibilité pour le <?= formatDateFr($availabilityDate) ?></h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=calendar&action=availability<?= $isEdit ? '&id=' . $availability->id : '' ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= $availabilityDate ?>" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Heure de début</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="<?= $startTime ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">Heure de fin</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="<?= $endTime ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Statut</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_available" value="available" <?= $status == 'available' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_available">
                                    <span class="badge bg-success me-1"><i class="fas fa-check"></i></span> Disponible
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_busy" value="busy" <?= $status == 'busy' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_busy">
                                    <span class="badge bg-warning me-1"><i class="fas fa-clock"></i></span> Occupé
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_away" value="away" <?= $status == 'away' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_away">
                                    <span class="badge bg-secondary me-1"><i class="fas fa-plane"></i></span> Absent
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=calendar&action=date&date=<?= $availabilityDate ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Les disponibilités vous permettent d'indiquer à votre équipe quand vous êtes disponible pour des visites de terrain.</li>
                    <li>Vous pouvez définir plusieurs plages horaires pour une même journée.</li>
                    <li>Les statuts disponibles sont :
                        <ul>
                            <li><span class="badge bg-success"><i class="fas fa-check"></i></span> <strong>Disponible</strong> : Vous êtes disponible pour des visites de terrain.</li>
                            <li><span class="badge bg-warning"><i class="fas fa-clock"></i></span> <strong>Occupé</strong> : Vous êtes au travail mais non disponible pour des visites de terrain.</li>
                            <li><span class="badge bg-secondary"><i class="fas fa-plane"></i></span> <strong>Absent</strong> : Vous êtes absent (congés, maladie, etc.).</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(event) {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (startTime >= endTime) {
            event.preventDefault();
            alert('L\'heure de fin doit être postérieure à l\'heure de début.');
        }
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>

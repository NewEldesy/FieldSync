<?php
// Titre de la page
$pageTitle = "Calendrier";

include 'views/layout/header.php';

// Fonction pour générer le calendrier
function generateCalendar($month, $year, $visits, $availabilities) {
    // Premier jour du mois
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    
    // Nombre de jours dans le mois
    $numberDays = date('t', $firstDayOfMonth);
    
    // Jour de la semaine du premier jour du mois (0 = dimanche, 6 = samedi)
    $dayOfWeek = date('w', $firstDayOfMonth);
    
    // Ajuster pour commencer la semaine le lundi (0 = lundi, 6 = dimanche)
    $dayOfWeek = ($dayOfWeek == 0) ? 6 : $dayOfWeek - 1;
    
    // Tableau des jours de la semaine
    $daysOfWeek = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    
    // Tableau des mois
    $months = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];
    
    // Mois précédent et suivant
    $prevMonth = ($month == 1) ? 12 : $month - 1;
    $prevYear = ($month == 1) ? $year - 1 : $year;
    $nextMonth = ($month == 12) ? 1 : $month + 1;
    $nextYear = ($month == 12) ? $year + 1 : $year;
    
    // Aujourd'hui
    $today = date('Y-m-d');
    
    // Début du calendrier
    $calendar = '<div class="calendar-header d-flex justify-content-between align-items-center mb-4">';
    $calendar .= '<div>';
    $calendar .= '<h2 class="mb-0">' . $months[$month] . ' ' . $year . '</h2>';
    $calendar .= '</div>';
    $calendar .= '<div class="btn-group">';
    $calendar .= '<a href="index.php?controller=calendar&action=index&month=' . $prevMonth . '&year=' . $prevYear . '" class="btn btn-outline-primary"><i class="fas fa-chevron-left"></i></a>';
    $calendar .= '<a href="index.php?controller=calendar&action=index&month=' . date('n') . '&year=' . date('Y') . '" class="btn btn-outline-primary">Aujourd\'hui</a>';
    $calendar .= '<a href="index.php?controller=calendar&action=index&month=' . $nextMonth . '&year=' . $nextYear . '" class="btn btn-outline-primary"><i class="fas fa-chevron-right"></i></a>';
    $calendar .= '</div>';
    $calendar .= '</div>';
    
    // Tableau du calendrier
    $calendar .= '<div class="table-responsive">';
    $calendar .= '<table class="table table-bordered calendar-table">';
    $calendar .= '<thead>';
    $calendar .= '<tr>';
    
    // En-têtes des jours de la semaine
    foreach ($daysOfWeek as $day) {
        $calendar .= '<th>' . $day . '</th>';
    }
    
    $calendar .= '</tr>';
    $calendar .= '</thead>';
    $calendar .= '<tbody>';
    
    // Initialiser le compteur de jours
    $currentDay = 1;
    $calendar .= '<tr>';
    
    // Cellules vides pour les jours avant le début du mois
    if ($dayOfWeek > 0) {
        $calendar .= '<td colspan="' . $dayOfWeek . '" class="text-muted"></td>';
    }
    
    // Jours du mois
    while ($currentDay <= $numberDays) {
        // Si on est au début d'une nouvelle semaine, ouvrir une nouvelle ligne
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= '</tr><tr>';
        }
        
        // Formater la date pour la comparaison
        $date = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        
        // Vérifier si c'est aujourd'hui
        $isToday = ($date == $today);
        
        // Vérifier s'il y a des visites pour cette date
        $hasVisits = isset($visits[$date]) && count($visits[$date]) > 0;
        
        // Vérifier s'il y a des disponibilités pour cette date
        $hasAvailabilities = isset($availabilities[$date]) && count($availabilities[$date]) > 0;
        
        // Classes CSS pour la cellule
        $cellClass = '';
        if ($isToday) {
            $cellClass .= ' today';
        }
        if ($hasVisits) {
            $cellClass .= ' has-visits';
        }
        if ($hasAvailabilities) {
            $cellClass .= ' has-availabilities';
        }
        
        // Lien vers la vue détaillée de la date
        $calendar .= '<td class="calendar-day' . $cellClass . '">';
        $calendar .= '<a href="index.php?controller=calendar&action=date&date=' . $date . '" class="day-number' . ($isToday ? ' today-number' : '') . '">' . $currentDay . '</a>';
        
        // Afficher les indicateurs de visites et disponibilités
        if ($hasVisits || $hasAvailabilities) {
            $calendar .= '<div class="day-indicators">';
            
            if ($hasVisits) {
                $visitCount = count($visits[$date]);
                $calendar .= '<span class="badge bg-primary" title="' . $visitCount . ' visite(s)">' . $visitCount . ' <i class="fas fa-map-marker-alt"></i></span> ';
            }
            
            if ($hasAvailabilities) {
                $availabilityStatus = $availabilities[$date][0]['status']; // Prendre le statut de la première disponibilité
                $statusClass = '';
                $statusIcon = '';
                
                switch ($availabilityStatus) {
                    case 'available':
                        $statusClass = 'bg-success';
                        $statusIcon = 'check';
                        break;
                    case 'busy':
                        $statusClass = 'bg-warning';
                        $statusIcon = 'clock';
                        break;
                    case 'away':
                        $statusClass = 'bg-secondary';
                        $statusIcon = 'plane';
                        break;
                }
                
                $calendar .= '<span class="badge ' . $statusClass . '" title="Vous êtes ' . $availabilityStatus . '"><i class="fas fa-' . $statusIcon . '"></i></span>';
            }
            
            $calendar .= '</div>';
        }
        
        $calendar .= '</td>';
        
        // Incrémenter les compteurs
        $currentDay++;
        $dayOfWeek++;
    }
    
    // Cellules vides pour les jours après la fin du mois
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        $calendar .= '<td colspan="' . $remainingDays . '" class="text-muted"></td>';
    }
    
    $calendar .= '</tr>';
    $calendar .= '</tbody>';
    $calendar .= '</table>';
    $calendar .= '</div>';
    
    return $calendar;
}
?>

<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-calendar-alt"></i> Calendrier</h1>
        <p class="text-muted">Gérez vos disponibilités et consultez les visites planifiées.</p>
    </div>
    <div class="col-auto">
        <a href="index.php?controller=calendar&action=availability" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter une disponibilité
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-9">
        <div class="card shadow">
            <div class="card-body">
                <?php
                // Afficher le calendrier
                echo generateCalendar($month, $year, $visits, $availabilities);
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
            </div>
            <div class="card-body">
                <form action="index.php" method="get">
                    <input type="hidden" name="controller" value="calendar">
                    <input type="hidden" name="action" value="index">
                    <input type="hidden" name="month" value="<?= $month ?>">
                    <input type="hidden" name="year" value="<?= $year ?>">
                    
                    <div class="mb-3">
                        <label for="team_member" class="form-label">Membre de l'équipe</label>
                        <select class="form-select" id="team_member" name="team_member">
                            <option value="all">Tous les membres</option>
                            <?php while ($member = $team_members_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $member['id'] ?>" <?= isset($_GET['team_member']) && $_GET['team_member'] == $member['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($member['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="visit_status" class="form-label">Statut des visites</label>
                        <select class="form-select" id="visit_status" name="visit_status">
                            <option value="all">Tous les statuts</option>
                            <option value="confirmed" <?= isset($_GET['visit_status']) && $_GET['visit_status'] == 'confirmed' ? 'selected' : '' ?>>Confirmées</option>
                            <option value="pending" <?= isset($_GET['visit_status']) && $_GET['visit_status'] == 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="cancelled" <?= isset($_GET['visit_status']) && $_GET['visit_status'] == 'cancelled' ? 'selected' : '' ?>>Annulées</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Appliquer les filtres
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Légende</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center">
                        <span class="badge bg-primary me-2"><i class="fas fa-map-marker-alt"></i></span>
                        <span>Visite planifiée</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <span class="badge bg-success me-2"><i class="fas fa-check"></i></span>
                        <span>Disponible</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <span class="badge bg-warning me-2"><i class="fas fa-clock"></i></span>
                        <span>Occupé</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <span class="badge bg-secondary me-2"><i class="fas fa-plane"></i></span>
                        <span>Absent</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-table {
    table-layout: fixed;
}

.calendar-table th {
    text-align: center;
    background-color: #f8f9fa;
}

.calendar-day {
    height: 100px;
    padding: 5px !important;
    position: relative;
    vertical-align: top;
}

.day-number {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    border-radius: 50%;
    text-decoration: none;
    color: #333;
}

.today {
    background-color: rgba(13, 110, 253, 0.05);
}

.today-number {
    background-color: #0d6efd;
    color: white;
}

.day-indicators {
    margin-top: 5px;
}

.has-visits {
    border-left: 3px solid #0d6efd;
}

.has-availabilities.has-visits {
    border-left: 3px solid #0d6efd;
    border-right: 3px solid #198754;
}

.has-availabilities:not(.has-visits) {
    border-right: 3px solid #198754;
}
</style>

<?php include 'views/layout/footer.php'; ?>


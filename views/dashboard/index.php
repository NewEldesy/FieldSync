<?php include 'views/layout/header.php'; ?>

<?php
/* ── Chart data : visites des 7 prochains jours ── */
$weekly_data = [];
for ($i = 0; $i < 7; $i++) {
    $weekly_data[date('Y-m-d', strtotime("+$i days"))] = 0;
}
$upcoming_visits->execute();
while ($v = $upcoming_visits->fetch(PDO::FETCH_ASSOC)) {
    if (isset($weekly_data[$v['date']])) {
        $weekly_data[$v['date']]++;
    }
}
$chart_labels = [];
foreach (array_keys($weekly_data) as $d) {
    $days_fr = ['Mon'=>'Lun','Tue'=>'Mar','Wed'=>'Mer','Thu'=>'Jeu','Fri'=>'Ven','Sat'=>'Sam','Sun'=>'Dim'];
    $en = date('D', strtotime($d));
    $chart_labels[] = ($days_fr[$en] ?? $en) . ' ' . date('d/m', strtotime($d));
}
$chart_values = array_values($weekly_data);
$upcoming_visits->execute(); /* reset pour le reste de la vue */
?>

<!-- ── Titre ── -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">Tableau de bord</h4>
        <p class="text-muted small mb-0">Bienvenue, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></p>
    </div>
    <a href="index.php?controller=visit&action=create" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Nouvelle visite
    </a>
</div>

<!-- ── Stat cards ── -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card stat-card stat-card-navy">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon"><i class="fas fa-map-marker-alt fa-lg text-white"></i></div>
                <div>
                    <div class="stat-number"><?= $upcoming_visits->rowCount() ?></div>
                    <div class="text-muted small">Visites à venir</div>
                    <a href="index.php?controller=visit&action=index" class="btn btn-sm btn-primary mt-2">Voir</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card stat-card stat-card-steel">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon"><i class="fas fa-users fa-lg text-white"></i></div>
                <div>
                    <div class="stat-number"><?= $team_members->rowCount() ?></div>
                    <div class="text-muted small">Membres équipe</div>
                    <a href="index.php?controller=calendar&action=index" class="btn btn-sm btn-primary mt-2">Voir</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card stat-card stat-card-muted">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon"><i class="fas fa-bell fa-lg text-white"></i></div>
                <div>
                    <div class="stat-number"><?= $unread_notifications->rowCount() ?></div>
                    <div class="text-muted small">Notifs non lues</div>
                    <a href="index.php?controller=notification&action=index" class="btn btn-sm btn-primary mt-2">Voir</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div class="row g-3">

    <!-- Graphique visites -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line me-2 text-muted"></i>Visites sur 7 jours</span>
            </div>
            <div class="card-body">
                <canvas id="weekChart" height="80"></canvas>
            </div>
        </div>

        <!-- Planning onglets -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="visitTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#today" type="button">Aujourd'hui</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tomorrow" type="button">Demain</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#week" type="button">Cette semaine</button></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Aujourd'hui -->
                    <div class="tab-pane fade show active" id="today">
                        <?php
                        $today = date('Y-m-d');
                        $has_today = false;
                        $upcoming_visits->execute();
                        while ($row = $upcoming_visits->fetch(PDO::FETCH_ASSOC)):
                            if ($row['date'] !== $today) continue;
                            $has_today = true;
                        ?>
                        <div class="card mb-2">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                                    <span class="badge bg-<?= $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                        <?= $row['status'] == 'confirmed' ? 'Confirmé' : ($row['status'] == 'pending' ? 'En attente' : 'Annulé') ?>
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-clock me-1"></i><?= $row['start_time'] ?> – <?= $row['end_time'] ?>
                                    &nbsp;<i class="fas fa-map-marker-alt ms-2 me-1"></i><?= htmlspecialchars($row['location']) ?>
                                </div>
                                <a href="index.php?controller=visit&action=view&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary mt-1">Détails</a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if (!$has_today): ?><div class="text-center py-4 text-muted small">Aucune visite aujourd'hui</div><?php endif; ?>
                    </div>

                    <!-- Demain -->
                    <div class="tab-pane fade" id="tomorrow">
                        <?php
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        $has_tomorrow = false;
                        $upcoming_visits->execute();
                        while ($row = $upcoming_visits->fetch(PDO::FETCH_ASSOC)):
                            if ($row['date'] !== $tomorrow) continue;
                            $has_tomorrow = true;
                        ?>
                        <div class="card mb-2">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                                    <span class="badge bg-<?= $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                        <?= $row['status'] == 'confirmed' ? 'Confirmé' : ($row['status'] == 'pending' ? 'En attente' : 'Annulé') ?>
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-clock me-1"></i><?= $row['start_time'] ?> – <?= $row['end_time'] ?>
                                    &nbsp;<i class="fas fa-map-marker-alt ms-2 me-1"></i><?= htmlspecialchars($row['location']) ?>
                                </div>
                                <a href="index.php?controller=visit&action=view&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary mt-1">Détails</a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if (!$has_tomorrow): ?><div class="text-center py-4 text-muted small">Aucune visite demain</div><?php endif; ?>
                    </div>

                    <!-- Semaine -->
                    <div class="tab-pane fade" id="week">
                        <?php
                        $has_week = false;
                        $upcoming_visits->execute();
                        while ($row = $upcoming_visits->fetch(PDO::FETCH_ASSOC)):
                            $has_week = true;
                        ?>
                        <div class="card mb-2">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                                    <span class="badge bg-<?= $row['status'] == 'confirmed' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                        <?= $row['status'] == 'confirmed' ? 'Confirmé' : ($row['status'] == 'pending' ? 'En attente' : 'Annulé') ?>
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i><?= formatDateFr($row['date']) ?>
                                    &nbsp;<i class="fas fa-clock ms-2 me-1"></i><?= $row['start_time'] ?> – <?= $row['end_time'] ?>
                                </div>
                                <a href="index.php?controller=visit&action=view&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary mt-1">Détails</a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if (!$has_week): ?><div class="text-center py-4 text-muted small">Aucune visite cette semaine</div><?php endif; ?>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <a href="index.php?controller=visit&action=create" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Planifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar droite -->
    <div class="col-lg-4">

        <!-- Disponibilité équipe -->
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-users me-2 text-muted"></i>Équipe</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php while ($member = $team_members->fetch(PDO::FETCH_ASSOC)):
                        $st = rand(0,2);
                        $sc = $st == 0 ? 'success' : ($st == 1 ? 'warning' : 'secondary');
                        $stxt = $st == 0 ? 'Disponible' : ($st == 1 ? 'Occupé' : 'Absent');
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-semibold small"><?= htmlspecialchars($member['name']) ?></div>
                            <div class="text-muted" style="font-size:.72rem"><?= htmlspecialchars($member['role'] ?? 'Membre') ?></div>
                        </div>
                        <span class="badge bg-<?= $sc ?> rounded-pill"><?= $stxt ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="index.php?controller=calendar&action=availability" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-clock me-1"></i>Ma disponibilité
                </a>
            </div>
        </div>

        <!-- Notifications récentes -->
        <div class="card">
            <div class="card-header"><i class="fas fa-bell me-2 text-muted"></i>Notifications récentes</div>
            <div class="card-body p-0">
                <?php
                $has_notifs = false;
                while ($notif = $all_notifications->fetch(PDO::FETCH_ASSOC)):
                    $has_notifs = true;
                ?>
                <div class="border-bottom px-3 py-2 <?= $notif['is_read'] ? '' : 'notif-unread' ?>">
                    <div class="fw-semibold small"><?= htmlspecialchars($notif['title']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($notif['message']) ?></div>
                    <div class="text-muted" style="font-size:.72rem"><?= date('d/m H:i', strtotime($notif['created_at'])) ?></div>
                </div>
                <?php endwhile; ?>
                <?php if (!$has_notifs): ?>
                <div class="text-center py-4 text-muted small">Aucune notification</div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="index.php?controller=notification&action=index" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-bell me-1"></i>Tout voir
                </a>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    var ctx = document.getElementById('weekChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Visites',
                data: <?= json_encode($chart_values) ?>,
                borderColor: '#1F3B5C',
                backgroundColor: 'rgba(31,59,92,.1)',
                fill: true,
                tension: .4,
                pointBackgroundColor: '#1F3B5C',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(c) { return c.parsed.y + ' visite(s)'; } } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(167,177,190,.2)' } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>

<?php include 'views/layout/footer.php'; ?>

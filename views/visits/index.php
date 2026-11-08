<?php
$pageTitle = "Visites";
include 'views/layout/header.php';

/* ── Collect all visits for Kanban ── */
$kanban = ['pending' => [], 'confirmed' => [], 'cancelled' => []];
$visits_stmt->execute();
while ($v = $visits_stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = $v['status'] ?? 'pending';
    if (!isset($kanban[$status])) $status = 'pending';
    $kanban[$status][] = $v;
}
$visits_stmt->execute(); /* reset for list view */
?>

<!-- ── Header ── -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">Gestion des visites</h4>
        <p class="text-muted small mb-0">Consultez et gérez toutes vos visites de terrain.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Toggle vue -->
        <div class="btn-group view-toggle" role="group" id="viewToggle">
            <button type="button" class="btn btn-outline-secondary active" id="btnList" title="Vue liste">
                <i class="fas fa-list"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btnKanban" title="Vue Kanban">
                <i class="fas fa-columns"></i>
            </button>
        </div>
        <a href="index.php?controller=visit&action=create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Planifier
        </a>
    </div>
</div>

<!-- ══════════════════════════════════
     VUE LISTE
══════════════════════════════════ -->
<div id="viewList">
    <div class="card mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="visitTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">À venir</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#past" type="button">Passées</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#all" type="button">Toutes</button></li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content">
                <?php
                $tabs = [
                    'upcoming' => ['label' => 'À venir', 'filter' => fn($v) => strtotime($v['date']) >= strtotime(date('Y-m-d'))],
                    'past'     => ['label' => 'Passées',  'filter' => fn($v) => strtotime($v['date']) < strtotime(date('Y-m-d'))],
                    'all'      => ['label' => 'Toutes',   'filter' => fn($v) => true],
                ];
                $first = true;
                foreach ($tabs as $tabId => $tab):
                    $visits_stmt->execute();
                ?>
                <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= $tabId ?>">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Date</th>
                                    <th>Horaire</th>
                                    <th>Lieu</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $hasRows = false;
                            while ($visit = $visits_stmt->fetch(PDO::FETCH_ASSOC)):
                                if (!($tab['filter'])($visit)) continue;
                                $hasRows = true;
                            ?>
                                <tr>
                                    <td class="fw-semibold small"><?= htmlspecialchars($visit['title']) ?></td>
                                    <td class="small"><?= formatDateFr($visit['date']) ?></td>
                                    <td class="small"><?= substr($visit['start_time'],0,5) ?> – <?= substr($visit['end_time'],0,5) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($visit['location']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $visit['status']=='confirmed'?'success':($visit['status']=='pending'?'warning':'danger') ?>">
                                            <?= $visit['status']=='confirmed'?'Confirmée':($visit['status']=='pending'?'En attente':'Annulée') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?controller=visit&action=view&id=<?= $visit['id'] ?>" class="btn btn-outline-primary" title="Voir"><i class="fas fa-eye"></i></a>
                                            <?php if ($visit['created_by'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                                            <a href="index.php?controller=visit&action=edit&id=<?= $visit['id'] ?>" class="btn btn-outline-secondary" title="Modifier"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-outline-danger delete-visit" data-id="<?= $visit['id'] ?>" title="Supprimer"><i class="fas fa-trash"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (!$hasRows): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Aucune visite.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php $first = false; endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card">
        <div class="card-header"><i class="fas fa-filter me-2 text-muted"></i>Filtres</div>
        <div class="card-body">
            <form action="index.php" method="get" class="row g-2">
                <input type="hidden" name="controller" value="visit">
                <input type="hidden" name="action" value="index">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Titre, lieu..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status']=='confirmed')?'selected':'' ?>>Confirmées</option>
                        <option value="pending"   <?= (isset($_GET['status']) && $_GET['status']=='pending')?'selected':'' ?>>En attente</option>
                        <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status']=='cancelled')?'selected':'' ?>>Annulées</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="date_range">
                        <option value="">Toutes les dates</option>
                        <option value="today"    <?= (isset($_GET['date_range']) && $_GET['date_range']=='today')?'selected':'' ?>>Aujourd'hui</option>
                        <option value="tomorrow" <?= (isset($_GET['date_range']) && $_GET['date_range']=='tomorrow')?'selected':'' ?>>Demain</option>
                        <option value="week"     <?= (isset($_GET['date_range']) && $_GET['date_range']=='week')?'selected':'' ?>>Cette semaine</option>
                        <option value="month"    <?= (isset($_GET['date_range']) && $_GET['date_range']=='month')?'selected':'' ?>>Ce mois</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i>Filtrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     VUE KANBAN
══════════════════════════════════ -->
<div id="viewKanban" style="display:none">
    <div class="kanban-board">

        <!-- Colonne : En attente -->
        <div class="kanban-col">
            <div class="kanban-col-header">
                <span class="kanban-col-title" style="color:#d97706"><i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle"></i>En attente</span>
                <span class="kanban-col-count"><?= count($kanban['pending']) ?></span>
            </div>
            <?php foreach ($kanban['pending'] as $v): ?>
            <div class="kanban-card pending">
                <div class="kanban-card-title"><?= htmlspecialchars($v['title']) ?></div>
                <div class="kanban-card-meta mb-1">
                    <i class="fas fa-calendar-alt"></i> <?= formatDateFr($v['date']) ?>
                </div>
                <div class="kanban-card-meta mb-2">
                    <i class="fas fa-clock"></i> <?= substr($v['start_time'],0,5) ?> – <?= substr($v['end_time'],0,5) ?>
                </div>
                <div class="kanban-card-meta"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($v['location']) ?></div>
                <div class="mt-2">
                    <a href="index.php?controller=visit&action=view&id=<?= $v['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                    <?php if ($v['created_by'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="index.php?controller=visit&action=edit&id=<?= $v['id'] ?>" class="btn btn-outline-secondary btn-sm">Éditer</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($kanban['pending'])): ?>
            <div class="text-center py-4 text-muted small">Aucune visite en attente</div>
            <?php endif; ?>
            <a href="index.php?controller=visit&action=create" class="kanban-add-btn">
                <i class="fas fa-plus"></i> Ajouter
            </a>
        </div>

        <!-- Colonne : Confirmées -->
        <div class="kanban-col">
            <div class="kanban-col-header">
                <span class="kanban-col-title" style="color:#198754"><i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle"></i>Confirmées</span>
                <span class="kanban-col-count"><?= count($kanban['confirmed']) ?></span>
            </div>
            <?php foreach ($kanban['confirmed'] as $v): ?>
            <div class="kanban-card confirmed">
                <div class="kanban-card-title"><?= htmlspecialchars($v['title']) ?></div>
                <div class="kanban-card-meta mb-1">
                    <i class="fas fa-calendar-alt"></i> <?= formatDateFr($v['date']) ?>
                </div>
                <div class="kanban-card-meta mb-2">
                    <i class="fas fa-clock"></i> <?= substr($v['start_time'],0,5) ?> – <?= substr($v['end_time'],0,5) ?>
                </div>
                <div class="kanban-card-meta"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($v['location']) ?></div>
                <div class="mt-2">
                    <a href="index.php?controller=visit&action=view&id=<?= $v['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                    <?php if ($v['created_by'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="index.php?controller=visit&action=edit&id=<?= $v['id'] ?>" class="btn btn-outline-secondary btn-sm">Éditer</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($kanban['confirmed'])): ?>
            <div class="text-center py-4 text-muted small">Aucune visite confirmée</div>
            <?php endif; ?>
        </div>

        <!-- Colonne : Annulées -->
        <div class="kanban-col">
            <div class="kanban-col-header">
                <span class="kanban-col-title" style="color:var(--color-muted)"><i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle"></i>Annulées</span>
                <span class="kanban-col-count"><?= count($kanban['cancelled']) ?></span>
            </div>
            <?php foreach ($kanban['cancelled'] as $v): ?>
            <div class="kanban-card cancelled">
                <div class="kanban-card-title"><?= htmlspecialchars($v['title']) ?></div>
                <div class="kanban-card-meta mb-1">
                    <i class="fas fa-calendar-alt"></i> <?= formatDateFr($v['date']) ?>
                </div>
                <div class="kanban-card-meta mb-2">
                    <i class="fas fa-clock"></i> <?= substr($v['start_time'],0,5) ?> – <?= substr($v['end_time'],0,5) ?>
                </div>
                <div class="kanban-card-meta"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($v['location']) ?></div>
                <div class="mt-2">
                    <a href="index.php?controller=visit&action=view&id=<?= $v['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($kanban['cancelled'])): ?>
            <div class="text-center py-4 text-muted small">Aucune visite annulée</div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Modal suppression -->
<div class="modal fade" id="deleteVisitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Êtes-vous sûr de vouloir supprimer cette visite ? Cette action est irréversible.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDelete" class="btn btn-danger btn-sm">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var btnList   = document.getElementById('btnList');
    var btnKanban = document.getElementById('btnKanban');
    var viewList  = document.getElementById('viewList');
    var viewKanban= document.getElementById('viewKanban');
    var STORAGE   = 'fs_visit_view';

    function showList() {
        viewList.style.display = '';
        viewKanban.style.display = 'none';
        btnList.classList.add('active');
        btnKanban.classList.remove('active');
        localStorage.setItem(STORAGE, 'list');
    }
    function showKanban() {
        viewList.style.display = 'none';
        viewKanban.style.display = '';
        btnKanban.classList.add('active');
        btnList.classList.remove('active');
        localStorage.setItem(STORAGE, 'kanban');
    }

    btnList.addEventListener('click', showList);
    btnKanban.addEventListener('click', showKanban);

    /* Restore saved preference */
    if (localStorage.getItem(STORAGE) === 'kanban') showKanban();

    /* Delete modal */
    document.querySelectorAll('.delete-visit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('confirmDelete').href =
                'index.php?controller=visit&action=delete&id=' + this.getAttribute('data-id');
            new bootstrap.Modal(document.getElementById('deleteVisitModal')).show();
        });
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>

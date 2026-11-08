<?php include 'views/layout/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-shield-alt me-2 text-danger"></i>Administration</h4>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold"><?= $stats['users']['total'] ?></div>
                            <div class="text-muted small">Utilisateurs</div>
                            <div class="text-muted" style="font-size:0.75rem">
                                <?= $stats['users']['admins'] ?> admin &bull;
                                <?= $stats['users']['managers'] ?> manager &bull;
                                <?= $stats['users']['users'] ?> user
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-calendar-check fa-lg text-success"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold"><?= $stats['visits']['total'] ?></div>
                            <div class="text-muted small">Visites</div>
                            <div class="text-muted" style="font-size:0.75rem">
                                <?= $stats['visits']['confirmed'] ?> confirmées &bull;
                                <?= $stats['visits']['pending'] ?> en attente &bull;
                                <?= $stats['visits']['cancelled'] ?> annulées
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-sms fa-lg text-warning"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold"><?= $stats['sms']['total'] ?></div>
                            <div class="text-muted small">SMS total</div>
                            <div class="text-muted" style="font-size:0.75rem">
                                <?= $stats['sms']['sent'] ?> envoyés &bull;
                                <span class="text-danger"><?= $stats['sms']['failed'] ?> échoués</span> &bull;
                                <?= $stats['sms']['pending'] ?> en attente
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-history me-2 text-secondary"></i>Journal d'activité récent
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light"><tr><th>Utilisateur</th><th>Action</th><th>Entité</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php while ($row = $activity_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['user_name'] ?? 'Supprimé') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['action']) ?></span></td>
                                <td><?= htmlspecialchars($row['entity_type']) ?> #<?= (int)$row['entity_id'] ?></td>
                                <td class="text-muted small"><?= date('d/m H:i', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-cogs me-2 text-secondary"></i>Accès rapides
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php?controller=admin&action=users" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fas fa-user-cog text-primary"></i> Gérer les utilisateurs et rôles
                    </a>
                    <a href="index.php?controller=admin&action=settings" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h text-success"></i> Paramètres système
                    </a>
                    <a href="index.php?controller=sms&action=settings" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                        <i class="fas fa-sms text-warning"></i> Configuration SMS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

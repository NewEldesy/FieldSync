<?php include 'views/layout/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-user-cog me-2 text-primary"></i>Gestion des utilisateurs</h4>
        <a href="index.php?controller=admin&action=index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Admin
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle actuel</th>
                        <th>Changer le rôle</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $users_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td class="text-muted"><?= (int)$row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '/') ?></td>
                        <td>
                            <?php
                            $badge = ['admin' => 'danger', 'manager' => 'warning', 'user' => 'secondary'];
                            $b = $badge[$row['role']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $b ?>"><?= htmlspecialchars($row['role']) ?></span>
                        </td>
                        <td>
                            <?php if ((int)$row['id'] !== (int)$_SESSION['user_id']): ?>
                            <form method="POST" action="index.php?controller=admin&action=users" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="user_id"    value="<?= (int)$row['id'] ?>">
                                <select name="role" class="form-select form-select-sm" style="width:120px">
                                    <option value="admin"   <?= $row['role']==='admin'   ? 'selected':'' ?>>admin</option>
                                    <option value="manager" <?= $row['role']==='manager' ? 'selected':'' ?>>manager</option>
                                    <option value="user"    <?= $row['role']==='user'    ? 'selected':'' ?>>user</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">Votre compte</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

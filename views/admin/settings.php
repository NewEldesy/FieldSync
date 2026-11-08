<?php include 'views/layout/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-sliders-h me-2 text-success"></i>Paramètres système</h4>
        <a href="index.php?controller=admin&action=index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Admin
        </a>
    </div>

    <?php displayFlashMessages(); ?>

    <div class="row g-3">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Paramètres modifiables</div>
                <div class="card-body">
                    <form method="POST" action="index.php?controller=admin&action=settings">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                        <?php
                        $rows = [];
                        while ($r = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
                            $rows[$r['setting_key']] = $r;
                        }
                        $editable = ['app_name', 'sms_provider', 'daily_summary_time', 'max_file_size'];
                        ?>

                        <?php foreach ($editable as $key): ?>
                        <?php $r = $rows[$key] ?? ['setting_value'=>'', 'description'=>'']; ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="<?= $key ?>">
                                <?= htmlspecialchars($key) ?>
                            </label>
                            <?php if ($key === 'sms_provider'): ?>
                            <select name="<?= $key ?>" id="<?= $key ?>" class="form-select">
                                <option value="twilio"   <?= $r['setting_value']==='twilio'   ? 'selected':'' ?>>twilio</option>
                                <option value="textbelt" <?= $r['setting_value']==='textbelt' ? 'selected':'' ?>>textbelt</option>
                            </select>
                            <?php else: ?>
                            <input type="text" class="form-control" name="<?= $key ?>" id="<?= $key ?>"
                                   value="<?= htmlspecialchars($r['setting_value']) ?>">
                            <?php endif; ?>
                            <?php if (!empty($r['description'])): ?>
                            <div class="form-text text-muted"><?= htmlspecialchars($r['description']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'views/layout/footer.php'; ?>

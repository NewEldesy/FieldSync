<?php
// This is a partial form, expected to be included within another form tag.
// Variables like $team (for editing) and $form_action_url, $submit_button_text should be set by the parent view.

// Example default values if not set by parent (useful for direct rendering if needed, though not typical for partials)
$team_name_value = isset($team) ? htmlspecialchars($team->team_name ?? '') : '';
$description_value = isset($team) ? htmlspecialchars($team->description ?? '') : '';
$submit_button_text = $submit_button_text ?? 'Submit';

// CSRF token - assuming a function generateCsrfToken() exists as per controller patterns
// if (function_exists('generateCsrfToken')) {
//     $csrf_token = generateCsrfToken();
// } else {
//     $csrf_token = ''; // Fallback or error
// }
?>

<?php /* Example of CSRF token field if used:
<input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
*/ ?>

<div class="mb-3">
    <label for="team_name" class="form-label">Team Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="team_name" name="team_name" value="<?= $team_name_value ?>" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3"><?= $description_value ?></textarea>
</div>

<button type="submit" class="btn btn-primary">
    <i class="fas fa-save"></i> <?= htmlspecialchars($submit_button_text) ?>
</button>
<a href="index.php?controller=team&action=index" class="btn btn-secondary">
    <i class="fas fa-times"></i> Cancel
</a>

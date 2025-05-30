<?php
// This is a partial for a Bootstrap modal.
// Expected to be included in a view that defines $team and $users_not_in_team (or similar for users list).
// $team is the current team object (to get $team->team_id for the form action).
// $users_not_in_team is an array of user objects/arrays to populate the dropdown.
?>

<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberModalLabel"><i class="fas fa-user-plus"></i> Add Member to <?= htmlspecialchars($team->team_name ?? 'Team') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?controller=team&action=addMember&team_id=<?= htmlspecialchars($team->team_id ?? '') ?>" method="POST">
                <?php /* Example of CSRF token field if used:
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                */ ?>
                <div class="modal-body">
                    <?php if (isset($team) && $team->team_id): ?>
                        <input type="hidden" name="team_id" value="<?= htmlspecialchars($team->team_id) ?>">
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select a User</option>
                                <?php if (!empty($users_not_in_team)): ?>
                                    <?php foreach ($users_not_in_team as $user): ?>
                                        <option value="<?= htmlspecialchars($user->id ?? $user['id']) ?>">
                                            <?= htmlspecialchars($user->name ?? $user['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No users available to add.</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="role" name="role" value="member" required>
                            <small class="form-text text-muted">E.g., Member, Leader, Contributor.</small>
                        </div>

                    <?php else: ?>
                        <p class="text-danger">Team information is missing. Cannot add a member.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <?php if (isset($team) && $team->team_id): ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Member
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$pageTitle = "Team Details";
if (isset($team) && property_exists($team, 'team_name') && $team->team_name) {
    $pageTitle = "Team: " . htmlspecialchars($team->team_name);
}
include __DIR__ . '/../layout/header.php';

// Placeholder for team data - expected to be passed from controller
// Example:
// $team = (object)['team_id' => 1, 'team_name' => 'Alpha Squad', 'description' => 'The best squad for critical missions.'];
// $members = [
//     (object)['user_id' => 1, 'user_name' => 'John Doe', 'role' => 'Leader', 'joined_at' => '2023-01-15', 'team_member_id' => 101],
//     (object)['user_id' => 2, 'user_name' => 'Jane Smith', 'role' => 'Member', 'joined_at' => '2023-02-20', 'team_member_id' => 102]
// ];
// $users_not_in_team = [ // For the add member modal
//    (object)['user_id' => 3, 'name' => 'Peter Pan'],
//    (object)['user_id' => 4, 'name' => 'Wendy Darling']
// ];
?>

<div class="container mt-4">
    <?php if (isset($team) && $team->team_id): ?>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-users"></i> <?= htmlspecialchars($team->team_name ?? 'Team Details') ?></h1>
                    <div>
                        <a href="index.php?controller=team&action=edit&id=<?= $team->team_id ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit"></i> Edit Team
                        </a>
                        <a href="index.php?controller=team&action=index" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-list"></i> Back to Teams List
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Description:</strong></p>
                <p><?= nl2br(htmlspecialchars($team->description ?? 'No description provided.')) ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-user-friends"></i> Team Members</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-user-plus"></i> Add Member
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($members)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Role</th>
                                    <th>Joined Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($member->user_name ?? 'N/A') ?></td> <?php // Assuming user_name is fetched via JOIN ?>
                                        <td><?= htmlspecialchars($member->role ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars(isset($member->joined_at) ? date('Y-m-d', strtotime($member->joined_at)) : 'N/A') ?></td>
                                        <td class="text-center">
                                            <a href="index.php?controller=team&action=removeMember&team_member_id=<?= $member->team_member_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this member?');" title="Remove Member">
                                                <i class="fas fa-user-minus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No members in this team yet.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-danger">
            Team not found.
        </div>
        <a href="index.php?controller=team&action=index" class="btn btn-link">Back to Teams List</a>
    <?php endif; ?>
</div>

<?php 
// Include the modal for adding a member if $team is defined
if (isset($team) && $team->team_id) {
    // The $users_not_in_team variable should be populated by the controller
    // For example: $users_not_in_team = User::getUsersNotInTeam($this->db, $team->team_id);
    include __DIR__ . '/_add_member_modal.php'; 
}
?>

<?php
include __DIR__ . '/../layout/footer.php';
?>

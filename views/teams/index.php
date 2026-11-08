<?php
$pageTitle = "Manage Teams"; // Example page title
include __DIR__ . '/../layout/header.php'; // Adjusted path for consistency
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1><i class="fas fa-users"></i> Team Management</h1>
        </div>
        <div class="col text-end">
            <a href="index.php?controller=team&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Team
            </a>
        </div>
    </div>

    <?php
    // Placeholder for flash messages, if any, usually handled in header.php
    // $flash = getFlashMessage();
    // if ($flash):
    //     echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
    // endif;
    ?>

    <div class="card">
        <div class="card-header">
            All Teams
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Description</th>
                            <th class="text-center">Members</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Placeholder for $teams data. Example:
                        // $teams = [
                        //     ['team_id' => 1, 'team_name' => 'Alpha Team', 'description' => 'The A team', 'member_count' => 5],
                        //     ['team_id' => 2, 'team_name' => 'Bravo Team', 'description' => 'The B team', 'member_count' => 3],
                        // ];
                        if (!empty($teams)): ?>
                            <?php foreach ($teams as $team): ?>
                                <tr>
                                    <td><?= htmlspecialchars($team['team_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($team['description'] ?? 'N/A') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($team['member_count'] ?? 0) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="index.php?controller=team&action=view&id=<?= $team['team_id'] ?>" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controller=team&action=edit&id=<?= $team['team_id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?controller=team&action=delete&id=<?= $team['team_id'] ?>" class="btn btn-outline-danger delete-team-button" data-team-id="<?= $team['team_id'] ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this team?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No teams found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Optional: Modal for delete confirmation if a more sophisticated one is needed
// Could be a generic modal included in footer.php or specific here.
?>

<?php
include __DIR__ . '/../layout/footer.php'; // Adjusted path
?>

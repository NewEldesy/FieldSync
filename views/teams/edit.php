<?php
$pageTitle = "Edit Team"; // Will be more specific if $team object is available
if (isset($team) && property_exists($team, 'team_name') && $team->team_name) {
    $pageTitle = "Edit Team: " . htmlspecialchars($team->team_name);
}
include __DIR__ . '/../layout/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h1><i class="fas fa-edit"></i> Edit Team</h1>
                </div>
                <div class="card-body">
                    <?php if (isset($team) && $team->team_id): // Check if $team object and its ID exist ?>
                        <form action="index.php?controller=team&action=edit&id=<?= htmlspecialchars($team->team_id) ?>" method="POST">
                            <?php
                            // Define variables for the form partial
                            $submit_button_text = "Update Team";
                            // The $team variable is expected to be set by the controller
                            
                            // Include the team form partial
                            include __DIR__ . '/_team_form.php';
                            ?>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            Team data not found. Cannot display edit form.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="index.php?controller=team&action=index" class="btn btn-link">Back to Teams List</a>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . '/../layout/footer.php';
?>

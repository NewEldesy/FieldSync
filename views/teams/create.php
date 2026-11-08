<?php
$pageTitle = "Create New Team";
include __DIR__ . '/../layout/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h1><i class="fas fa-users-cog"></i> Create New Team</h1>
                </div>
                <div class="card-body">
                    <form action="index.php?controller=team&action=create" method="POST">
                        <?php
                        // Define variables for the form partial
                        $submit_button_text = "Create Team";
                        // $team object is not needed for create form, but could be an empty object if form expects it
                        // $team = null; // Or new Team($db_connection_if_model_methods_used_in_form)
                        
                        // Include the team form partial
                        include __DIR__ . '/_team_form.php';
                        ?>
                    </form>
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

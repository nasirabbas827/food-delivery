<?php
include('config.php');

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user details from the database
$sql = "SELECT id, username, email FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $username, $email);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update"])) {
        $newUsername = $_POST["username"];
        $newEmail = $_POST["email"];
        $newPassword = $_POST["password"];

        // Prepare the SQL statement for updating user details
        $update_query = "UPDATE users 
                         SET username = ?, email = ?";

        // Add password update conditionally
        if (!empty($newPassword)) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update_query .= ", password = ?";
        }

        $update_query .= " WHERE id = ?";
        
        $update_stmt = mysqli_prepare($conn, $update_query);

        if (!empty($newPassword)) {
            mysqli_stmt_bind_param($update_stmt, "ssi", $newUsername, $newEmail, $newPasswordHash, $user_id);
        } else {
            mysqli_stmt_bind_param($update_stmt, "ssi", $newUsername, $newEmail, $user_id);
        }

        if (mysqli_stmt_execute($update_stmt)) {
            echo "Profile updated successfully!";
            // Update session data if needed
            $_SESSION["username"] = $newUsername;
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }

        mysqli_stmt_close($update_stmt);
    } elseif (isset($_POST["delete"])) {
        // Delete user account
        $delete_query = "DELETE FROM users WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $user_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            // Log out the user
            session_unset();
            session_destroy();
            header("location: index.php");
            exit;
        } else {
            echo "Error deleting account: " . mysqli_error($conn);
        }

        mysqli_stmt_close($delete_stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Update Profile</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Delete Account</button>
    </form>

    <!-- Modal for Account Deletion Confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete your account? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-danger">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

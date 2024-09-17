<?php
// Include database configuration file
include('config.php');

// Check if the verification code is provided in the URL
if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Prepare an SQL statement to verify the user
    $sql = "SELECT id, status FROM users WHERE verification_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $verification_code);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    // Check if a matching user is found
    if (mysqli_stmt_num_rows($stmt) == 1) {
        // Bind the result variables
        mysqli_stmt_bind_result($stmt, $user_id, $status);
        mysqli_stmt_fetch($stmt);

        // Check if the account is already verified
        if ($status === 'pending') {
            // Update the user's status to 'verified'
            $sql_update = "UPDATE users SET status = 'verified', verification_code = NULL WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "i", $user_id);
            mysqli_stmt_execute($stmt_update);
            
            if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                echo '<div class="alert alert-success" role="alert">Your account has been successfully verified. You can now <a href="login.php">login</a>.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Verification failed. Please try again later.</div>';
            }

            mysqli_stmt_close($stmt_update);
        } else {
            echo '<div class="alert alert-info" role="alert">Your account is already verified. You can <a href="login.php">login</a>.</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Invalid verification code or the account has already been verified.</div>';
    }

    mysqli_stmt_close($stmt);
} else {
    echo '<div class="alert alert-danger" role="alert">No verification code provided.</div>';
}

mysqli_close($conn);
?>

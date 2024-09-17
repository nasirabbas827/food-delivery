<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle review deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM reviews WHERE review_id = $delete_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $message = "Review deleted successfully.";
    } else {
        $message = "Error deleting review: " . mysqli_error($conn);
    }
}

// Fetch all reviews
$reviews_query = "SELECT * FROM reviews";
$reviews_result = mysqli_query($conn, $reviews_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .delete-btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .message {
            margin: 15px 0;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Reviews</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info message" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Review ID</th>
                <th>Restaurant ID</th>
                <th>Customer ID</th>
                <th>Review</th>
                <th>Rating</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($reviews_result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['review_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['restaurant_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['review']); ?></td>
                    <td><?php echo htmlspecialchars($row['rating']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="manage_reviews.php?delete_id=<?php echo $row['review_id']; ?>" class="btn btn-danger btn-sm delete-btn" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

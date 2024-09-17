<?php
include('config.php');

session_start();

// Check if restaurant is logged in, if not, redirect to login page
if (!isset($_SESSION["restaurant_id"]) || empty($_SESSION["restaurant_id"])) {
    header("Location: restaurant_login.php");
    exit;
}

$restaurant_id = $_SESSION["restaurant_id"];
$message = '';

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the menu item from the database
    $delete_query = "DELETE FROM menus WHERE menu_id = $delete_id AND restaurant_id = $restaurant_id";
    if (mysqli_query($conn, $delete_query)) {
        $message = "Menu item deleted successfully.";
    } else {
        $message = "Error deleting menu item: " . mysqli_error($conn);
    }
}

// Fetch all menu items for the logged-in restaurant
$query = "SELECT * FROM menus WHERE restaurant_id = $restaurant_id";
$result = mysqli_query($conn, $query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Menu</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <a href="add_menu.php" class="btn btn-primary mb-3">Add New Menu Item</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Menu Type</th>
                <th>Menu Name</th>
                <th>Description</th>
                <th>Creation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['menu_type']; ?></td>
                    <td><?php echo $row['menu_name']; ?></td>
                    <td><?php echo $row['menu_description']; ?></td>
                    <td><?php echo $row['creation_date']; ?></td>
                    <td>
                        <!-- Edit Button triggers a modal -->
                        <a href="edit_menu.php?menu_id=<?php echo $row['menu_id']; ?>" class="btn btn-warning">Edit</a>
                        
                        <!-- Delete Button -->
                        <a href="manage_menu.php?delete_id=<?php echo $row['menu_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

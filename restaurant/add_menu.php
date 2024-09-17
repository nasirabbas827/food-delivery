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

// Handle form submission for adding a new menu item
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_type = $_POST['menu_type'];
    $menu_name = $_POST['menu_name'];
    $menu_description = $_POST['menu_description'];
    $creation_date = date('Y-m-d H:i:s'); // Current date and time

    // Validate form data
    if (!empty($menu_type) && !empty($menu_name)) {
        // Insert new menu item into the database
        $insert_query = "INSERT INTO menus (restaurant_id, menu_type, menu_name, menu_description, creation_date) 
                         VALUES ('$restaurant_id', '$menu_type', '$menu_name', '$menu_description', '$creation_date')";

        if (mysqli_query($conn, $insert_query)) {
            $message = "Menu item added successfully.";
        } else {
            $message = "Error adding menu item: " . mysqli_error($conn);
        }
    } else {
        $message = "Menu type and name are required.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Add Menu Item</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="add_menu.php" method="post">
    <div class="form-group">
            <label for="menu_type">Menu Type:</label>
            <select class="form-control" id="menu_type" name="menu_type" required>
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
            </select>
        </div>
        <div class="form-group">
            <label for="menu_name">Menu Name:</label>
            <input type="text" class="form-control" id="menu_name" name="menu_name" required>
        </div>
        <div class="form-group">
            <label for="menu_description">Menu Description:</label>
            <textarea class="form-control" id="menu_description" name="menu_description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Menu Item</button>
        <a href="manage_menu.php" class="btn btn-outline-dark">Manage Menu</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

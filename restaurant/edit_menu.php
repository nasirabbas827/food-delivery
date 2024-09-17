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
$menu_item = array(); // Initialize $menu_item to avoid null reference

// Handle form submission for updating menu item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['menu_id'])) {
    $menu_id = $_POST['menu_id'];
    $menu_type = $_POST['menu_type'];
    $menu_name = $_POST['menu_name'];
    $menu_description = $_POST['menu_description'];

    // Validate form data
    if (!empty($menu_type) && !empty($menu_name)) {
        // Update the menu item in the database
        $update_query = "UPDATE menus SET 
                         menu_type = '$menu_type', 
                         menu_name = '$menu_name',
                         menu_description = '$menu_description'
                         WHERE menu_id = $menu_id AND restaurant_id = $restaurant_id";

        if (mysqli_query($conn, $update_query)) {
            $message = "Menu item updated successfully.";
        } else {
            $message = "Error updating menu item: " . mysqli_error($conn);
        }
    } else {
        $message = "Menu type and name are required.";
    }
}

// Fetch the menu item details to pre-fill the form
if (isset($_GET['menu_id'])) {
    $menu_id = $_GET['menu_id'];
    $query = "SELECT * FROM menus WHERE menu_id = $menu_id AND restaurant_id = $restaurant_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $menu_item = mysqli_fetch_assoc($result);
    } else {
        header("Location: manage_menu.php");
        exit;
    }
} else {
    header("Location: manage_menu.php");
    exit;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Edit Menu Item</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="edit_menu.php" method="post">
        <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($menu_item['menu_id']); ?>">
        <div class="form-group">
            <label for="menu_type">Menu Type:</label>
            <select class="form-control" id="menu_type" name="menu_type" required>
                <option value="Daily" <?php echo (isset($menu_item['menu_type']) && $menu_item['menu_type'] == 'Daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="Weekly" <?php echo (isset($menu_item['menu_type']) && $menu_item['menu_type'] == 'Weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="Monthly" <?php echo (isset($menu_item['menu_type']) && $menu_item['menu_type'] == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
        </div>
        <div class="form-group">
            <label for="menu_name">Menu Name:</label>
            <input type="text" class="form-control" id="menu_name" name="menu_name" value="<?php echo isset($menu_item['menu_name']) ? htmlspecialchars($menu_item['menu_name']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="menu_description">Menu Description:</label>
            <textarea class="form-control" id="menu_description" name="menu_description"><?php echo isset($menu_item['menu_description']) ? htmlspecialchars($menu_item['menu_description']) : ''; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Menu Item</button>
        <a href="manage_menu.php" class="btn btn-outline-dark">Cancel</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

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
    $menu_id = $_POST['menu_id'];
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $price = $_POST['price'];
    $availability_status = $_POST['availability_status'];

    // Handle image upload
    $item_image = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'menu_images/'; // Directory to save uploaded images
        $tmp_name = $_FILES['item_image']['tmp_name'];
        $file_name = basename($_FILES['item_image']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $item_image = $file_name; // Save the file name in the database
        } else {
            $message = "Failed to upload image.";
        }
    }

    // Validate form data
    if (!empty($menu_id) && !empty($item_name) && !empty($price)) {
        // Insert new menu item into the database
        $insert_query = "INSERT INTO menuitems (menu_id, item_name, item_description, price, item_image, availability_status) 
                         VALUES ('$menu_id', '$item_name', '$item_description', '$price', '$item_image', '$availability_status')";

        if (mysqli_query($conn, $insert_query)) {
            $message = "Menu item added successfully.";
        } else {
            $message = "Error adding menu item: " . mysqli_error($conn);
        }
    } else {
        $message = "Menu ID, item name, and price are required.";
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
    <form action="add_menu_item.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="menu_id">Select Menu:</label>
            <select class="form-control" id="menu_id" name="menu_id" required>
                <?php
                include('config.php');

                // Fetch the menus for the logged-in restaurant
                $menu_query = "SELECT menu_id, menu_name FROM menus WHERE restaurant_id = $restaurant_id";
                $menu_result = mysqli_query($conn, $menu_query);

                while ($menu = mysqli_fetch_assoc($menu_result)) {
                    echo "<option value=\"{$menu['menu_id']}\">{$menu['menu_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="item_name">Item Name:</label>
            <input type="text" class="form-control" id="item_name" name="item_name" required>
        </div>
        <div class="form-group">
            <label for="item_description">Item Description:</label>
            <textarea class="form-control" id="item_description" name="item_description"></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="availability_status">Availability Status:</label>
            <select class="form-control" id="availability_status" name="availability_status" required>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
            </select>
        </div>
        <div class="form-group">
            <label for="item_image">Item Image (optional):</label>
            <input type="file" class="form-control-file" id="item_image" name="item_image">
        </div>
        <button type="submit" class="btn btn-primary">Add Menu Item</button>
        <a href="manage_menu_items.php" class="btn btn-outline-dark">Manage Menu</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

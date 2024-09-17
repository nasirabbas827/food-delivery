<?php
include('config.php');

session_start();

// Check if restaurant is logged in, if not, redirect to login page
if (!isset($_SESSION["restaurant_id"]) || empty($_SESSION["restaurant_id"])) {
    header("Location: restaurant_login.php");
    exit;
}

$restaurant_id = $_SESSION["restaurant_id"];
$item_id = $_GET['id'] ?? '';
$message = '';

// Fetch the menu item details
if (!empty($item_id)) {
    $query = "
        SELECT mi.item_id, mi.menu_id, mi.item_name, mi.item_description, mi.price, mi.item_image, mi.availability_status, m.menu_name
        FROM menuitems mi
        JOIN menus m ON mi.menu_id = m.menu_id
        WHERE mi.item_id = '$item_id' AND m.restaurant_id = $restaurant_id
    ";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
    } else {
        header("Location: manage_menu.php");
        exit;
    }
}

// Handle form submission for updating the menu item
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_id = $_POST['menu_id'];
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $price = $_POST['price'];
    $availability_status = $_POST['availability_status'];

    // Handle file upload
    $item_image = $item['item_image'];
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $target_dir = "menu_images/";
        $target_file = $target_dir . basename($_FILES["item_image"]["name"]);
        $upload_ok = 1;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image
        $check = getimagesize($_FILES["item_image"]["tmp_name"]);
        if ($check !== false) {
            $upload_ok = 1;
        } else {
            $message = "File is not an image.";
            $upload_ok = 0;
        }

        // Check file size
        if ($_FILES["item_image"]["size"] > 500000) {
            $message = "Sorry, your file is too large.";
            $upload_ok = 0;
        }

        // Allow certain file formats
        if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $upload_ok = 0;
        }

        // Check if $upload_ok is set to 0 by an error
        if ($upload_ok == 0) {
            $message = "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $target_file)) {
                $item_image = basename($_FILES["item_image"]["name"]);
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Update the menu item in the database
    $update_query = "
        UPDATE menuitems 
        SET menu_id = '$menu_id', item_name = '$item_name', item_description = '$item_description', price = '$price', item_image = '$item_image', availability_status = '$availability_status' 
        WHERE item_id = '$item_id' AND menu_id IN (SELECT menu_id FROM menus WHERE restaurant_id = $restaurant_id)
    ";

    if (mysqli_query($conn, $update_query)) {
        $message = "Menu item updated successfully.";
    } else {
        $message = "Error updating menu item: " . mysqli_error($conn);
    }
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
    <form action="edit_menu_item.php?id=<?php echo $item_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="menu_id">Menu:</label>
            <select class="form-control" id="menu_id" name="menu_id" required>
                <?php
include('config.php');

                $menus_query = "SELECT menu_id, menu_name FROM menus WHERE restaurant_id = $restaurant_id";
                $menus_result = mysqli_query($conn, $menus_query);
                while ($menu = mysqli_fetch_assoc($menus_result)) {
                    echo "<option value=\"" . $menu['menu_id'] . "\"";
                    if ($menu['menu_id'] == $item['menu_id']) {
                        echo " selected";
                    }
                    echo ">" . htmlspecialchars($menu['menu_name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="item_name">Item Name:</label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="item_description">Item Description:</label>
            <textarea class="form-control" id="item_description" name="item_description"><?php echo htmlspecialchars($item['item_description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>
        </div>
        <div class="form-group">
            <label for="availability_status">Availability Status:</label>
            <select class="form-control" id="availability_status" name="availability_status" required>
                <option value="Available" <?php if ($item['availability_status'] == 'Available') echo 'selected'; ?>>Available</option>
                <option value="Not Available" <?php if ($item['availability_status'] == 'Not Available') echo 'selected'; ?>>Not Available</option>
            </select>
        </div>
        <div class="form-group">
            <label for="item_image">Item Image:</label>
            <input type="file" class="form-control-file" id="item_image" name="item_image">
            <?php if ($item['item_image']): ?>
                <img src="menu_images/<?php echo htmlspecialchars($item['item_image']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" width="100" class="mt-2">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Menu Item</button>
        <a href="manage_menu.php" class="btn btn-outline-dark">Back to Menu</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

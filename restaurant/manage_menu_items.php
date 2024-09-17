<?php
include('config.php');

session_start();

// Check if restaurant is logged in, if not, redirect to login page
if (!isset($_SESSION["restaurant_id"]) || empty($_SESSION["restaurant_id"])) {
    header("Location: restaurant_login.php");
    exit;
}

$restaurant_id = $_SESSION["restaurant_id"];

// Handle delete request
if (isset($_GET['delete_id'])) {
    $item_id = $_GET['delete_id'];

    // Delete the menu item from the database
    $delete_query = "DELETE FROM menuitems WHERE item_id = '$item_id' AND menu_id IN (SELECT menu_id FROM menus WHERE restaurant_id = $restaurant_id)";
    if (mysqli_query($conn, $delete_query)) {
        $message = "Menu item deleted successfully.";
    } else {
        $message = "Error deleting menu item: " . mysqli_error($conn);
    }
}

// Fetch menu items for the logged-in restaurant
$menu_items_query = "
    SELECT mi.item_id, mi.item_name, mi.item_description, mi.price, mi.item_image, mi.availability_status, m.menu_name
    FROM menuitems mi
    JOIN menus m ON mi.menu_id = m.menu_id
    WHERE m.restaurant_id = $restaurant_id
";
$menu_items_result = mysqli_query($conn, $menu_items_query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu Items</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Menu Items</h2>
    <?php if (isset($message)): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Status</th>
                <th>Menu</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($menu_items_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_description']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td>
                        <?php if ($row['item_image']): ?>
                            <img src="menu_images/<?php echo htmlspecialchars($row['item_image']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>" width="100">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['availability_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['menu_name']); ?></td>
                    <td>
                        <a href="edit_menu_item.php?id=<?php echo $row['item_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete_id=<?php echo $row['item_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="add_menu_item.php" class="btn btn-primary">Add Menu Item</a>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

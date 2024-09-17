<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$customer_id = $_SESSION["id"];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle Delete Order
if ($action == 'delete' && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Check the order status before deleting
    $status_query = "SELECT order_status FROM Orders WHERE order_id = $order_id AND customer_id = $customer_id";
    $status_result = mysqli_query($conn, $status_query);
    $status_row = mysqli_fetch_assoc($status_result);

    if ($status_row['order_status'] == 'Pending') {
        $delete_query = "DELETE FROM Orders WHERE order_id = $order_id AND customer_id = $customer_id";
        mysqli_query($conn, $delete_query);
        header("location: view_orders.php");
        exit;
    } else {
        $message = "Cannot delete. Order status is not 'Pending'.";
    }
}

// Handle Edit Order
if ($action == 'edit' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);

    // Check the order status before editing
    $status_query = "SELECT order_status FROM Orders WHERE order_id = $order_id AND customer_id = $customer_id";
    $status_result = mysqli_query($conn, $status_query);
    $status_row = mysqli_fetch_assoc($status_result);

    if ($status_row['order_status'] == 'Pending') {
        $update_query = "UPDATE Orders SET delivery_address = '$delivery_address' WHERE order_id = $order_id AND customer_id = $customer_id";
        mysqli_query($conn, $update_query);
        header("location: view_orders.php");
        exit;
    } else {
        $message = "Cannot edit. Order status is not 'Pending'.";
    }
}

// Fetch User Orders
$query = "SELECT o.*, r.restaurant_name, m.menu_name, i.item_name 
          FROM Orders o 
          JOIN Restaurants r ON o.restaurant_id = r.restaurant_id
          JOIN Menus m ON o.menu_id = m.menu_id
          JOIN MenuItems i ON o.item_id = i.item_id
          WHERE o.customer_id = $customer_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>My Orders</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-warning">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Restaurant Name</th>
                <th>Menu Name</th>
                <th>Item Name</th>
                <th>Total Amount</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Delivery Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['menu_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['total_amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                    <td>
                        <?php if ($row['order_status'] == 'Pending'): ?>
                            <a href="view_orders.php?action=edit&order_id=<?php echo $row['order_id']; ?>" class="mb-2 btn btn-primary btn-sm">Edit</a>
                            <a href="view_orders.php?action=delete&order_id=<?php echo $row['order_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm mb-2 " disabled>Edit</button>
                            <button class="btn btn-secondary btn-sm" disabled>Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if ($action == 'edit' && isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);
        $edit_query = "SELECT * FROM Orders WHERE order_id = $order_id AND customer_id = $customer_id";
        $edit_result = mysqli_query($conn, $edit_query);
        $edit_row = mysqli_fetch_assoc($edit_result);
    ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Edit Delivery Address</h3>
                <form method="POST" action="view_orders.php?action=edit">
                    <input type="hidden" name="order_id" value="<?php echo $edit_row['order_id']; ?>">
                    <div class="form-group">
                        <label for="delivery_address">Delivery Address</label>
                        <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="<?php echo htmlspecialchars($edit_row['delivery_address']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Address</button>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

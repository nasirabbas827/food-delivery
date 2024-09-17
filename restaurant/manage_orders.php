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

// Handle Update Order Status and Assign Rider
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $rider_id = !empty($_POST['rider_id']) ? $_POST['rider_id'] : NULL; // Allow NULL for no rider assigned

    // Update order status and assign rider
    $update_query = "UPDATE orders SET 
                     order_status = '$order_status', 
                     rider_id = " . ($rider_id ? "'$rider_id'" : "NULL") . "
                     WHERE order_id = $order_id AND restaurant_id = $restaurant_id";

    if (mysqli_query($conn, $update_query)) {
        $message = "Order updated successfully.";
    } else {
        $message = "Error updating order: " . mysqli_error($conn);
    }
}

// Fetch all orders for the logged-in restaurant
$query = "SELECT * FROM orders WHERE restaurant_id = $restaurant_id";
$result = mysqli_query($conn, $query);

// Fetch all rider IDs for the current restaurant
$riders_query = "SELECT rider_id FROM restaurant_riders WHERE restaurant_id = $restaurant_id";
$riders_result = mysqli_query($conn, $riders_query);

// Check for query errors
if (!$result || !$riders_result) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch rider details
$rider_details_query = "SELECT id, username FROM users WHERE id IN (SELECT rider_id FROM restaurant_riders WHERE restaurant_id = $restaurant_id)";
$rider_details_result = mysqli_query($conn, $rider_details_query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Orders</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Menu ID</th>
                <th>Item ID</th>
                <th>Order Date</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Delivery Address</th>
                <th>Assign Rider</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <form action="manage_orders.php" method="post">
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['menu_id']; ?></td>
                        <td><?php echo $row['item_id']; ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                        <td>
                            <select class="form-control" name="order_status">
                                <option value="Pending" <?php echo ($row['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?php echo ($row['order_status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Completed" <?php echo ($row['order_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo ($row['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </td>
                        <td><?php echo $row['payment_status']; ?></td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><?php echo $row['total_amount']; ?></td>
                        <td><?php echo $row['delivery_address']; ?></td>
                        <td>
                            <select class="form-control" name="rider_id">
                                <option value="">Select Rider</option>
                                <?php 
                                while ($rider = mysqli_fetch_assoc($rider_details_result)): ?>
                                    <option value="<?php echo $rider['id']; ?>" <?php echo ($row['rider_id'] == $rider['id']) ? 'selected' : ''; ?>>
                                        <?php echo $rider['username']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </td>
                    </form>
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

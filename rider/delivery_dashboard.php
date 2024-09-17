<?php
include('config.php');

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if rider is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("Location: index.php");
    exit;
}

$rider_id = $_SESSION["id"];
$message = '';

// Handle Update Order Status and Payment Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];

    // Update order status and payment status (if payment method is Cash on Delivery)
    $update_query = "UPDATE orders SET 
                     order_status = '$order_status', 
                     payment_status = IF(payment_method = 'Cash on Delivery', '$payment_status', payment_status)
                     WHERE order_id = $order_id AND rider_id = $rider_id";

    if (mysqli_query($conn, $update_query)) {
        $message = "Order updated successfully.";
    } else {
        $message = "Error updating order: " . mysqli_error($conn);
    }
}

// Fetch all orders for the logged-in rider
$query = "SELECT * FROM orders WHERE rider_id = $rider_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch new orders for the rider
$new_orders_query = "SELECT * FROM orders WHERE rider_id = $rider_id AND order_status = 'Pending'";
$new_orders_result = mysqli_query($conn, $new_orders_query);
$new_orders_count = mysqli_num_rows($new_orders_result);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Assigned Orders</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <!-- New Orders Card -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>New Orders</h4>
        </div>
        <div class="card-body">
            <p>You have <?php echo $new_orders_count; ?> new order(s) pending.</p>
            <?php if ($new_orders_count > 0): ?>
                <ul class="list-group">
                    <?php while ($order = mysqli_fetch_assoc($new_orders_result)): ?>
                        <li class="list-group-item">
                            <strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?><br>
                            <strong>Customer ID:</strong> <?php echo htmlspecialchars($order['customer_id']); ?><br>
                            <strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?><br>
                            <strong>Total Amount:</strong> <?php echo htmlspecialchars($order['total_amount']); ?><br>
                            <strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No new orders at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Orders Table -->
    <table class="table table-bordered table-responsive mt-4">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Username</th>
                <th>Menu Name</th>
                <th>Item Name</th>
                <th>Order Date</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Payment Method</th>
                <th>Total Amount</th>
                <th>Delivery Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <form action="delivery_dashboard.php" method="post">
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['menu_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td>
                            <select class="form-control" name="order_status">
                                <option value="Pending" <?php echo ($row['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?php echo ($row['order_status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Completed" <?php echo ($row['order_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo ($row['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="payment_status">
                                <option value="Pending" <?php echo ($row['payment_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Completed" <?php echo ($row['payment_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td>
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
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

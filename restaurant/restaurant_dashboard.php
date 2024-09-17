<?php
include('config.php');

session_start();

// Check if restaurant is logged in, if not, redirect to login page
if (!isset($_SESSION["restaurant_id"]) || empty($_SESSION["restaurant_id"])) {
    header("Location: restaurant_login.php");
    exit;
}

// Fetch restaurant details from the database
$restaurant_id = $_SESSION["restaurant_id"];
$query = "SELECT * FROM Restaurants WHERE restaurant_id = '$restaurant_id'";
$result = mysqli_query($conn, $query);
$restaurant = mysqli_fetch_assoc($result);

if (!$restaurant) {
    echo "Error fetching restaurant details.";
    exit;
}

// Fetch new orders for the restaurant
$new_orders_query = "SELECT * FROM orders WHERE restaurant_id = '$restaurant_id' AND order_status = 'Pending'";
$new_orders_result = mysqli_query($conn, $new_orders_query);
$new_orders_count = mysqli_num_rows($new_orders_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Welcome, <?php echo htmlspecialchars($restaurant['restaurant_name']); ?>!</h2>
    
    <!-- Restaurant Details Card -->
    <div class="card mt-3">
        <div class="card-header">
            <h4>Restaurant Details</h4>
        </div>
        <div class="card-body">
            <p><strong>Restaurant Name:</strong> <?php echo htmlspecialchars($restaurant['restaurant_name']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($restaurant['restaurant_address']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($restaurant['phone_number']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($restaurant['email']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($restaurant['status']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($restaurant['description']); ?></p>
            <p><strong>Logo:</strong> <img src="rest_images/<?php echo htmlspecialchars($restaurant['logo_image']); ?>" alt="Restaurant Logo" width="100"></p>
        </div>
    </div>

    <!-- New Orders Card -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>New Orders</h4>
        </div>
        <div class="card-body">
            <p>You have <?php echo $new_orders_count; ?> new order(s) placed.</p>
            <?php if ($new_orders_count > 0): ?>
                <ul class="list-group">
                    <?php while ($order = mysqli_fetch_assoc($new_orders_result)): ?>
                        <li class="list-group-item">
                            <strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?><br>
                            <strong>Customer ID:</strong> <?php echo htmlspecialchars($order['customer_id']); ?><br>
                            <strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?><br>
                            <strong>Total Amount:</strong> <?php echo htmlspecialchars($order['total_amount']); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No new orders at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4">
        <a href="edit_restaurant.php" class="btn btn-primary">Edit Restaurant Details</a>
        <a href="manage_orders.php" class="btn btn-success">Manage Orders</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

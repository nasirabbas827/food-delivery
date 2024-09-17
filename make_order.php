<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if required parameters are provided
if (!isset($_GET['restaurant_id']) || !isset($_GET['menu_id']) || !isset($_GET['item_id'])) {
    header("location: home.php");
    exit;
}

// Get IDs from the GET request
$restaurant_id = intval($_GET['restaurant_id']);
$menu_id = intval($_GET['menu_id']);
$item_id = intval($_GET['item_id']);

// Fetch restaurant details
$restaurant_query = "SELECT * FROM Restaurants WHERE restaurant_id = $restaurant_id";
$restaurant_result = mysqli_query($conn, $restaurant_query);
$restaurant = mysqli_fetch_assoc($restaurant_result);

// Fetch menu details
$menu_query = "SELECT * FROM Menus WHERE menu_id = $menu_id AND restaurant_id = $restaurant_id";
$menu_result = mysqli_query($conn, $menu_query);
$menu = mysqli_fetch_assoc($menu_result);

// Fetch menu item details
$item_query = "SELECT * FROM MenuItems WHERE item_id = $item_id AND menu_id = $menu_id";
$item_result = mysqli_query($conn, $item_query);
$item = mysqli_fetch_assoc($item_result);

// Check if all queries returned results
if (!$restaurant || !$menu || !$item) {
    header("location: home.php");
    exit;
}

// If the form is submitted, handle the order placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);
    $total_amount = $item['price'];
    $order_date = date('Y-m-d H:i:s');
    $order_status = 'Pending';
    $payment_status = 'Pending';
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $customer_id = $_SESSION['id']; // Assuming you have customer ID stored in session

    $order_query = "INSERT INTO Orders (customer_id, restaurant_id, menu_id, item_id, order_date, order_status, payment_status, payment_method, total_amount, delivery_address) 
                    VALUES ('$customer_id', '$restaurant_id', '$menu_id', '$item_id', '$order_date', '$order_status', '$payment_status', '$payment_method', '$total_amount', '$delivery_address')";
    
    if (mysqli_query($conn, $order_query)) {
        echo "Order placed successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Order - <?php echo htmlspecialchars($item['item_name']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Make Order</h2>
    <div class="card mt-4">
        <div class="card-body">
            <h3><?php echo htmlspecialchars($restaurant['restaurant_name']); ?></h3>
            <p>
                <strong>Address:</strong> <?php echo htmlspecialchars($restaurant['restaurant_address']); ?><br>
                <strong>Phone:</strong> <?php echo htmlspecialchars($restaurant['phone_number']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($restaurant['email']); ?><br>
                <strong>Status:</strong> <?php echo htmlspecialchars($restaurant['status']); ?><br>
            </p>
            <hr>
            <h4><?php echo htmlspecialchars($menu['menu_name']); ?></h4>
            <p>
                <strong>Type:</strong> <?php echo htmlspecialchars($menu['menu_type']); ?><br>
                <strong>Description:</strong> <?php echo htmlspecialchars($menu['menu_description']); ?><br>
                <strong>Creation Date:</strong> <?php echo htmlspecialchars($menu['creation_date']); ?><br>
            </p>
            <hr>
            <h5><?php echo htmlspecialchars($item['item_name']); ?></h5>
            <p>
                <strong>Description:</strong> <?php echo htmlspecialchars($item['item_description']); ?><br>
                <strong>Price:</strong> $<?php echo htmlspecialchars($item['price']); ?><br>
            </p>
            <hr>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="delivery_address">Delivery Address</label>
                    <textarea class="form-control" id="delivery_address" name="delivery_address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Online Transfer">Online Transfer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Place Order</button>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

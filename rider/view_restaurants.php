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

// Handle restaurant registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['restaurant_id'])) {
    $restaurant_id = $_POST['restaurant_id'];

    // Check if the rider is already registered with this restaurant
    $check_query = "SELECT * FROM restaurant_riders WHERE rider_id = $rider_id AND restaurant_id = $restaurant_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "You are already registered with this restaurant.";
    } else {
        // Register the rider with the restaurant
        $register_query = "INSERT INTO restaurant_riders (rider_id, restaurant_id) VALUES ($rider_id, $restaurant_id)";

        if (mysqli_query($conn, $register_query)) {
            $message = "Successfully registered with the restaurant.";
        } else {
            $message = "Error registering with the restaurant: " . mysqli_error($conn);
        }
    }
}

// Handle restaurant deregistration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deregister_restaurant_id'])) {
    $restaurant_id = $_POST['deregister_restaurant_id'];

    // Deregister the rider from the restaurant
    $deregister_query = "DELETE FROM restaurant_riders WHERE rider_id = $rider_id AND restaurant_id = $restaurant_id";

    if (mysqli_query($conn, $deregister_query)) {
        $message = "Successfully deregistered from the restaurant.";
    } else {
        $message = "Error deregistering from the restaurant: " . mysqli_error($conn);
    }
}

// Fetch all restaurants
$query = "SELECT * FROM Restaurants";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Restaurants</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>All Restaurants</h2>

    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th>Restaurant ID</th>
                <th>Restaurant Name</th>
                <th>Address</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Description</th>
                <th>Logo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                // Check if the rider is registered with this restaurant
                $registered_query = "SELECT * FROM restaurant_riders WHERE rider_id = $rider_id AND restaurant_id = " . $row['restaurant_id'];
                $registered_result = mysqli_query($conn, $registered_query);
                $is_registered = mysqli_num_rows($registered_result) > 0;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['restaurant_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['restaurant_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><img src="../restaurant/<?php echo htmlspecialchars($row['logo_image']); ?>" alt="Restaurant Logo" width="100"></td>
                    <td>
                        <?php if ($is_registered): ?>
                            <form action="view_restaurants.php" method="post" style="display:inline;">
                                <input type="hidden" name="deregister_restaurant_id" value="<?php echo htmlspecialchars($row['restaurant_id']); ?>">
                                <button type="submit" class="btn btn-danger">Deregister</button>
                            </form>
                        <?php else: ?>
                            <form action="view_restaurants.php" method="post" style="display:inline;">
                                <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($row['restaurant_id']); ?>">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </form>
                        <?php endif; ?>
                    </td>
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

<?php
// Close the database connection at the end
mysqli_close($conn);
?>

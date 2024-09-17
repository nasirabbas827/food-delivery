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

// Handle rider registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rider_id'])) {
    $rider_id = $_POST['rider_id'];

    // Check if the rider is already registered with this restaurant
    $check_query = "SELECT * FROM restaurant_riders WHERE rider_id = $rider_id AND restaurant_id = $restaurant_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "This rider is already registered with your restaurant.";
    } else {
        // Register the rider with the restaurant
        $register_query = "INSERT INTO restaurant_riders (rider_id, restaurant_id) VALUES ($rider_id, $restaurant_id)";
        
        if (mysqli_query($conn, $register_query)) {
            $message = "Rider registered successfully.";
        } else {
            $message = "Error registering rider: " . mysqli_error($conn);
        }
    }
}

// Handle rider unregistration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unregister_rider_id'])) {
    $unregister_rider_id = $_POST['unregister_rider_id'];

    // Unregister the rider from the restaurant
    $unregister_query = "DELETE FROM restaurant_riders WHERE rider_id = $unregister_rider_id AND restaurant_id = $restaurant_id";
    
    if (mysqli_query($conn, $unregister_query)) {
        $message = "Rider unregistered successfully.";
    } else {
        $message = "Error unregistering rider: " . mysqli_error($conn);
    }
}

// Fetch all registered riders for the logged-in restaurant
$registered_riders_query = "SELECT r.rider_id, u.username 
                            FROM restaurant_riders r 
                            JOIN users u ON r.rider_id = u.id 
                            WHERE r.restaurant_id = $restaurant_id";
$registered_riders_result = mysqli_query($conn, $registered_riders_query);

// Fetch all riders available for registration
$available_riders_query = "SELECT id, username FROM users WHERE usertype = 'Delivery Boy'";
$available_riders_result = mysqli_query($conn, $available_riders_query);

if (!$registered_riders_result || !$available_riders_result) {
    die("Query failed: " . mysqli_error($conn));
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Register Riders</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Registered Riders</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rider ID</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($registered_riders_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['rider_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <form action="view_riders.php" method="post" style="display:inline;">
                            <input type="hidden" name="unregister_rider_id" value="<?php echo htmlspecialchars($row['rider_id']); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Unregister</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 class="mt-5">Register New Rider</h3>
    <form action="view_riders.php" method="post">
        <div class="form-group">
            <label for="rider_id">Select Rider:</label>
            <select class="form-control" id="rider_id" name="rider_id">
                <option value="">Select Rider</option>
                <?php while ($rider = mysqli_fetch_assoc($available_riders_result)): ?>
                    <option value="<?php echo $rider['id']; ?>"><?php echo htmlspecialchars($rider['username']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Register Rider</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

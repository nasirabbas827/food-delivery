<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if the restaurant_id is passed via GET, if not redirect to home
if (!isset($_GET['restaurant_id']) || empty($_GET['restaurant_id'])) {
    header("location: home.php");
    exit;
}

// Get the restaurant_id from the GET request
$restaurant_id = intval($_GET['restaurant_id']);

// Handle search query
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_query = mysqli_real_escape_string($conn, $search_query);
    $query = "SELECT * FROM Menus WHERE restaurant_id = $restaurant_id AND (menu_id LIKE '%$search_query%' OR menu_type LIKE '%$search_query%' OR menu_name LIKE '%$search_query%' OR menu_description LIKE '%$search_query%' OR creation_date LIKE '%$search_query%')";
} else {
    $query = "SELECT * FROM Menus WHERE restaurant_id = $restaurant_id";
}

$result = mysqli_query($conn, $query);

// Fetch restaurant name for display
$restaurant_query = "SELECT restaurant_name FROM Restaurants WHERE restaurant_id = $restaurant_id";
$restaurant_result = mysqli_query($conn, $restaurant_query);
$restaurant = mysqli_fetch_assoc($restaurant_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['restaurant_name']); ?> Menus</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($restaurant['restaurant_name']); ?> Menus</h2>
        </div>
        <div class="col-md-6">
            <form class="form-inline float-right" method="GET" action="view_menu.php">
                <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id; ?>">
                <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search by menu ID, type, name, description, or date" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['menu_name']); ?></h5>
                        <p class="card-text">
                            <strong>Menu ID:</strong> <?php echo htmlspecialchars($row['menu_id']); ?><br>
                            <strong>Menu Type:</strong> <?php echo htmlspecialchars($row['menu_type']); ?><br>
                            <strong>Description:</strong> <?php echo htmlspecialchars($row['menu_description']); ?><br>
                            <strong>Creation Date:</strong> <?php echo htmlspecialchars($row['creation_date']); ?>
                        </p>
                        <a href="view_menu_items.php?menu_id=<?php echo $row['menu_id']; ?>&restaurant_id=<?php echo $restaurant_id; ?>" class="btn btn-primary">View Menu Items</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

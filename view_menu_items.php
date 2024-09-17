<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if menu_id and restaurant_id are provided via GET, if not redirect to home
if (!isset($_GET['menu_id']) || empty($_GET['menu_id']) || !isset($_GET['restaurant_id']) || empty($_GET['restaurant_id'])) {
    header("location: home.php");
    exit;
}

// Get the menu_id and restaurant_id from the GET request
$menu_id = intval($_GET['menu_id']);
$restaurant_id = intval($_GET['restaurant_id']);

// Query to fetch menu items for the given menu_id
$query = "SELECT * FROM MenuItems WHERE menu_id = $menu_id";
$result = mysqli_query($conn, $query);

// Fetch menu name for display
$menu_query = "SELECT menu_name FROM Menus WHERE menu_id = $menu_id";
$menu_result = mysqli_query($conn, $menu_query);
$menu = mysqli_fetch_assoc($menu_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($menu['menu_name']); ?> - Menu Items</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2><?php echo htmlspecialchars($menu['menu_name']); ?> - Menu Items</h2>
    
    <div class="row mt-4">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img class="card-img-top" src="restaurant/menu_images/<?php echo htmlspecialchars($row['item_image']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['item_name']); ?></h5>
                        <p class="card-text">
                            <strong>Description:</strong> <?php echo htmlspecialchars($row['item_description']); ?><br>
                            <strong>Price:</strong> $<?php echo htmlspecialchars($row['price']); ?>
                        </p>
                        <?php if ($row['availability_status']) { ?>
                            <a href="make_order.php?restaurant_id=<?php echo $restaurant_id; ?>&menu_id=<?php echo $menu_id; ?>&item_id=<?php echo $row['item_id']; ?>" class="btn btn-success">Make Order</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary" disabled>Unavailable</button>
                        <?php } ?>
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

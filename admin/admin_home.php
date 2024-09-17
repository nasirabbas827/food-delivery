<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch the total counts for restaurants, menus, and menu items
$restaurants_count_query = "SELECT COUNT(*) AS total_restaurants FROM Restaurants";
$menus_count_query = "SELECT COUNT(*) AS total_menus FROM Menus";
$menu_items_count_query = "SELECT COUNT(*) AS total_menu_items FROM MenuItems";

$restaurants_count_result = mysqli_query($conn, $restaurants_count_query);
$menus_count_result = mysqli_query($conn, $menus_count_query);
$menu_items_count_result = mysqli_query($conn, $menu_items_count_query);

$restaurants_count = mysqli_fetch_assoc($restaurants_count_result)['total_restaurants'];
$menus_count = mysqli_fetch_assoc($menus_count_result)['total_menus'];
$menu_items_count = mysqli_fetch_assoc($menu_items_count_result)['total_menu_items'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <div class="row">
        <!-- Total Restaurants Card -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Restaurants</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $restaurants_count; ?></h5>
                    <p class="card-text">Number of restaurants currently in the system.</p>
                </div>
            </div>
        </div>

        <!-- Total Menus Card -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Menus</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $menus_count; ?></h5>
                    <p class="card-text">Number of menus currently available in the system.</p>
                </div>
            </div>
        </div>

        <!-- Total Menu Items Card -->
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Menu Items</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $menu_items_count; ?></h5>
                    <p class="card-text">Number of menu items currently in the system.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include('config.php');

// Handle search query
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_query = mysqli_real_escape_string($conn, $search_query);
    $query = "SELECT * FROM Restaurants WHERE restaurant_name LIKE '%$search_query%' OR restaurant_address LIKE '%$search_query%'";
} else {
    $query = "SELECT * FROM Restaurants";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Food Delivery System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
 <style>
.jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }
        .search-form {
            width: 80%;
            margin: 0 auto;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .btn-sm {
            font-size: 0.875rem;
        }
        .promotion-btn {
            margin-bottom: 15px;
        }
        .no-results {
            text-align: center;
            font-size: 1.25rem;
            color: #dc3545;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<div class="jumbotron text-center">
    <h1>Welcome to Online Food Delivery System</h1>
    <p>Discover Your Perfect Food Experience with Us</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Explore</a>
</div>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h2>Restaurants</h2>
            <form class="form-inline search-form mb-4" method="GET" action="index.php">
                <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search by name or address" aria-label="Search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </div>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="no-results">
            No results found.
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="restaurant/rest_images/<?php echo htmlspecialchars($row['logo_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['restaurant_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['restaurant_name']); ?></h5>
                            <p class="card-text">
                                <strong>Address:</strong> <?php echo htmlspecialchars($row['restaurant_address']); ?><br>
                                <strong>Phone Number:</strong> <?php echo htmlspecialchars($row['phone_number']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?><br>
                                <strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?><br>
                                <strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?>
                            </p>
                            <a href="view_menu.php?restaurant_id=<?php echo $row['restaurant_id']; ?>" class="btn btn-primary btn-sm mb-2" <?php echo ($row['status'] === 'Closed') ? 'disabled' : ''; ?>">View Menu</a>
                            <a href="food_review.php?restaurant_id=<?php echo $row['restaurant_id']; ?>" class="btn btn-secondary btn-sm mb-2">Food Review</a>
                            <a href="promotions.php?restaurant_id=<?php echo $row['restaurant_id']; ?>" class="btn btn-info btn-sm promotion-btn">View Promotions</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php endif; ?>

</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 Online Food Delivery System. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

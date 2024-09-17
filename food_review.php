<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$restaurant_id = intval($_GET['restaurant_id']);

// Handle Review Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION["id"];
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $rating = intval($_POST['rating']);

    $insert_query = "INSERT INTO Reviews (restaurant_id, customer_id, review, rating) VALUES ('$restaurant_id', '$customer_id', '$review', '$rating')";
    mysqli_query($conn, $insert_query);
    header("location: food_review.php?restaurant_id=$restaurant_id");
    exit;
}

// Fetch Existing Reviews
$query = "SELECT r.*, c.username FROM Reviews r JOIN users c ON r.customer_id = c.id WHERE r.restaurant_id = $restaurant_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Reviews</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
.checked {
    color: orange;
}
</style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Food Reviews</h2>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['username']); ?></h5>
                        <div class="mb-1">
                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                                <span class="fa fa-star <?php echo ($i <= $row['rating']) ? 'checked' : ''; ?>"></span>
                            <?php } ?>
                        </div>
                        <p class="card-text"><?php echo htmlspecialchars($row['review']); ?></p>
                        <small>Reviewed on <?php echo htmlspecialchars($row['created_at']); ?></small>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <hr>

    <h3>Write a Review</h3>
    <form method="POST" action="food_review.php?restaurant_id=<?php echo $restaurant_id; ?>">
        <div class="form-group">
            <label for="review">Review</label>
            <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="rating">Rating</label>
            <select class="form-control" id="rating" name="rating" required>
                <option value="1">1 - Very Bad</option>
                <option value="2">2 - Bad</option>
                <option value="3">3 - Average</option>
                <option value="4">4 - Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



<?php
include('config.php');

session_start();



// Fetch promotions
$query = "SELECT r.restaurant_name, p.discount_percentage, p.description AS promo_description, p.valid_from, p.valid_to
          FROM promotions p
          JOIN Restaurants r ON p.restaurant_id = r.restaurant_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Promotions</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Restaurant Name</th>
                <th>Promotion Description</th>
                <th>Discount Percentage</th>
                <th>Valid From</th>
                <th>Valid To</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['promo_description']); ?></td>
                    <td><?php echo htmlspecialchars($row['discount_percentage']); ?>%</td>
                    <td><?php echo htmlspecialchars($row['valid_from']); ?></td>
                    <td><?php echo htmlspecialchars($row['valid_to']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

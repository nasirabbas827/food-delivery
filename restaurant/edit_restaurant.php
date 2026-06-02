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

// Fetch restaurant details from the database
$query = "SELECT * FROM Restaurants WHERE restaurant_id = '$restaurant_id'";
$result = mysqli_query($conn, $query);
$restaurant = mysqli_fetch_assoc($result);

if (!$restaurant) {
    $message = "Error fetching restaurant details.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $restaurant_name = $_POST['restaurant_name'];
    $restaurant_address = $_POST['restaurant_address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $new_password = $_POST['password'];

    // Check if a new password is provided
    if (!empty($new_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE Restaurants SET 
                         restaurant_name = '$restaurant_name', 
                         restaurant_address = '$restaurant_address',
                         phone_number = '$phone_number',
                         email = '$email',
                         status = '$status',
                         description = '$description',
                         password = "YOUR_OWN_API_KEY"";
    } else {
        $update_query = "UPDATE Restaurants SET 
                         restaurant_name = '$restaurant_name', 
                         restaurant_address = '$restaurant_address',
                         phone_number = '$phone_number',
                         email = '$email',
                         status = '$status',
                         description = '$description'";
    }

    // Handle logo image upload if a new file is provided
    if (!empty($_FILES["logo_image"]["name"])) {
        // Fetch the old logo image path to delete the file
        $old_logo_image = $restaurant['logo_image'];

        // Upload new logo image
        $target_dir = "rest_images/";
        $image_name = basename($_FILES["logo_image"]["name"]); // Get only the image file name
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["logo_image"]["tmp_name"]);
        if ($check !== false && ($_FILES["logo_image"]["size"] <= 500000) && 
            ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg")) {

            if (move_uploaded_file($_FILES["logo_image"]["tmp_name"], $target_file)) {
                // Update query to include the new logo image (only the image name)
                $update_query .= ", logo_image = '$image_name'";

                // Delete the old logo image file
                if (file_exists("rest_images/" . $old_logo_image)) {
                    unlink("rest_images/" . $old_logo_image);
                }
            } else {
                $message = "Error uploading new logo image.";
            }
        } else {
            $message = "Invalid image file.";
        }
    }

    $update_query .= " WHERE restaurant_id = '$restaurant_id'";

    if (mysqli_query($conn, $update_query)) {
        $message = "Restaurant details updated successfully.";
    } else {
        $message = "Error updating restaurant details: " . mysqli_error($conn);
    }
}




mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Restaurant Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Edit Restaurant Details</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="edit_restaurant.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="restaurant_name">Restaurant Name:</label>
            <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" value="<?php echo htmlspecialchars($restaurant['restaurant_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="restaurant_address">Restaurant Address:</label>
            <input type="text" class="form-control" id="restaurant_address" name="restaurant_address" value="<?php echo htmlspecialchars($restaurant['restaurant_address']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($restaurant['phone_number']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($restaurant['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status">
                <option value="Open" <?php echo ($restaurant['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                <option value="Closed" <?php echo ($restaurant['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($restaurant['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="logo_image">Restaurant Logo:</label>
            <input type="file" class="form-control-file" id="logo_image" name="logo_image">
            <img src="rest_images/<?php echo $restaurant['logo_image']; ?>" alt="Current Logo" width="100" class="mt-2">
        </div>
        <div class="form-group">
            <label for="password">New Password (Leave blank to keep current password):</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

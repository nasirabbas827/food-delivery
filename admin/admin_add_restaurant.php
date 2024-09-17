<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $restaurant_name = $_POST['restaurant_name'];
    $restaurant_address = $_POST['restaurant_address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password']; // New password input
    $status = $_POST['status'];
    $description = $_POST['description'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle logo image upload
    $target_dir = "../restaurant/rest_images/";
    $logo_image_name = basename($_FILES["logo_image"]["name"]);
    $target_file = $target_dir . $logo_image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($_FILES["logo_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["logo_image"]["size"] > 500000) {
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $message = "Sorry, only JPG, JPEG, and PNG files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["logo_image"]["tmp_name"], $target_file)) {
            // Insert into database (save only the image name)
            $sql = "INSERT INTO Restaurants (restaurant_name, restaurant_address, phone_number, email, password, status, description, logo_image) 
                    VALUES ('$restaurant_name', '$restaurant_address', '$phone_number', '$email', '$hashed_password', '$status', '$description', '$logo_image_name')";
            
            if (mysqli_query($conn, $sql)) {
                $message = "New restaurant added successfully.";
            } else {
                $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Restaurant</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Add Restaurant</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="admin_add_restaurant.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="restaurant_name">Restaurant Name:</label>
            <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" required>
        </div>
        <div class="form-group">
            <label for="restaurant_address">Restaurant Address:</label>
            <input type="text" class="form-control" id="restaurant_address" name="restaurant_address" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
    <label for="password">Password:</label>
    <input type="password" class="form-control" id="password" name="password" required>
</div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status">
                <option value="Open">Open</option>
                <option value="Closed">Closed</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="logo_image">Restaurant Logo:</label>
            <input type="file" class="form-control-file" id="logo_image" name="logo_image" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Restaurant</button>
        <a class="btn btn-outline-dark" href="admin_manage_restaurants.php">View Restaurants</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

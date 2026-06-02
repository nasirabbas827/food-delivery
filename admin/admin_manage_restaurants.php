<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Fetch the restaurant's logo image path to delete the file
    $query = "SELECT logo_image FROM Restaurants WHERE restaurant_id = $delete_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $logo_image = $row['logo_image'];
        
        // Delete the logo image file from the directory
        if (file_exists($logo_image)) {
            unlink($logo_image);
        }

        // Delete the restaurant from the database
        $delete_query = "DELETE FROM Restaurants WHERE restaurant_id = $delete_id";
        if (mysqli_query($conn, $delete_query)) {
            $message = "Restaurant deleted successfully.";
        } else {
            $message = "Error deleting restaurant: " . mysqli_error($conn);
        }
    }
}

// Handle Edit Request (including logo image and password handling)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $restaurant_name = $_POST['restaurant_name'];
    $restaurant_address = $_POST['restaurant_address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $password = $_POST['password'];

    // Start building the update query
    $update_query = "UPDATE Restaurants SET 
                     restaurant_name = '$restaurant_name', 
                     restaurant_address = '$restaurant_address',
                     phone_number = '$phone_number',
                     email = '$email',
                     status = '$status',
                     description = '$description'";

    // If password is provided, hash it and include in the update
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query .= ", password = "YOUR_OWN_API_KEY"";
    }

    // Handle logo image upload if a new file is provided
    if (!empty($_FILES["logo_image"]["name"])) {
        // Fetch the current image to delete it
        $query = "SELECT logo_image FROM Restaurants WHERE restaurant_id = $edit_id";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $current_image = $row['logo_image'];
            $current_image_path = "../restaurant/rest_images/" . $current_image;

            // Delete the old image file if it exists
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }
        }

        // Define the target directory and file name for the new image
        $target_dir = "../restaurant/rest_images/";
        $target_file = $target_dir . basename($_FILES["logo_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image file (check type and size)
        $check = getimagesize($_FILES["logo_image"]["tmp_name"]);
        if ($check !== false && ($_FILES["logo_image"]["size"] <= 500000) && 
            ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png")) {

            // Move uploaded file to target directory
            if (move_uploaded_file($_FILES["logo_image"]["tmp_name"], $target_file)) {
                // Save only the image name to the database
                $image_name = basename($_FILES["logo_image"]["name"]);
                $update_query .= ", logo_image = '$image_name'";
            } else {
                $message = "Sorry, there was an error uploading your new logo.";
            }
        } else {
            $message = "Invalid image file. Only JPG, JPEG, and PNG formats are allowed, and the file must be less than 500KB.";
        }
    }

    $update_query .= " WHERE restaurant_id = $edit_id";

    if (mysqli_query($conn, $update_query)) {
        $message = "Restaurant updated successfully.";
    } else {
        $message = "Error updating restaurant: " . mysqli_error($conn);
    }
}


// Fetch all restaurants (same as before)
$query = "SELECT * FROM Restaurants";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Restaurants</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Restaurants</h2>
    <?php if (isset($message) && $message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Status</th>
                <th>Description</th>
                <th>Password</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="../restaurant/rest_images/<?php echo $row['logo_image']; ?>" alt="Logo" width="50"></td>
                    <td><?php echo $row['restaurant_name']; ?></td>
                    <td><?php echo $row['restaurant_address']; ?></td>
                    <td><?php echo $row['phone_number']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['password']; ?></td>
                    <td>
                        <!-- Edit Button triggers a modal -->
                        <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#editModal<?php echo $row['restaurant_id']; ?>">Edit</button>
                        
                        <!-- Delete Button -->
                        <a href="admin_manage_restaurants.php?delete_id=<?php echo $row['restaurant_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this restaurant?');">Delete</a>
                    </td>
                </tr>

<!-- Edit Modal -->
<div class="modal fade" id="editModal<?php echo $row['restaurant_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Restaurant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="admin_manage_restaurants.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="edit_id" value="<?php echo $row['restaurant_id']; ?>">
                    <div class="form-group">
                        <label for="restaurant_name">Restaurant Name:</label>
                        <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" value="<?php echo $row['restaurant_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="restaurant_address">Restaurant Address:</label>
                        <input type="text" class="form-control" id="restaurant_address" name="restaurant_address" value="<?php echo $row['restaurant_address']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $row['phone_number']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select class="form-control" id="status" name="status">
                            <option value="Open" <?php echo ($row['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                            <option value="Closed" <?php echo ($row['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"><?php echo $row['description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" value="">
                        <small class="form-text text-muted">Leave blank if you don't want to update the password.</small>
                    </div>
                    <div class="form-group">
                        <label for="logo_image">Restaurant Logo:</label>
                        <input type="file" class="form-control-file" id="logo_image" name="logo_image">
                        <img src="../restaurant/rest_images/<?php echo $row['logo_image']; ?>" alt="Current Logo" width="50" class="mt-2">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

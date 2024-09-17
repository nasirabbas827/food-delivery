<?php
include('config.php');

session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$restaurant_id = null;

// Handle restaurant selection and promotion management
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['restaurant_id'])) {
        $restaurant_id = $_POST['restaurant_id'];
    }

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            $discount_percentage = $_POST['discount_percentage'];
            $description = $_POST['description'];
            $valid_from = $_POST['valid_from'];
            $valid_to = $_POST['valid_to'];

            // Validate discount percentage
            if ($discount_percentage < 0 || $discount_percentage > 100) {
                $message = "Discount percentage must be between 0 and 100.";
            } else {
                // Insert new promotion into the database
                $insert_query = "INSERT INTO promotions (restaurant_id, discount_percentage, description, valid_from, valid_to)
                                 VALUES ('$restaurant_id', '$discount_percentage', '$description', '$valid_from', '$valid_to')";

                if (mysqli_query($conn, $insert_query)) {
                    $message = "Promotion added successfully.";
                } else {
                    $message = "Error adding promotion: " . mysqli_error($conn);
                }
            }
        } elseif ($action == 'edit') {
            $promotion_id = $_POST['promotion_id'];
            $discount_percentage = $_POST['discount_percentage'];
            $description = $_POST['description'];
            $valid_from = $_POST['valid_from'];
            $valid_to = $_POST['valid_to'];

            // Validate discount percentage
            if ($discount_percentage < 0 || $discount_percentage > 100) {
                $message = "Discount percentage must be between 0 and 100.";
            } else {
                // Update promotion in the database
                $update_query = "UPDATE promotions SET 
                                 discount_percentage = '$discount_percentage', 
                                 description = '$description', 
                                 valid_from = '$valid_from', 
                                 valid_to = '$valid_to' 
                                 WHERE promotion_id = '$promotion_id' AND restaurant_id = '$restaurant_id'";

                if (mysqli_query($conn, $update_query)) {
                    $message = "Promotion updated successfully.";
                } else {
                    $message = "Error updating promotion: " . mysqli_error($conn);
                }
            }
        } elseif ($action == 'delete') {
            $promotion_id = $_POST['promotion_id'];

            // Delete promotion from the database
            $delete_query = "DELETE FROM promotions WHERE promotion_id = '$promotion_id' AND restaurant_id = '$restaurant_id'";

            if (mysqli_query($conn, $delete_query)) {
                $message = "Promotion deleted successfully.";
            } else {
                $message = "Error deleting promotion: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch all restaurants
$restaurants_query = "SELECT * FROM Restaurants";
$restaurants_result = mysqli_query($conn, $restaurants_query);

// Fetch promotions for the selected restaurant if any
$promotions_result = null;
if ($restaurant_id) {
    $promotions_query = "SELECT * FROM promotions WHERE restaurant_id = $restaurant_id";
    $promotions_result = mysqli_query($conn, $promotions_query);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Promotions</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Promotions</h2>
    <?php if ($message): ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Form to Select Restaurant -->
    <form action="manage_promotions.php" method="post">
        <div class="form-group">
            <label for="restaurant_id">Select Restaurant:</label>
            <select class="form-control" id="restaurant_id" name="restaurant_id" required>
                <option value="">Select a restaurant</option>
                <?php while ($row = mysqli_fetch_assoc($restaurants_result)): ?>
                    <option value="<?php echo htmlspecialchars($row['restaurant_id']); ?>" <?php echo ($restaurant_id == $row['restaurant_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['restaurant_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Select Restaurant</button>
    </form>

    <?php if ($restaurant_id): ?>
        <!-- Form to Add New Promotion -->
        <h3 class="mt-5">Add New Promotion for Restaurant <?php echo htmlspecialchars($restaurant_id); ?></h3>
        <form action="manage_promotions.php" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant_id); ?>">
            <div class="form-group">
                <label for="discount_percentage">Discount Percentage:</label>
                <input type="number" step="0.01" class="form-control" id="discount_percentage" name="discount_percentage" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="valid_from">Valid From:</label>
                <input type="date" class="form-control" id="valid_from" name="valid_from" required>
            </div>
            <div class="form-group">
                <label for="valid_to">Valid To:</label>
                <input type="date" class="form-control" id="valid_to" name="valid_to" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Promotion</button>
        </form>

        <!-- Table to View and Edit/Delete Existing Promotions -->
        <h3 class="mt-5">Existing Promotions</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Promotion ID</th>
                    <th>Discount Percentage</th>
                    <th>Description</th>
                    <th>Valid From</th>
                    <th>Valid To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($promotions_result): ?>
                    <?php while ($row = mysqli_fetch_assoc($promotions_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['promotion_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['discount_percentage']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['valid_from']); ?></td>
                            <td><?php echo htmlspecialchars($row['valid_to']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $row['promotion_id']; ?>">
                                    Edit
                                </button>
                                <!-- Delete Button -->
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?php echo $row['promotion_id']; ?>">
                                    Delete
                                </button>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $row['promotion_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Promotion</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="manage_promotions.php" method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant_id); ?>">
                                                    <input type="hidden" name="promotion_id" value="<?php echo $row['promotion_id']; ?>">
                                                    <div class="form-group">
                                                        <label for="discount_percentage_edit">Discount Percentage:</label>
                                                        <input type="number" step="0.01" class="form-control" id="discount_percentage_edit" name="discount_percentage" value="<?php echo htmlspecialchars($row['discount_percentage']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="description_edit">Description:</label>
                                                        <textarea class="form-control" id="description_edit" name="description" rows="3" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="valid_from_edit">Valid From:</label>
                                                        <input type="date" class="form-control" id="valid_from_edit" name="valid_from" value="<?php echo htmlspecialchars($row['valid_from']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="valid_to_edit">Valid To:</label>
                                                        <input type="date" class="form-control" id="valid_to_edit" name="valid_to" value="<?php echo htmlspecialchars($row['valid_to']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?php echo $row['promotion_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Delete Promotion</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="manage_promotions.php" method="post">
                                                <div class="modal-body">
                                                    Are you sure you want to delete this promotion?
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant_id); ?>">
                                                    <input type="hidden" name="promotion_id" value="<?php echo $row['promotion_id']; ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

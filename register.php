<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
include('config.php');

// define variables and initialize with empty values
$username = $password = $email = $phone = $usertype = "";
$username_err = $password_err = $email_err = $phone_err = $usertype_err = "";

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // check if username already exists in database
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($_POST["username"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
    }

    // validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } else {
        $email = trim($_POST["email"]);
        // check if email already exists in database
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email address is already taken.";
        }
    }

    // validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter a phone number.";
    } else {
        $phone = trim($_POST["phone"]);
        // check if phone number already exists in database
        $sql = "SELECT id FROM users WHERE phone = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_phone);
        $param_phone = $phone;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $phone_err = "This phone number is already taken.";
        }
    }

    // validate usertype
    if (empty($_POST["usertype"])) {
        $usertype_err = "Please select a user type.";
    } else {
        $usertype = trim($_POST["usertype"]);
    }

    // if no errors, insert user into database
    if (empty($username_err) && empty($password_err) && empty($email_err) && empty($phone_err) && empty($usertype_err)) {
        $status = "pending";
        $verification_code = md5(rand());
        $sql = "INSERT INTO users (username, password, email, phone, usertype, status, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssss", $param_username, $param_password, $param_email, $param_phone, $param_usertype, $param_status, $param_verification_code);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_email = $email;
        $param_phone = $phone;
        $param_usertype = $usertype;
        $param_status = $status;
        $param_verification_code = $verification_code;

        if (mysqli_stmt_execute($stmt)) {
            // Send verification email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'nasiryt.827@gmail.com';
                $mail->Password = 'mtvp ruzp aqfu tfxt';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('nasiryt.827@gmail.com', 'Food Delivery System');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body = 'Click on the following link to verify your email: <a href="http://localhost/food/verify.php?code=' . $verification_code . '">Verify Email</a>';

                $mail->send();
                echo 'Verification email has been sent.';
            } catch (Exception $e) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }

            echo '<div class="alert alert-success" role="alert">User registered successfully. Please check your email to verify your account.</div>';
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2 class="text-center">User Registration</h2>
        <p class="text-center">Please fill in your details to register.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="number" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>User Type</label>
                <select name="usertype" class="form-control <?php echo (!empty($usertype_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select User Type</option>
                    <option value="Customer">Customer</option>
                    <option value="Delivery Boy">Delivery Boy</option>
                </select>
                <span class="invalid-feedback"><?php echo $usertype_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Register">
            </div>
        </form>

        <p class="text-center">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

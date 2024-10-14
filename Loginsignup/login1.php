<?php
session_start();
include("connection.php");
include("functions.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Regular login form submission
    if (isset($_POST['login'])) {
        $user_name = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($user_name) && !empty($password) && filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
            // Check if user exists in the database
            $query = "SELECT * FROM users WHERE user_email = '$user_name' LIMIT 1";
            $result = mysqli_query($connection, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);

                // Verify the password using password_verify
                if (password_verify($password, $user_data['password'])) {
                    // Password is correct, set session and redirect
                    $_SESSION['id'] = $user_data['id'];
                    header("Location: home.php"); // Redirect to booking page
                    die;
                } else {
                    // Incorrect password
                    $error_message = "Incorrect password!";
                }
            } else {
                // User not found
                $error_message = "User not found!";
            }
        } else {
            // Invalid email format
            $error_message = "Please enter a valid email address including @ and .";
        }
    }

    // Handle password reset
    if (isset($_POST['reset_password'])) {
        $user_name = $_POST['reset_username'];
        $new_password = $_POST['reset_new_password'];

        if (!empty($user_name) && !empty($new_password) && filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
            // Hash the new password before storing it
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $query = "UPDATE users SET password = '$hashed_password' WHERE user_email = '$user_name'";
            $result = mysqli_query($connection, $query);

            if ($result) {
                $error_message = "Password has been updated successfully!";
            } else {
                $error_message = "Failed to update the password!";
            }
        } else {
            $error_message = "Please enter a valid email address and fill in all fields!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
    <style>
    /* Styles for the popup */
    .popup {
        display: none;
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 400px; /* Increased width for better layout */
        background-color: #f9f9f9; /* Lighter background */
        padding: 30px; /* More padding for better spacing */
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }

    .popup h3 {
        margin-bottom: 20px; /* Space below the title */
        text-align: center; /* Centered title */
    }

    .popup label {
        font-weight: bold; /* Bold labels for clarity */
        margin-bottom: 5px; /* Space between label and input */
    }

    .popup input[type="email"],
    .popup input[type="password"] {
        width: 100%; /* Full width inputs */
        padding: 10px; /* Increased padding for comfort */
        margin-bottom: 15px; /* Space below inputs */
        border: 1px solid #ccc; /* Light border */
        border-radius: 4px; /* Rounded corners */
        box-sizing: border-box; /* Ensure padding is included in width */
    }

    .popup input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        width: 100%; /* Full width button */
        border-radius: 4px; /* Rounded corners for button */
        font-size: 16px; /* Larger font for button */
    }

    .popup input[type="submit"]:hover {
        background-color: #45a049; /* Darker green on hover */
    }

    .popup button {
        background-color: rgb(27, 67, 154);
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        border-radius: 4px; /* Rounded corners */
        width: 100%; /* Full width button */
        margin-top: 10px; /* Space above close button */
    }

    .popup button:hover {
        background-color: #007bff;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
    .error-message{
        color: #e53935;
    }
    </style>
</head>
<body>
    <header>
        <div class="container">
           <div class="logo">CodeBegin</div>
           <nav>
               <ul>
                   <li><a href="../CODEBEGIN/group10/Homepage/home.html">Home</a></li>
                   <li><a href="../CODEBEGIN/group10/Homepage/course.php">Courses</a></li>
                   <li><a href="../CODEBEGIN/group10/Homepage/tutor.php">Tutors</a></li>
                   <li><a href="../CODEBEGIN/group10/Homepage/contactus.html">Contact Us</a></li>
               </ul>
           </nav> 
        </div>
    </header>

    <section class="login">
        <div class="login-container">
            <h2>Login</h2>
            
            <!-- Display error message if present -->
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            
            <form method="POST">
                <label for="username">Email</label>
                <input type="text" id="username" name="username" required placeholder="Enter your email">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">

                <input type="submit" name="login" value="Login">
            </form>

            <div class="forgot-password">
                <p>Forgot your password? <a href="#" onclick="showResetPopup()">Reset it</a></p>
            </div>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup1.php">Sign up here</a></p>
            </div>
        </div>
    </section>

    <!-- Password Reset Popup -->
    <!-- Password Reset Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="popup" id="resetPopup">
        <h3>Reset Password</h3>
        <form method="POST">
            <label for="reset_username">Email</label>
            <input type="email" id="reset_username" name="reset_username" required placeholder="Enter your email">
            
            <label for="reset_new_password">New Password</label>
            <input type="password" id="reset_new_password" name="reset_new_password" required placeholder="Enter new password">
            
            <input type="submit" name="reset_password" value="Reset Password">
        </form>
        <button onclick="closeResetPopup()">Close</button>
    </div>


    <script>
        function showResetPopup() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('resetPopup').style.display = 'block';
        }

        function closeResetPopup() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('resetPopup').style.display = 'none';
        }
    </script>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <div class="quick-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Courses</a></li>
                    <li><a href="#">Tutors</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="social-media">
                <h3>Follow Us</h3>
                <ul class="social-icons">
                    <li><a href="#"><img src="linkedin-big-logo.png" alt="LinkedIn"></a></li>
                    <li><a href="#"><img src="twitter.png" alt="Twitter"></a></li>
                    <li><a href="#"><img src="github.png" alt="GitHub"></a></li>
                </ul>
            </div>

            <div class="contact-info">
                <h3>Contact Us</h3>
                <p>Email: info@codebegin.com</p>
                <p>Phone: +63 912 345 6789</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 Codebegin. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>

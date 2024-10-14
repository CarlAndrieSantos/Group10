<?php
session_start();
include("connection.php");

// Fetch Tutors and Availability from the database
$query_tutors = "SELECT tutor_id, tutor_name, available_date FROM tutors";
$tutors_result = mysqli_query($connection, $query_tutors);

$query_courses = "SELECT course_id, courses FROM courses";
$courses_result = mysqli_query($connection, $query_courses);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if the user is logged in
    if (isset($_SESSION['id'])) {
        // Collect form data
        $user_id = $_SESSION['id'];  // Get user ID from session
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['user_phone'];
        $user_address = $_POST['user_address'];
        $age = $_POST['user_age'];
        $gender = $_POST['user_gender'];
        $available_date = $_POST['available_date'];
        $prepared_time = $_POST['prepared_time'];
        $tutor_id = $_POST['tutor_id'];
        $course_id = $_POST['course'];
        $payment_method = $_POST['payment_method'];

        // Insert into bookings table
        $query = "INSERT INTO bookings (id, first_name, last_name, user_phone, user_address, user_age, user_gender, prepared_time, course_id, tutor_id, available_date, payment_method)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the query
        if ($stmt = mysqli_prepare($connection, $query)) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "issisissiiss",$user_id, $first_name, $last_name, $phone, $user_address, $age, $gender, $prepared_time, $course_id, $tutor_id, $available_date, $payment_method);
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $booking_successful = true; // Set flag to true if booking is successful
            } else {
                echo "Error: " . mysqli_stmt_error($stmt);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($connection);
        }
    } else {
        echo "You need to log in to make a booking.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="booking_5.css">
    <link rel="shortcut icon" href="android-chrome-512x512.png" type="image/x-icon">
    <title>Book a Session</title>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">CodeBegin</div>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="course.php">Courses</a></li>
                    <li class="courses"><a href="tutor.php">Tutors</a></li>
                    <li><a href="contactus.html">Contact Us</a></li>
                    <li class="userlogo"><a href="logout.php">Logout</a></li>
                </ul>
            </nav> 
        </div>
    </header>
    
    <!-- Booking Form -->
    <form method="post">
        <div class="container_booking">
            <h2>Book Your Session</h2>
            <h3>Enter Your Information</h3>
            <div class="name">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" placeholder="Enter your first name" required><br><br>
                
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" placeholder="Enter your last name" required><br><br>
            </div>
            
            <div class="info">
               <label for="phone">Phone Number:</label>
                <input type="tel" name="user_phone" placeholder="Enter your phone number" required><br><br>

                <label for="age">Age:</label>
                <input type="number" name="user_age" placeholder="Enter your age" required><br><br>
            </div>

            <div class="info2">  
                <label for="gender">Gender:</label>
                <select name="user_gender" id="gender" required>
                    <option value="" selected disabled>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select><br><br>          
                
                <label for="prepared_time">Preferred Time:</label>
                <select name="prepared_time" id="preferred_time" required>
                    <option value="" selected disabled>Select Time</option>
                    <option value="Morning (9:00 AM - 12:00 PM)">Morning (9:00 AM - 12:00 PM)</option>
                    <option value="Afternoon (1:00 PM - 4:00 PM)">Afternoon (1:00 PM - 4:00 PM)</option>
                    <option value="Evening (6:00 PM - 9:00 PM)">Evening (6:00 PM - 9:00 PM)</option>
                </select>  
            </div>
            
            <div class="address">
                <label for="user_address">Address:</label>
                <input type="text" name="user_address" placeholder="Enter your address" required>
            </div><br>

            <div class="choose">
               <label for="tutor_id">Choose a Tutor:</label>
               <select name="tutor_id" id="tutor_id" required>
                <option value="" selected disabled>Select Tutor</option>
                <?php
                while ($row = mysqli_fetch_assoc($tutors_result)) {
                    echo "<option value='{$row['tutor_id']}' data-available-date='{$row['available_date']}'>{$row['tutor_name']} - {$row['available_date']}</option>";
                }
                ?>
                </select>

            <input type="hidden" name="available_date" id="available_date" value="">


            <label for="course">Courses:</label>
            <select name="course" id="courses" required>
                <option value="" selected disabled>Select Course</option>
                <?php
                while ($row = mysqli_fetch_assoc($courses_result)) {
                    echo "<option value='{$row['course_id']}'>{$row['courses']}</option>";
                }
                ?>
            </select>
        </div><br>

        <div class="payment">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="" selected disabled>Select Payment Method</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Paypal">PayPal</option>
                <option value="Gcash">GCash</option>
            </select><br><br>   
        </div>
        <div class="terms">
            <input type="checkbox" id="terms" name="terms" required>
            <label for="terms">I accept the <a href="#" id="termsLink">Terms and Conditions</a></label><br><br>
            
        </div>
        <button type="submit" name="submit">Submit Booking</button>
    </form> 
    


    <!-- Modal for Terms and Conditions -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Terms and Conditions</h2>
            <p>Here are the terms and conditions of the service. Please read carefully.</p>
            <ul>
                <li>All sessions must be booked at least 24 hours in advance.</li>
                <li>Cancellation must be done 12 hours before the scheduled session.</li>
                <li>Payments are non-refundable unless the tutor cancels the session.</li>
                <li>By booking, you agree to follow the guidelines set by the tutor.</li>
                <li>Any misconduct during the session can lead to a ban from future sessions.</li>
            </ul>
        </div>
    </div>
    <!-- Modal for Booking Success -->
    <div id="successModal" class="modal2">
        <div class="modal2-content2"> <!-- Use consistent class names for styling -->
            <span class="close2">&times;</span>
            <h2>Booking Successful</h2>
            <p>Your booking has been successfully completed. You will receive a confirmation email shortly.</p>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
    // Get the modal
    var modal = document.getElementById("termsModal");

    // Get the link that opens the modal
    var termsLink = document.getElementById("termsLink");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the link, open the modal
    termsLink.onclick = function(event) {
        event.preventDefault(); // Prevent the default action
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
   <!-- JavaScript for Modal -->
    <script>
        // Get the modal for success
        var successModal = document.getElementById("successModal");

        // Get the <span> element that closes the success modal
        var closeSuccessModal = document.getElementsByClassName("close2")[0];

        // Show the success modal if booking was successful
        <?php if (isset($booking_successful) && $booking_successful): ?>
            successModal.style.display = "block";
        <?php endif; ?>

        // Function to redirect to home
        function redirectToHome() {
            window.location.href = "home.php"; // Change this to your home page URL
        }

        // When the user clicks on <span> (x), close the success modal and redirect
        closeSuccessModal.onclick = function() {
            successModal.style.display = "none";
            redirectToHome(); // Redirect to home after closing
        }

        // When the user clicks anywhere outside of the success modal, close it and redirect
        window.onclick = function(event) {
            if (event.target == successModal) {
                successModal.style.display = "none";
                redirectToHome(); // Redirect to home after closing
            }
        }
    </script>
    <script>
        document.getElementById('tutor_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var availableDate = selectedOption.getAttribute('data-available-date');
        document.getElementById('available_date').value = availableDate;
    });

    </script>


</body>
</html>

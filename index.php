<?php
// Include database connection
include('db.php'); // Ensure this file exists and is correctly configured

$message = ""; // Default message is empty

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Get registration form inputs
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

        // Check if the email already exists
        $check_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Email already exists
            $message = "Error: The email is already registered!";
        } else {
            // Prepare an insert statement for registration
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);

            // Execute the statement
            if ($stmt->execute()) {
                $message = "Registration successful!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }

        $check_stmt->close();
    }

    if (isset($_POST['login'])) {
        // Get login form inputs
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare a select statement for login
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        // Check if email exists
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            
            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Redirect to index.html on successful login
                header("Location: index.html");
                exit(); // Ensure no further code is executed
            } else {
                $message = "Invalid password!";
            }
        } else {
            $message = "No account found with that email!";
        }
        $stmt->close();
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styleOf.css">
    <title>RideMe</title>
</head>
<body>

    <?php if ($message): ?>
        <div id="popup-banner" class="popup-banner">
            <p><?php echo $message; ?></p>
        </div>
    <?php endif; ?>

    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <p>NIKHIL</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#" class="link active"><i class='bx bx-home'></i> Home</a></li>
                    <!-- <li><a href="#" class="link"><i class='bx bx-edit'></i> Vehicles</a></li>
                    <li><a href="#" class="link"><i class='bx bx-cog'></i> Services</a></li> -->
                    <li><a href="about1.html" class="link"><i class='bx bx-info-circle'></i> About</a></li>
                </ul>
            </div>
            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="login()">Sign In</button>
                <button class="btn" id="registerBtn" onclick="register()">Sign Up</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <div class="form-box">
            <!-- Login Form -->
            <div class="login-container" id="login">
                <div class="top">
                    <span>Don't have an account? <a href="#" onclick="register()">Sign Up</a></span>
                    <header>Login</header>
                </div>
                <form method="POST" action="">
                    <div class="input-box">
                        <input type="text" class="input-field" name="email" placeholder="Email" required>
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="password" placeholder="Password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <input type="submit" class="submit" name="login" value="Sign In">
                    </div>
                    <div class="two-col">
                        <div class="one">
                            <input type="checkbox" id="login-check">
                            <label for="login-check"> Remember Me</label>
                        </div>
                        <div class="two">
                            <label><a href="#">Forgot password?</a></label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div class="register-container" id="register">
                <div class="top">
                    <span>Have an account? <a href="#" onclick="login()">Login</a></span>
                    <header>Sign Up</header>
                </div>
                <form method="POST" action="">
                    <div class="two-forms">
                        <div class="input-box">
                            <input type="text" class="input-field" name="firstname" placeholder="Firstname" required>
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="input-box">
                            <input type="text" class="input-field" name="lastname" placeholder="Lastname" required>
                            <i class="bx bx-user"></i>
                        </div>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="email" placeholder="Email" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" class="input-field" name="password" placeholder="Password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box">
                        <input type="submit" class="submit" name="register" value="Register">
                    </div>
                    <div class="two-col">
                        <div class="one">
                            <input type="checkbox" id="register-check">
                            <label for="register-check"> Remember Me</label>
                        </div>
                        <div class="two">
                            <label><a href="#">Terms & conditions</a></label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Banner pop-up close after 3 seconds
        window.onload = function() {
            var popupBanner = document.getElementById("popup-banner");
            if (popupBanner) {
                setTimeout(function() {
                    popupBanner.style.display = "none";
                }, 3000);
            }
        };

        function myMenuFunction() {
            var i = document.getElementById("navMenu");
            if (i.className === "nav-menu") {
                i.className += " responsive";
            } else {
                i.className = "nav-menu";
            }
        }

        var a = document.getElementById("loginBtn");
        var b = document.getElementById("registerBtn");
        var x = document.getElementById("login");
        var y = document.getElementById("register");

        function login() {
            x.style.left = "4px";
            y.style.right = "-520px";
            a.className += " white-btn";
            b.className = "btn";
            x.style.opacity = 1;
            y.style.opacity = 0;
        }

        function register() {
            x.style.left = "-510px";
            y.style.right = "5px";
            a.className = "btn";
            b.className += " white-btn";
            x.style.opacity = 0;
            y.style.opacity = 1;
        }
    </script>

    <style>
        /* Popup Banner Styling */
        #popup-banner {
            background-color: #f44336;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            top: 20px;
            width: 100%;
            z-index: 999;
            display: none; /* Initially hidden */
        }
    </style>
</body>
</html>

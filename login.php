<?php
	include ("settings.php");
	
	session_start();
	
	$error_msg = array();
	
	// Connect to database
	$conn = @mysqli_connect($host, $user, $pass, $dbnm);
	if (!$conn) {
		$error_msg[] = "The database server cannot be connected to: " . mysqli_connect_error() . "<br/>";
	}
	
	// Check if email exists in database
	function check_email_exist ($email, $conn) {
		$email = mysqli_real_escape_string($conn, $email);
		$query = "SELECT friend_email FROM friends WHERE friend_email = '$email'";
		$result = mysqli_query($conn, $query);
		
		if (!$result) {
			$error_msg[] = "An error occurred while checking the email: " . mysqli_error($conn) . "<br/>";
			return;
		}
		
		if (mysqli_num_rows($result) === 0) {
			$error_msg[] = "Email does not exist in our records. Please try again. <br/>";
		}
	}
	
	// Check if form is submitted
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Verify user inputs
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		// If data is valid, verify the password
		if (empty($error_msg)) {
			$email = mysqli_real_escape_string($conn, $email);
			$password = mysqli_real_escape_string($conn, $password);

			$query = "SELECT password FROM friends WHERE friend_email = '$email'";
			$result = mysqli_query($conn, $query);

			if (!$result) {
				$error_msg[] = "An error occurred while checking the password: " . mysqli_error($conn) . "<br/>";
			} else {
				if (mysqli_num_rows($result) === 1) {
					$row = mysqli_fetch_assoc($result);
					$saved_password = $row['password'];

					if (strpos($password, $saved_password) !== false) {
						// Set session status to successful login
						$_SESSION["status"] = "success";
						$_SESSION["friend_email"] = $email;
						$_SESSION["profile_name"] = $profile;
						
						// Redirect to 'friendlist.php'
						header("Location: friendlist.php");
						exit();
					} else {
						$error_msg[] = "Invalid password. Please try again. <br/>";
					}
				} else {
					$error_msg[] = "Unexpected error occurred: " . mysqli_error($conn) . "<br/>";
				}
			}
		}
	}
?>
	
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Web Programming :: Assignment 2">
    <meta name="keywords" content="HTML, PHP">
    <meta name="author" content="Nguyen Dang Anh">
    <title>Login Page</title>
    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #FFD68A;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        /* Page container */
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
            padding: 20px;
        }

		/* Center align the text */
        h2, h3, p {
            text-align: center;
        }
		
        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"],
        input[type="reset"] {
            padding: 8px 16px;
            margin-top: 10px;
            background-color: #FFA500;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            background-color: #A36A00;
        }

		/* Login and Clear buttons container */
        .btn-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        button[type="submit"],
        button[type="reset"] {
            color: white;
            border: none;
            padding: 8px 14px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Login & Clear button */
        button[type="submit"],
		button[type="reset"] {
            background-color: #FF2E2E;
            color: white;
        }

        button[type="submit"]:hover,
        button[type="reset"]:hover {
            background-color: #A30000;
        }
		
        /* Link styles */
        a {
            margin-top: 10px;
            color: #2196F3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Error message style */
        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
		
		/* Signup and Index links */
        .action-links {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .action-links a {
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: white;
        }
        .action-links a.signup-btn {
            background-color: #FF0000;
            margin-right: 10px;
        }
        .action-links a.signup-btn:hover {
            background-color: #CC0000;
        }
        .action-links a.index-btn {
            background-color: #0000FF;
        }
        .action-links a.index-btn:hover {
            background-color: #0000A3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Login Page</h2>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

			<div class="btn-container">
				<button type="submit" style="background-color: #FFA500; color: white;">Login</button>
				<button type="reset" style="background-color: #FFA500; color: white;">Clear</button>
			</div>
        </form>

		<div class="action-links">
			<a href="signup.php" class="signup-btn">Register here</a>
			<a href="index.php" class="index-btn">Home</a>
		</div>
		
        <?php
        // Display error messages if there are any
        if (!empty($error_msg)) {
            echo '<div class="error">' . implode("<br>", $error_msg) . '</div>';
        }
        ?>
    </div>
</body>
</html>
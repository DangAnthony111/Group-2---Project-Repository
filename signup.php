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
		
		if (mysqli_num_rows($result) > 0) {
			$error_msg[] = "Email is already registered. Please try again. <br/>";
		}
	}
	
	// Check if form is submitted
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Verify user inputs
		$email = $_POST['email'];
		$profile = $_POST['profile'];
		$password = $_POST['password'];
		$cf_password = $_POST['cf_password'];
	
		//Validate email format
		$valid_domains = ["@gmail", "@yahoo", "@hotmail"];
		$valid_email = false;
		foreach ($valid_domains as $domain) {
			if (strpos($email, $domain) !== false) {
				$valid_email = true;
				break;
			}
		}
		if (!$valid_email) {
			$error_msg[] = "Invalid email format. Please try again. <br/>";
		}
			
		// Validate profile format
		if (!preg_match("/^([A-Za-z][\s]*){1,30}$/", $profile)) {
			$error_msg[] = "Profile must contain only letters. <br/>";
		}
		
		// Validate password format
		if (!preg_match("/^(\w*){1,20}$/", $password)) {
			$error_msg[] = "Password must contain only letters and numbers. <br/>";
		}
		if ($password !== $cf_password) {
			$error_msg[] = "Passwords do not match. <br/>";
		}
		
		// If data is valid, insert into database
		if (empty($error_msg)) {
			$email = mysqli_real_escape_string($conn, $email);
			$password = mysqli_real_escape_string($conn, $password);
			$profile = mysqli_real_escape_string($conn, $profile);
			$date = date('Y-m-d');

			$query = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) 
					  VALUES ('$email', '$password', '$profile', '$date', 0)";
			$result = mysqli_query($conn, $query);
			
			if (!$result) {
				$error_msg[] = "An error occurred while inserting the data: " . mysqli_error($conn) . "<br/>";
				return;
			}

			// Set session status to successful log in
			$_SESSION["status"] = "success";
			$_SESSION["friend_email"] = $email;
			$_SESSION["profile_name"] = $profile;
			
			// Redirect to 'friendadd.php'
			header("Location: friendadd.php");
			exit();
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
    <title>Registration Page</title>
    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #FF8A8A;
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

        /* Register and Clear buttons container */
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

        /* Register & Clear button */
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
		
		/* Login and Index links */
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
        .action-links a.login-btn {
            background-color: #FFA500;
            margin-right: 10px;
        }
        .action-links a.login-btn:hover {
            background-color: #FF8C00;
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
        <h2>Registration Page</h2>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <label for="profile">Profile Name:</label>
            <input type="text" id="profile" name="profile" value="<?php echo isset($profile) ? htmlspecialchars($profile) : ''; ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="cf_password">Confirm Password:</label>
            <input type="password" id="cf_password" name="cf_password" required>

            <div class="btn-container">
                <button type="submit" style="background-color: #FF2E2E; color: white;">Register</button>
                <button type="reset" style="background-color: #FF2E2E; color: white;">Clear</button>
            </div>
        </form>

		<div class="action-links">
			<a href="login.php" class="login-btn">Already have an account?</a>
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
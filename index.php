<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Web Programming :: Assignment 2">
    <meta name="keywords" content="HTML, PHP">
    <meta name="author" content="Nguyen Dang Anh">
    <title>My Friends System - Index</title>
    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #ADD8E6;
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
            max-width: 600px;
            padding: 20px;
        }

        /* Center align the text */
        h2, p {
            text-align: center;
        }

        /* Navigation links */
        .navigation a {
            display: block;
            margin: 10px 0;
            padding: 8px 16px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        /* Sign-Up button */
        .navigation a:nth-child(1) {
            background-color: #FF0000;
        }

        /* Log-In button */
        .navigation a:nth-child(2) {
            background-color: #FFA500;
        }

        /* About button */
        .navigation a:nth-child(3) {
            background-color: #808080;
        }

        /* Hover effect for each button */
        .navigation a:nth-child(1):hover {
            background-color: #CC0000;
        }

        .navigation a:nth-child(2):hover {
            background-color: #FF8C00;
        }

        .navigation a:nth-child(3):hover {
            background-color: #505050;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>My Friends System</h2>

        <div class="user-info">
            <p>Name: Nguyen Dang Anh</p>
            <p>Student ID: 103809611</p>
            <p>Email: <a href="mailto:103809611@student.swin.edu.au">103809611@student.swin.edu.au</a></p>
        </div>

        <div class="declaration">
            <p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</p>
        </div>

        <div class="navigation">
            <a href="signup.php">Sign-Up</a>
            <a href="login.php">Log-In</a>
            <a href="about.php">About</a>
        </div>
    </div>
</body>
</html>

<?php
	include ("settings.php");
	
	// Connect to database
	$conn = @mysqli_connect($host, $user, $pass, $dbnm);
	if (!$conn) {
		$error_msg[] = "The database server cannot be connected to: " . mysqli_connect_error() . "<br/>";
	}
	
	// Create database tables if they don't exist
	if ($conn) {
		$friends_table = "CREATE TABLE IF NOT EXISTS friends (
			friend_id INT AUTO_INCREMENT PRIMARY KEY,
			friend_email VARCHAR(50) NOT NULL UNIQUE,
			password VARCHAR(255) NOT NULL,
			profile_name VARCHAR(30) NOT NULL,
			date_started DATE NOT NULL,
			num_of_friends INT UNSIGNED
		)";
		$table_1 = mysqli_query($conn, $friends_table);

		$myfriends_table = "CREATE TABLE IF NOT EXISTS myfriends (
			friend_id1 INT NOT NULL,
			friend_id2 INT NOT NULL,
			PRIMARY KEY (friend_id1, friend_id2),
			FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
			FOREIGN KEY (friend_id2) REFERENCES friends(friend_id)
		)";
		$table_2 = mysqli_query($conn, $myfriends_table);
		
		if ($table_1 && $table_2) {
			echo "Successfully create tables. <br/>";
		} else {
			echo "Failed to create tables. <br/>";
		}
	}
	
	// Populate 'friends' table with sample data
	$friends_add = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES
		('jdoe@gmail.com', 'R4nd0mP@ss', 'John Doe', '2023-05-15', '3'),
		('msmith@yahoo.com', 'P@ssw0rd42', 'Mary Smith', '2023-06-10', '4'),
		('jperez@hotmail.com', 'Secur3P@ss', 'Juan Perez', '2023-07-02', '2'),
		('kwilson@gmail.com', 'MyP@ssw0rd20', 'Karen Wilson', '2023-04-28', '3'),
		('rthomas@yahoo.com', 'R@nd0m123', 'Robert Thomas', '2023-07-19', '2'),
		('jlee@hotmail.com', 'L33tP@ss', 'Jennifer Lee', '2023-06-02', '2'),
		('bnguyen@gmail.com', 'B3tterP@ss', 'Binh Nguyen', '2023-05-07', '2'),
		('cwang@yahoo.com', 'P@ss1234', 'Chen Wang', '2023-07-12', '1'),
		('emiller@hotmail.com', 'M!ll3rP@ss', 'Emily Miller', '2023-04-23', '1'),
		('tkim@gmail.com', 'K!mP@ssw0rd', 'Tae Kim', '2023-07-09', '0')";
		
	$friends_result = @mysqli_query($conn, $friends_add);
	
	if ($friends_result) {
		echo "Successfully insert sample friends. <br/>";
	} else {
		echo "Failed to insert sample friends. <br/>";
	}
	
	// Populate 'myfriends' table with sample data
	$myfriends_add = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES
		(1, 3),
		(2, 3),
		(1, 5),
		(3, 7),
		(2, 9),
		(5, 6),
		(7, 8),
		(4, 9),
		(5, 10),
		(1, 10),
		(2, 7),
		(6, 9),
		(8, 9),
		(2, 4),
		(4, 5),
		(4, 7),
		(6, 10),
		(7, 9),
		(9, 10),
		(3, 9)";
		
	$myfriends_result = @mysqli_query($conn, $myfriends_add);
	
	if ($myfriends_result) {
		echo "Successfully insert sample friendships. <br/>";
	} else {
		echo "Failed to insert sample friendships. <br/>";
	}
?>

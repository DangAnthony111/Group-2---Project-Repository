<?php
	include ("settings.php");

	session_start();

	$error_msg = array();

	// Connect to database
	$conn = @mysqli_connect($host, $user, $pass, $dbnm);
	if (!$conn) {
		$error_msg[] = "The database server cannot be connected to: " . mysqli_connect_error() . "<br/>";
	}

	// Check if the user is logged in
	if (!isset($_SESSION["status"]) || $_SESSION["status"] !== "success") {
		header("Location: login.php");
		exit();
	}

	// Get current logged-in user's email
	$email = $_SESSION["friend_email"];

	// Get current logged-in user's profile name
	$pr_name = $_SESSION["profile_name"];

	// Get the list of registered users who are not friends of logged-in user
	$query = "SELECT DISTINCT friends.profile_name, friends.friend_id FROM friends
				WHERE friends.friend_email <> '$email'
				AND NOT EXISTS (
					SELECT 1 FROM myfriends 
					WHERE myfriends.friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
					AND myfriends.friend_id2 = friends.friend_id
				)";
	$result = mysqli_query($conn, $query);

	if (!$result) {
		$error_msg[] = "An error occurred while retrieving the profile name: " . mysqli_error($conn) . "<br/>";
		return;
	}

	// Check if friend_id is submitted
	if (isset($_POST["friend_id"])) {
		$friend_id = $_POST["friend_id"];

		// Check if provided friend_id is defined
		$query = "SELECT friend_id2 FROM myfriends 
				  WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
				  AND friend_id2 = '$friend_id'";
		$result = mysqli_query($conn, $query);

		if (!$result) {
			$error_msg[] = "An error occurred while checking the friend: " . mysqli_error($conn) . "<br/>";
			return false;
		}

		if (mysqli_num_rows($result) === 0) {
			// Add the chosen friend
			$query_add = "INSERT INTO myfriends (friend_id1, friend_id2) 
						VALUES ((SELECT friend_id FROM friends WHERE friend_email = '$email'), '$friend_id')";
			$result_add = mysqli_query($conn, $query_add);

			if (!$result_add) {
				$error_msg[] = "An error occurred while adding the friend: " . mysqli_error($conn) . "<br/>";
				return false;
			}

			// Update number of friends
			$query_update = "UPDATE friends 
						SET num_of_friends = num_of_friends + 1
						WHERE friend_email = '$email'";
			$result_update = mysqli_query($conn, $query_update);

			if (!$result_update) {
				$error_msg[] = "An error occurred while updating friend count: " . mysqli_error($conn) . "<br/>";
				return false;
			}

			mysqli_close($conn);

			header("Location: friendadd.php");
			exit;
		} else {
			mysqli_close($conn);

			// Redirect back to page with an error message
			header("Location: friendadd.php?error=already_friend");
			exit;
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
    <title>Friend Add</title>
	<style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #CBC3E3;
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

        /* Table styles */
        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ccc;
            margin-top: 20px;
        }

        td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            width: 50%;
        }

        td:first-child {
            border-right: 1px solid #ccc;
        }
		
		/* Center align the text */
        h2, h3, p {
            text-align: center;
        }
		
		/* Add Friend button styles */
        .add-friend-btn {
            background-color: #800080;
            color: white;
            border: none;
            padding: 8px 14px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .add-friend-btn:hover {
            background-color: #3c005a;
        }

        /* Friend list and log out links */
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
        .action-links a.friend-list-btn {
            background-color: #4CAF50;
            margin-right: 10px;
        }
        .action-links a.friend-list-btn:hover {
            background-color: #45a049;
        }
        .action-links a.logout-btn {
            background-color: #0000FF;
        }
        .action-links a.logout-btn:hover {
            background-color: #0000A3;
        }
    </style>
</head>

<body>
	<div class="container">
		<h2>Add Friend List Page</h2>
		<h2>Welcome, <?php echo htmlspecialchars($pr_name); ?>!</h2>

		<h3>These are people that can't wait to be your friends!</h3>
		<table>
			<tbody>
				<?php foreach ($result as $row) : ?>
					<tr>
						<td><?php echo $row["profile_name"]; ?></td>
						<td>
							<form action="friendadd.php" method="post" onsubmit="return confirm('Are you sure you want to add this person as a friend?');">
								<input type="hidden" name="friend_id" value="<?php echo $row["friend_id"]; ?>">
								<button type="submit" class="add-friend-btn">Add Friend</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="action-links">
			<a href="friendlist.php" class="friend-list-btn">Friend List</a>
			<a href="logout.php" class="logout-btn">Log out</a>
		</div>
		
		<?php
		// Display error messages if there are any
		if (!empty($error_msg)) {
			echo '<div style="color: red;">' . implode("<br>", $error_msg) . '</div>';
		}
		?>
	</div>
</body>
</html>
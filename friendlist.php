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

    // Get the profile name of the logged-in user from the session
    $email = $_SESSION["friend_email"];
    $query = "SELECT profile_name FROM friends WHERE friend_email = '$email'";
    $result = mysqli_query($conn, $query);
	
	if (!$result) {
		$error_msg[] = "An error occurred while retrieving the profile name: " . mysqli_error($conn) . "<br/>";
		return;
	}
	$row = mysqli_fetch_assoc($result);
	$_SESSION["profile_name"] = $row["profile_name"];
	
	$pr_name = $_SESSION["profile_name"];
	
	// Get the list of current friends for the logged-in user
    $query = "SELECT DISTINCT friends.profile_name, friends.friend_id FROM myfriends
              JOIN friends ON myfriends.friend_id2 = friends.friend_id
              WHERE myfriends.friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')";
    $result = mysqli_query($conn, $query);
	
	if (!$result) {
		$error_msg[] = "An error occurred while retrieving the friend list: " . mysqli_error($conn) . "<br/>";
		return;
	}

    // Remove a friend
	function friends_remove($conn, $friend_id, $email, &$error_msg) {
		$friend_id = mysqli_real_escape_string($conn, $friend_id);

		// Check if provided friend_id is defined
		$query = "SELECT friend_id2 FROM myfriends 
				  WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
				  AND friend_id2 = '$friend_id'";
		$result = mysqli_query($conn, $query);

		if (!$result) {
			$error_msg[] = "An error occurred while checking the friend: " . mysqli_error($conn) . "<br/>";
			return false;
		}
		if (mysqli_num_rows($result) !== 1) {
			$error_msg[] = "Invalid friend_id or not a friend of the current user. <br/>";
			return false;
		}

		// Get the number of friends before removing
		$query_num_friends_before = "SELECT num_of_friends FROM friends WHERE friend_email = '$email'";
		$result_num_friends_before = mysqli_query($conn, $query_num_friends_before);
		$row_num_friends_before = mysqli_fetch_assoc($result_num_friends_before);
		$num_friends_before = (int) $row_num_friends_before['num_of_friends'];

		// Remove the chosen friend
		$query_remove = "DELETE FROM myfriends 
						 WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
						 AND friend_id2 = '$friend_id'";
		$result_remove = mysqli_query($conn, $query_remove);

		if (!$result_remove) {
			$error_msg[] = "An error occurred while removing the friend: " . mysqli_error($conn) . "<br/>";
			return false;
		}

		// Calculate the updated number of friends
		$query_num_friends_after = "SELECT COUNT(*) AS num_of_friends FROM myfriends 
									WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')";
		$result_num_friends_after = mysqli_query($conn, $query_num_friends_after);
		$row_num_friends_after = mysqli_fetch_assoc($result_num_friends_after);
		$num_friends_after = (int) $row_num_friends_after['num_of_friends'];

		// Update the 'friends' table with the new friend count
		$query_update = "UPDATE friends
						 SET num_of_friends = $num_friends_after
						 WHERE friend_email = '$email'
						 AND num_of_friends = $num_friends_before";
		$result_update = mysqli_query($conn, $query_update);

		if (!$result_update) {
			$error_msg[] = "An error occurred while updating friend count: " . mysqli_error($conn) . "<br/>";
			return false;
		}

		return true;
	}																	
	
	// Check if friend_id is submitted and it's not a repeated submission
	if (isset($_POST["friend_id"]) && !isset($friend_removed)) {
		// Get the friend_id from the POST data
		$removed_id = $_POST["friend_id"];

		// Attempt to remove the friend
		if (friends_remove($conn, $removed_id, $_SESSION["friend_email"], $error_msg)) {
			// Set the flag indicating successful friend removal
			$friend_removed = true;
		} else {
			// Set the flag indicating unsuccessful friend removal
			$friend_removed = false;
		}
	}

	// Fetch the updated list of current friends for the logged-in user after removal
	$query = "SELECT DISTINCT friends.profile_name, friends.friend_id FROM myfriends
			  JOIN friends ON myfriends.friend_id2 = friends.friend_id
			  WHERE myfriends.friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')";
	$result = mysqli_query($conn, $query);

	if (!$result) {
		$error_msg[] = "An error occurred while retrieving the friend list: " . mysqli_error($conn) . "<br/>";
		return;
	}
	
	// Query to get the current number of friends
	$query_friends_count = "SELECT COUNT(*) AS num_of_friends FROM myfriends 
							WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')";
	$result_friends_count = mysqli_query($conn, $query_friends_count);
	$row_friends_count = mysqli_fetch_assoc($result_friends_count);
	$num_of_friends = (int) $row_friends_count['num_of_friends'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Web Programming :: Assignment 2">
    <meta name="keywords" content="HTML, PHP">
    <meta name="author" content="Nguyen Dang Anh">
    <title>Friend List</title>
    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #e0ffe0;
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

        /* Unfriend button styles */
        .unfriend-btn {
            background-color: #4CAF50;
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
        .unfriend-btn:hover {
            background-color: #45a049;
        }

        /* Add friends and log out links */
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
        .action-links a.add-friends-btn {
            background-color: #800080;
            margin-right: 10px;
        }
        .action-links a.add-friends-btn:hover {
            background-color: #6a006a;
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
        <h2>My Friend List</h2>
        <h2>Welcome, <?php echo htmlspecialchars($pr_name); ?>!</h2>

        <h3>Current number of friends: <?php echo $num_of_friends; ?></h3>
        <table>
            <tbody>
                <?php foreach ($result as $row) : ?>
                    <tr>
                        <td><?php echo $row["profile_name"]; ?></td>
                        <td>
                            <form action="friendlist.php" method="post" onsubmit="return confirm('Are you sure you want to unfriend this person?');">
                                <input type="hidden" name="friend_id" value="<?php echo $row["friend_id"]; ?>">
                                <button type="submit" class="unfriend-btn">Unfriend</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="action-links">
            <a href="friendadd.php" class="add-friends-btn">Add Friends</a>
            <a href="logout.php" class="logout-btn">Log out</a>
        </div>

        <?php
            // Display error messages if there are any
            if (!empty($error_msg)) {
                echo '<div style="color: red; text-align: center; margin-top: 20px;">' . implode("<br>", $error_msg) . '</div>';
            }
        ?>
    </div>
</body>
</html>

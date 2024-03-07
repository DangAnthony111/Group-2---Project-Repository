<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="description" content="Web Programming :: Assignment 2">
    <meta name="keywords" content="HTML, PHP">
    <meta name="author" content="Nguyen Dang Anh">
    <title>About</title>
    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #a6a6a6;
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
            max-width: 800px;
            padding: 20px;
        }

        /* Center align the text */
        h2 {
            text-align: center;
        }

        /* Images */
        img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
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

        /* Friend list button */
        .navigation a:nth-child(1) {
            background-color: #4CAF50;
        }

        /* Add friend button */
        .navigation a:nth-child(2) {
            background-color: #800080;
        }

        /* Index button */
        .navigation a:nth-child(3) {
            background-color: #0000FF;
        }

        /* Hover effect for each button */
        .navigation a:nth-child(1):hover {
            background-color: #45a049;
        }

        .navigation a:nth-child(2):hover {
            background-color: #6a006a;
        }

        .navigation a:nth-child(3):hover {
            background-color: #0000A3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>About</h2>

        <h3>What tasks you have not attempted or not completed?</h3>
        <p>I managed to complete every task except Extra Challenge.</p>

        <h3>Special Features:</h3>
        <p>Input Validation: I have implemented input validation in friendlist.php to prevent unauthorized access. If the user is not logged in (session status is not set to "success"), they will be redirected to the login.php page using the header("Location: login.php"); function.</p>

        <h3>Which parts did you have trouble with?</h3>
        <p>I really struggled with listing and adding friends, since I could not manage to transfer signup and login info to friend listing and adding.</p>

        <h3>What would you like to do better next time?</h3>
        <p>I would love to join discussions sooner, so that I get better ideas to perform.</p>

        <h3>Assignment 2 discussion:</h3>
        <p>I took part in 1 discussion, which is about updating the friend list in either 1 or 2 ways.</p>

        <img src="./images/image_1.jpg" width="700" height="400">
        <img src="./images/image_2.jpg" width="700" height="400">
        <br/>

        <div class="navigation">
            <a href="friendlist.php">Friend Lists</a>
            <a href="friendadd.php">Add friends</a>
            <a href="index.php">Return to Home Page</a>
        </div>
    </div>
</body>
</html>

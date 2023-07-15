<?php
// login.php

session_start(); // Start the session

$errorMessage = ""; // Initialize the error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Create a connection to the MySQL database
    $conn = new mysqli('192.168.29.101', 'admin','admin',  'TradingDB');

    // Check the connection
    if ($conn->connect_error) {
        $errorMessage = "Connection failed. Please try again later.";
    } else {
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Check the username and password directly
        $sql = "SELECT password FROM Users WHERE username = '$username' ";
        $result = $conn->query($sql);
        $verify = $result->fetch_assoc();
        if(password_verify($password, $verify['password']))
        {
            $sql = "SELECT * FROM Users WHERE username = '$username' ";
            $result = $conn->query($sql);
        }
        else {
            $result = NULL;
        }
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $_SESSION['username'] = $username; 
            $user_id = $row['user_id'];
            $orders = $row['orders'];
            $_SESSION['orders'] = $orders;
            $_SESSION['user_id'] = $user_id;
            header("Location: ./frontPage.php");
            exit();
        } else {
            $errorMessage = "Invalid username or password. Please try again.<br>$verify[0]";
        }

        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e402329cbc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./login.css">
</head>

<body>
    <div class="container">
        <div class="login-box">
            <div class="login-content">
                <h1>Login</h1>
                <form action="login.php" method="POST">
                    <div class="wrap-username">
                        <h2>Username</h2>
                        <span><i class="fa-solid fa-user "></i></span>
                        <label for="username">
                            <input type="text" name="username" id="username" placeholder="Type your Username"
                                value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                        </label>
                    </div>
                    <div class="wrap-password">
                        <h2>Password</h2>
                        <span><i class="fa-solid fa-lock"></i></span>
                        <label for="password">
                            <input type="password" name="password" id="password" placeholder="Type your Password">
                        </label>
                    </div>
                    <button type="submit">LOGIN</button>
                </form>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="error-message">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>

</html>

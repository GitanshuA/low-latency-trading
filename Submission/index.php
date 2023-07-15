<?php
// register.php

session_start(); 

$errorMessage = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli('192.168.29.101', 'admin', 'admin', 'TradingDB');

    if ($conn->connect_error) {
        $errorMessage = "Connection failed. Please try again later.";
    } else {
        $sql = "SELECT * FROM Users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $errorMessage = "Username taken.";
        } else {
            $userID = mt_rand(1000, 9999);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (username, password, user_id, orders) VALUES ('$username', '$hashedPassword', $userID, 0)";
            $conn->query($sql);

            if ($conn->affected_rows > 0) {
                $_SESSION['username'] = $username; 

                header("Location: ./frontPage.php");
                exit();
            } else {
                $errorMessage = "Registration failed. Please try again.";
            }
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
    <title>Registration page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e402329cbc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./register.css">
</head>

<body>
    <div class="container">
        <div class="login-box">
            <div class="login-content">
                <h1>Register</h1>
                <form action="index.php" method="POST">
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
                    <button type="submit">REGISTER AND LOGIN</button>
                </form>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="error-message" style="margin-bottom:15px">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
                    <div class="login-redirect">
                        Already a User? Login <a href="./login.php">Here</a>
                    </div>
            </div>
        </div>
    </div>
</body>

</html>

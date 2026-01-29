<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication System</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
    <div class="container">
        <div class="form-box active" id="login-form">
            <form action="process_register.php" method="post">
                <h2>Login</h2>

                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password"placeholder="Password" required>
                <button type="submit">Log In</button>
                <p>Don't have an Account? <a href="register.php"> Register here</a></p>
            </form>
        </div>
    </div>




</body>
</html>
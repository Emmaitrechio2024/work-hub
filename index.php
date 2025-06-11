<?php
include "connect.php";
session_start();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == "client") {
            header("Location: client_dashboard.php");
        } else {
            header("Location: solutionist_dashboard.php");
        }
        exit();
    } else {
        $msg = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Login</h2>
<form method="post">
    <input type="text" name="username" required placeholder="Username or Email"><br>
    <input type="password" name="password" required placeholder="Password"><br>
    <button type="submit">Login</button>
</form>
<p style="color:red;"><?= $msg ?></p>
<p>Don't have an account? <a href="signup.php">Sign up</a></p>
</body>
</html>
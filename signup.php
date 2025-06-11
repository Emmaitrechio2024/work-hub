<?php
include "connect.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = sha1($_POST['password']);
    $role = $_POST['role'];

    // Check if user already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Username or email already exists.";
    } else {
        $sql = "INSERT INTO users (username, email, password, role)
                VALUES ('$username', '$email', '$password', '$role')";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit();
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Sign Up</h2>
<form method="post">
    <input type="text" name="username" required placeholder="Username"><br>
    <input type="email" name="email" required placeholder="Email"><br>
    <input type="password" name="password" required placeholder="Password"><br>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="client">Client</option>
        <option value="solutionist">Solutionist</option>
    </select><br>
    <button type="submit">Register</button>
</form>
<p style="color:red;"><?= $msg ?></p>
<p>Already have an account? <a href="index.php">Login</a></p>
</body>
</html>

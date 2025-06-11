<?php
session_start();
include "connect.php";

// Redirect if not logged in or not a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'client') {
    header("Location: index.php");
    exit();
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $budget = floatval($_POST['budget']);
    $client_id = $_SESSION['user_id'];

    if ($title && $budget > 0) {
        $sql = "INSERT INTO gigs (client_id, title, description, budget) 
                VALUES ('$client_id', '$title', '$description', '$budget')";
        if (mysqli_query($conn, $sql)) {
            header("Location: client_dashboard.php");
            exit();
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    } else {
        $msg = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post a New Gig</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Post a New Gig</h2>
<form method="post">
    <input type="text" name="title" required placeholder="Gig Title"><br>
    <textarea name="description" placeholder="Gig Description (optional)" rows="4" cols="40"></textarea><br>
    <input type="number" name="budget" required placeholder="Budget (e.g. 50)" step="0.01"><br>
    <button type="submit">Post Gig</button>
</form>
<p style="color:red;"><?= $msg ?></p>
<p><a href="client_dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>

<?php
session_start();
include "connect.php";

// Redirect if not logged in or not a solutionist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'solutionist') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$gig_id = isset($_GET['gig_id']) ? intval($_GET['gig_id']) : 0;

// Check if gig exists and is still open
$check_gig = mysqli_query($conn, "SELECT * FROM gigs WHERE id = $gig_id AND status = 'Open'");
if (mysqli_num_rows($check_gig) == 0) {
    echo "Invalid or unavailable gig.";
    exit();
}

// Prevent duplicate application
$check_app = mysqli_query($conn, "SELECT * FROM applications WHERE gig_id = $gig_id AND solutionist_id = $user_id");
if (mysqli_num_rows($check_app) > 0) {
    echo "You've already applied for this gig.<br><a href='solutionist_dashboard.php'>⬅ Back</a>";
    exit();
}

// Insert application
$sql = "INSERT INTO applications (gig_id, solutionist_id) VALUES ('$gig_id', '$user_id')";
if (mysqli_query($conn, $sql)) {
    echo "Application submitted successfully!<br><a href='solutionist_dashboard.php'>⬅ Back to Gigs</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>


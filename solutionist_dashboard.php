<?php
session_start();
include "connect.php";

// Redirect if not logged in or not a solutionist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'solutionist') {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Get open gigs
$gigs = mysqli_query($conn, "
    SELECT g.*, u.username AS client_name 
    FROM gigs g
    JOIN users u ON g.client_id = u.id
    WHERE g.status = 'Open'
    ORDER BY g.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Solutionist Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Welcome, <?= htmlspecialchars($username) ?> (Solutionist)</h2>
<p><a href="logout.php">Logout</a>  | <a href="solutionist_tasks.php">My Tasks</a></p>

<h3>Available Gigs</h3>
<?php if (mysqli_num_rows($gigs) > 0): ?>
    <table border="1" cellpadding="10" style="margin: 0 auto;">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Budget</th>
            <th>Client</th>
            <th>Posted</th>
            <th>Action</th>
        </tr>
        <?php while ($gig = mysqli_fetch_assoc($gigs)): ?>
            <tr>
                <td><?= htmlspecialchars($gig['title']) ?></td>
                <td><?= htmlspecialchars($gig['description']) ?></td>
                <td>$<?= $gig['budget'] ?></td>
                <td><?= htmlspecialchars($gig['client_name']) ?></td>
                <td><?= $gig['created_at'] ?></td>
                <td><a href="apply_gig.php?gig_id=<?= $gig['id'] ?>">Apply</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No open gigs available right now.</p>
<?php endif; ?>
</body>
</html>

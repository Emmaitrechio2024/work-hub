<?php
session_start();
include "connect.php";

// Ensure client is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'client') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch client's gigs
$gigs = mysqli_query($conn, "
    SELECT g.*, u.username AS solutionist_name
    FROM gigs g
    LEFT JOIN users u ON g.assigned_solutionist = u.id
    WHERE g.client_id = $user_id
    ORDER BY g.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Welcome, <?= htmlspecialchars($username) ?> (Client)</h2>
<p><a href="post_gig.php">+ Post New Gig</a> | <a href="logout.php">Logout</a></p>

<h3>Your Posted Gigs</h3>
<?php if (mysqli_num_rows($gigs) > 0): ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>Title</th>
            <th>Budget</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Action</th>
        </tr>
        <?php while ($gig = mysqli_fetch_assoc($gigs)): ?>
            <tr>
                <td><?= htmlspecialchars($gig['title']) ?></td>
                <td>$<?= $gig['budget'] ?></td>
                <td><?= $gig['status'] ?></td>
                <td><?= $gig['solutionist_name'] ? htmlspecialchars($gig['solutionist_name']) : '—' ?></td>
                <td>
                    <?php if ($gig['status'] == 'open'): ?>
                        <a href="view_gig.php?gig_id=<?= $gig['id'] ?>">👀 View Applicants</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>You haven't posted any gigs yet.</p>
<?php endif; ?>
</body>
</html>

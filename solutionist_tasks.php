<?php
session_start();
include "connect.php";

// Ensure solutionist is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'solutionist') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Mark a gig as completed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_gig'])) {
    $gig_id = intval($_POST['complete_gig']);

    // Ensure gig is assigned to this user and still In Progress
    $check = mysqli_query($conn, "
        SELECT * FROM gigs 
        WHERE id = $gig_id AND assigned_solutionist = $user_id AND status = 'In Progress'
    ");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE gigs SET status = 'Completed' WHERE id = $gig_id");
        $msg = "✅ Gig marked as completed.";
    } else {
        $msg = "⚠️ Invalid action.";
    }
}

// Fetch all assigned gigs
$gigs = mysqli_query($conn, "
    SELECT g.id, g.title, g.description, g.status, g.budget, g.created_at, g.client_id, u.username AS client_name 
    FROM gigs g
    JOIN users u ON g.client_id = u.id
    WHERE g.assigned_solutionist = $user_id
    ORDER BY g.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Gigs - Solutionist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Hello, <?= htmlspecialchars($username) ?> — Your Assigned Gigs</h2>
<p><a href="logout.php">Logout</a></p>

<?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<?php if (mysqli_num_rows($gigs) > 0): ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>Title</th>
            <th>Client</th>
            <th>Budget</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($gig = mysqli_fetch_assoc($gigs)): ?>
            
            <tr>
                <td><?= htmlspecialchars($gig['title']) ?></td>
                <td><?= htmlspecialchars($gig['client_name']) ?></td>
                <td>$<?= $gig['budget'] ?></td>
                <td><?= $gig['status'] ?></td>
                <td>
                    <?php if ($gig['status'] === 'In Progress'): ?>
                        <form method="post" style="display:inline;">
                            <button type="submit" name="complete_gig" value="<?= $gig['id'] ?>">✅ Mark as Completed</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>You haven't been assigned any gigs yet.</p>
<?php endif; ?>

<p><a href="solutionist_dashboard.php">⬅ Back to Browse Gigs</a></p>
</body>
</html>

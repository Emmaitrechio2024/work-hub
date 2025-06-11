<?php
session_start();
include "connect.php";

// Ensure client is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'client') {
    header("Location: index.php");
    exit();
}

$client_id = $_SESSION['user_id'];
$gig_id = isset($_GET['gig_id']) ? intval($_GET['gig_id']) : 0;

// Validate gig ownership
$gig_result = mysqli_query($conn, "SELECT * FROM gigs WHERE id = $gig_id AND client_id = $client_id");
if (mysqli_num_rows($gig_result) == 0) {
    echo "Invalid or unauthorized gig.<br><a href='client_dashboard.php'>⬅ Back</a>";
    exit();
}
$gig = mysqli_fetch_assoc($gig_result);

// Fetch applicants
$applicants = mysqli_query($conn, "
    SELECT a.id AS app_id, u.id AS sol_id, u.username 
    FROM applications a
    JOIN users u ON a.solutionist_id = u.id
    WHERE a.gig_id = $gig_id
");

// Assign solutionist if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
    $solutionist_id = intval($_POST['assign']);
    $gig_id = intval($_POST['gig_id']); 

    $update = mysqli_query($conn, "
        UPDATE gigs 
        SET assigned_solutionist = $solutionist_id, status = 'In Progress' 
        WHERE id = $gig_id
    ");

    if ($update) {
        echo "✅ Solutionist assigned successfully!<br><a href='client_dashboard.php'>⬅ Back</a>";
    } else {
        echo "❌ SQL Error: " . mysqli_error($conn);
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Gig Applicants</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Gig Details</h2>
<p><strong>Title:</strong> <?= htmlspecialchars($gig['title']) ?></p>
<p><strong>Budget:</strong> $<?= $gig['budget'] ?></p>
<p><strong>Description:</strong> <?= nl2br(htmlspecialchars($gig['description'])) ?></p>
<p><strong>Status:</strong> <?= $gig['status'] ?></p>

<h3>Applicants</h3>
<?php if (mysqli_num_rows($applicants) > 0): ?>
    <form method="post">
        <ul>
            <?php while ($row = mysqli_fetch_assoc($applicants)): ?>
                <li>
                    <?= htmlspecialchars($row['username']) ?>
                    <?php if ($gig['status'] == 'open'): ?>
                        <button type="submit" name="assign" value="<?= $row['sol_id'] ?>">Assign</button>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <input type="hidden" name="gig_id" value="<?php echo $gig['id']; ?>">

    </form>
<?php else: ?>
    <p>No applicants yet.</p>
<?php endif; ?>

<p><a href="client_dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>

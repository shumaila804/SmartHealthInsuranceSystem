<?php
include("database.php");

$notifications = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body style="background:#eef3f9;">

<div class="container mt-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">
<h3><i class="bi bi-bell"></i> System Notifications</h3>
</div>

<div class="card-body">

<?php while($row = mysqli_fetch_assoc($notifications)){ ?>

<div class="alert alert-info d-flex justify-content-between align-items-center">
<div>
<i class="bi bi-info-circle"></i>
<?php echo $row['message']; ?>
<br>
<small class="text-muted">For: <?php echo $row['user_role']; ?></small>
</div>

<span class="badge bg-dark">
<?php echo $row['created_at']; ?>
</span>
</div>

<?php } ?>

</div>
</div>

</div>

</body>
</html>
<?php
include("database.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body style="background:#f4f7fb;">

<div class="container mt-4">

<div class="card shadow">

<div class="card-header bg-dark text-white">
<h2>📊 Reports & Analytics</h2>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">
<div class="alert alert-primary">
<h4>Total Claims</h4>
<?php
$r=mysqli_query($conn,"SELECT COUNT(*) total FROM claims");
$d=mysqli_fetch_assoc($r);
echo $d['total'];
?>
</div>
</div>

<div class="col-md-3">
<div class="alert alert-success">
<h4>Approved</h4>
<?php
$r=mysqli_query($conn,"SELECT COUNT(*) total FROM claims WHERE status='Approved'");
$d=mysqli_fetch_assoc($r);
echo $d['total'];
?>
</div>
</div>

<div class="col-md-3">
<div class="alert alert-warning">
<h4>Pending</h4>
<?php
$r=mysqli_query($conn,"SELECT COUNT(*) total FROM claims WHERE status='Pending'");
$d=mysqli_fetch_assoc($r);
echo $d['total'];
?>
</div>
</div>

<div class="col-md-3">
<div class="alert alert-danger">
<h4>Rejected</h4>
<?php
$r=mysqli_query($conn,"SELECT COUNT(*) total FROM claims WHERE status='Rejected'");
$d=mysqli_fetch_assoc($r);
echo $d['total'];
?>
</div>
</div>

</div>

</div>

</div>

</div>

</body>
</html>
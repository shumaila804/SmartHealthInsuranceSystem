<?php
include("database.php");

$logs = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY action_time DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Audit Logs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body style="background:#eef3f9;">

<div class="container mt-4">

<div class="card shadow border-0">
<div class="card-header bg-dark text-white">
<h3><i class="bi bi-journal-check"></i> Audit Logs & Action History</h3>
</div>

<div class="card-body">

<table class="table table-hover table-bordered">
<thead class="table-dark">
<tr>
<th>Log ID</th>
<th>Action Performed</th>
<th>Performed By</th>
<th>Date & Time</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($logs)){ ?>
<tr>
<td><?php echo $row['log_id']; ?></td>
<td><?php echo $row['action_performed']; ?></td>
<td><?php echo $row['performed_by']; ?></td>
<td><?php echo $row['action_time']; ?></td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

</div>

</body>
</html>
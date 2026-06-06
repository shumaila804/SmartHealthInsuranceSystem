<?php
include("../database.php");

$result = null;

if(isset($_POST['search']))
{
    $policy_number = $_POST['policy_number'];

    $query = mysqli_query($conn,
    "SELECT * FROM claims
     WHERE policy_number='$policy_number'
     ORDER BY claim_id DESC");

    $result = $query;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Track Claim</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
background:#eef3f9;
}

.card{
border:none;
box-shadow:0 4px 12px rgba(0,0,0,0.08);
}
</style>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container mt-5">

<div class="card">

<div class="card-header bg-primary text-white">
<h3>
<i class="bi bi-search"></i>
Policyholder Claim Tracking System
</h3>
</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-10">
<input
type="text"
name="policy_number"
class="form-control"
placeholder="Enter Policy Number (Example: POL1001)"
required>
</div>

<div class="col-md-2">
<button
type="submit"
name="search"
class="btn btn-success w-100">

Track

</button>
</div>

</div>

</form>

</div>

</div>

<?php
if($result)
{
?>

<div class="card mt-4">

<div class="card-header bg-dark text-white">
Claim Tracking Results
</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead>

<tr>
<th>ID</th>
<th>Patient</th>
<th>Hospital</th>
<th>Treatment</th>
<th>Amount</th>
<th>Risk</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php
while($row = mysqli_fetch_assoc($result))
{
?>

<tr>

<td>#<?php echo $row['claim_id']; ?></td>

<td><?php echo $row['patient_name']; ?></td>

<td><?php echo $row['hospital_name']; ?></td>

<td><?php echo $row['treatment_type']; ?></td>

<td>
Rs. <?php echo $row['claim_amount']; ?>
</td>

<td>

<?php

if($row['risk_level']=="High")
{
echo "<span class='badge bg-danger'>High Risk</span>";
}
elseif($row['risk_level']=="Medium")
{
echo "<span class='badge bg-warning text-dark'>Medium Risk</span>";
}
else
{
echo "<span class='badge bg-success'>Low Risk</span>";
}

?>

</td>

<td>

<?php

if($row['status']=="Approved")
{
echo "<span class='badge bg-success'>Approved</span>";
}
elseif($row['status']=="Rejected")
{
echo "<span class='badge bg-danger'>Rejected</span>";
}
else
{
echo "<span class='badge bg-warning text-dark'>Pending</span>";
}

?>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>

<?php
}
?>

</div>

</body>
</html>
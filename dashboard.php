<?php
include("database.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$totalClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims"))['total'];
$pendingClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Pending'"))['total'];
$approvedClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Approved'"))['total'];
$rejectedClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Rejected'"))['total'];
$lowRisk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='Low'"))['total'];
$mediumRisk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='Medium'"))['total'];
$highRisk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='High'"))['total'];

$claims = mysqli_query($conn,"SELECT * FROM claims ORDER BY submitted_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<title>Advanced Analytics Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    background:#eef3f9;
    margin:0;
    font-family:Arial, sans-serif;
}
.sidebar{
    width:260px;
    min-height:100vh;
    background:#06154a;
    color:white;
    padding:25px;
    position:fixed;
}
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    margin:18px 0;
    font-size:17px;
}
.sidebar a.active{
    color:#ffc107;
}
.main{
    margin-left:260px;
    padding:28px;
}
.card{
    border:0;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
    border-radius:12px;
}
.chart-box{
    height:260px;
}
</style>
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="sidebar">
<h3 class="mb-4">Smart Health</h3>

<a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Analytics Dashboard</a>
<a href="hospital/submit_claim.php"><i class="bi bi-hospital"></i> Submit Claim</a>
<a href="officer/review_claims.php"><i class="bi bi-shield-check"></i> Officer Review</a>
<a href="policyholder/track_claim.php"><i class="bi bi-search"></i> Track Claim</a>
<a href="admin/manage_users.php"><i class="bi bi-people"></i> Manage Users</a>

<hr>
<a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main">

<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h2 class="fw-bold">Advanced Insurance Analytics Dashboard</h2>
<p class="text-muted">Real-time claim monitoring, fraud intelligence and decision analytics</p>
</div>
<span class="badge bg-primary p-3">Role: <?php echo strtoupper($_SESSION['role']); ?></span>
</div>

<div class="row g-3 mb-4">

<div class="col-md-3">
<div class="card p-3">
<h6 class="text-muted">Total Claims</h6>
<h2><?php echo $totalClaims; ?></h2>
<i class="bi bi-file-medical fs-1 text-primary"></i>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6 class="text-muted">Pending Claims</h6>
<h2><?php echo $pendingClaims; ?></h2>
<i class="bi bi-hourglass-split fs-1 text-warning"></i>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6 class="text-muted">Approved Claims</h6>
<h2><?php echo $approvedClaims; ?></h2>
<i class="bi bi-check-circle fs-1 text-success"></i>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6 class="text-muted">High Fraud Alerts</h6>
<h2><?php echo $highRisk; ?></h2>
<i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
</div>
</div>

</div>

<div class="row g-3 mb-4">

<div class="col-md-6">
<div class="card">
<div class="card-header bg-white fw-bold">
<i class="bi bi-pie-chart"></i> Claim Status Distribution
</div>
<div class="card-body">
<div class="chart-box">
<canvas id="statusChart"></canvas>
</div>
</div>
</div>
</div>

<div class="col-md-6">
<div class="card">
<div class="card-header bg-white fw-bold">
<i class="bi bi-bar-chart"></i> Fraud Risk Analysis
</div>
<div class="card-body">
<div class="chart-box">
<canvas id="riskChart"></canvas>
</div>
</div>
</div>
</div>

</div>

<div class="row g-3">

<div class="col-md-8">
<div class="card">
<div class="card-header bg-white fw-bold">
<i class="bi bi-clock-history"></i> Recent Claim Activity
</div>

<div class="card-body">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Patient</th>
<th>Policy</th>
<th>Amount</th>
<th>Risk</th>
<th>Status</th>
</tr>
</thead>

<tbody>
<?php while($row = mysqli_fetch_assoc($claims)){ ?>
<tr>
<td>#<?php echo $row['claim_id']; ?></td>
<td><?php echo $row['patient_name']; ?></td>
<td><?php echo $row['policy_number']; ?></td>
<td>Rs. <?php echo $row['claim_amount']; ?></td>
<td>
<?php
if($row['risk_level']=="High"){
echo "<span class='badge bg-danger'>High</span>";
}
elseif($row['risk_level']=="Medium"){
echo "<span class='badge bg-warning text-dark'>Medium</span>";
}
else{
echo "<span class='badge bg-success'>Low</span>";
}
?>
</td>
<td><span class="badge bg-secondary"><?php echo $row['status']; ?></span></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card">
<div class="card-header bg-white fw-bold">
<i class="bi bi-cpu"></i> Fraud Intelligence Engine
</div>
<div class="card-body">

<p><b>Risk Scoring Rules:</b></p>
<ul>
<li>Claim exceeds coverage: +40</li>
<li>Expired policy: +30</li>
<li>Non-eligible hospital: +20</li>
<li>Duplicate claim pattern: +10</li>
</ul>

<div class="alert alert-danger py-2">
High risk claims require manual officer verification.
</div>

<div class="alert alert-info py-2">
Supports monitoring, decision making, audit control and fraud analysis.
</div>

</div>
</div>
</div>

</div>

</div>

<script>
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            data: [<?php echo $pendingClaims; ?>, <?php echo $approvedClaims; ?>, <?php echo $rejectedClaims; ?>],
            backgroundColor: ['#ffc107', '#198754', '#dc3545']
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false
    }
});

new Chart(document.getElementById('riskChart'), {
    type: 'bar',
    data: {
        labels: ['Low Risk', 'Medium Risk', 'High Risk'],
        datasets: [{
            label: 'Claims',
            data: [<?php echo $lowRisk; ?>, <?php echo $mediumRisk; ?>, <?php echo $highRisk; ?>],
            backgroundColor: ['#198754', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        scales:{
            y:{
                beginAtZero:true
            }
        }
    }
});
</script>


</body>
</html>
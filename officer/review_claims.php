<?php
include("../database.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['action']) && isset($_GET['id']))
{
    $claim_id = $_GET['id'];
    $action = $_GET['action'];

    if($action == "approve"){
        $status = "Approved";
        $message = "Claim approved by insurance officer";
    } else {
        $status = "Rejected";
        $message = "Claim rejected due to fraud/eligibility risk";
    }

    mysqli_query($conn,"UPDATE claims SET status='$status' WHERE claim_id='$claim_id'");

    mysqli_query($conn,
    "INSERT INTO audit_logs(action_performed, performed_by)
     VALUES('$message for Claim ID $claim_id','".$_SESSION['name']."')");

    mysqli_query($conn,
    "INSERT INTO notifications(message,user_role)
     VALUES('$message for Claim ID $claim_id','policyholder')");

    header("Location: review_claims.php");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$risk = isset($_GET['risk']) ? $_GET['risk'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT * FROM claims WHERE 1=1";

if($search != ''){
    $sql .= " AND (
        patient_name LIKE '%$search%'
        OR policy_number LIKE '%$search%'
        OR hospital_name LIKE '%$search%'
    )";
}

if($risk != ''){
    $sql .= " AND risk_level='$risk'";
}

if($statusFilter != ''){
    $sql .= " AND status='$statusFilter'";
}

$sql .= " ORDER BY submitted_at DESC";

$claims = mysqli_query($conn,$sql);

$totalClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims"))['total'];
$pendingClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Pending'"))['total'];
$approvedClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Approved'"))['total'];
$highRiskClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='High'"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Officer Claim Review</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body style="background:#f4f7fb;">

<div class="container-fluid">

<div class="row">

<div class="col-md-2 min-vh-100 text-white p-4" style="background:#071952;">
<h4>Officer Panel</h4>
<hr>

<a href="../dashboard.php" class="text-white d-block mb-3 text-decoration-none">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="review_claims.php" class="text-warning d-block mb-3 text-decoration-none">
<i class="bi bi-shield-check"></i> Review Claims
</a>

<a href="../notifications.php" class="text-white d-block mb-3 text-decoration-none">
<i class="bi bi-bell"></i> Notifications
</a>

<a href="../audit_logs.php" class="text-white d-block mb-3 text-decoration-none">
<i class="bi bi-journal-check"></i> Audit Logs
</a>

<a href="../logout.php" class="text-white d-block mb-3 text-decoration-none">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</div>

<div class="col-md-10 p-4">

<div class="d-flex justify-content-between align-items-center mb-4">
<div>
<h2 class="fw-bold">Advanced Insurance Officer Claim Review</h2>
<p class="text-muted">Search, filter, analyze fraud score and perform claim decision</p>
</div>
<span class="badge bg-primary p-3">Role: OFFICER</span>
</div>

<div class="row g-3 mb-4">

<div class="col-md-3">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h6 class="text-muted">Total Claims</h6>
<h2><?php echo $totalClaims; ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h6 class="text-muted">Pending Claims</h6>
<h2><?php echo $pendingClaims; ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h6 class="text-muted">Approved Claims</h6>
<h2><?php echo $approvedClaims; ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h6 class="text-muted">High Risk Claims</h6>
<h2><?php echo $highRiskClaims; ?></h2>
</div>
</div>
</div>

</div>

<div class="card border-0 shadow-sm mb-4">
<div class="card-header bg-white fw-bold">
<i class="bi bi-funnel"></i> Search & Filter Claims
</div>

<div class="card-body">

<form method="GET" class="row g-3">

<div class="col-md-4">
<input type="text"
name="search"
class="form-control"
placeholder="Search Patient / Policy / Hospital"
value="<?php echo $search; ?>">
</div>

<div class="col-md-3">
<select name="risk" class="form-control">
<option value="">All Risk Levels</option>
<option value="Low" <?php if($risk=="Low") echo "selected"; ?>>Low</option>
<option value="Medium" <?php if($risk=="Medium") echo "selected"; ?>>Medium</option>
<option value="High" <?php if($risk=="High") echo "selected"; ?>>High</option>
</select>
</div>

<div class="col-md-3">
<select name="status" class="form-control">
<option value="">All Status</option>
<option value="Pending" <?php if($statusFilter=="Pending") echo "selected"; ?>>Pending</option>
<option value="Approved" <?php if($statusFilter=="Approved") echo "selected"; ?>>Approved</option>
<option value="Rejected" <?php if($statusFilter=="Rejected") echo "selected"; ?>>Rejected</option>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">
<i class="bi bi-search"></i> Search
</button>
</div>

</form>

</div>
</div>

<div class="card border-0 shadow-sm">
<div class="card-header bg-white fw-bold">
<i class="bi bi-file-earmark-medical"></i> Submitted Claims for Verification
</div>

<div class="card-body">

<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Patient</th>
<th>Policy</th>
<th>Hospital</th>
<th>Treatment</th>
<th>Amount</th>
<th>Fraud Score</th>
<th>Risk</th>
<th>Status</th>
<th>Decision</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($claims)){ ?>

<tr>
<td>#<?php echo $row['claim_id']; ?></td>
<td><?php echo $row['patient_name']; ?></td>
<td><?php echo $row['policy_number']; ?></td>
<td><?php echo $row['hospital_name']; ?></td>
<td><?php echo $row['treatment_type']; ?></td>
<td>Rs. <?php echo $row['claim_amount']; ?></td>

<td style="min-width:130px;">
<div class="progress" style="height:23px;">
<div class="progress-bar
<?php
if($row['fraud_score'] > 60){ echo 'bg-danger'; }
elseif($row['fraud_score'] > 30){ echo 'bg-warning'; }
else{ echo 'bg-success'; }
?>"
style="width:<?php echo $row['fraud_score']; ?>%">
<?php echo $row['fraud_score']; ?>%
</div>
</div>
</td>

<td>
<?php
if($row['risk_level']=="High"){
    echo "<span class='badge bg-danger'>High Risk</span>";
}
elseif($row['risk_level']=="Medium"){
    echo "<span class='badge bg-warning text-dark'>Medium Risk</span>";
}
else{
    echo "<span class='badge bg-success'>Low Risk</span>";
}
?>
</td>

<td>
<?php
if($row['status']=="Approved"){
    echo "<span class='badge bg-success'>Approved</span>";
}
elseif($row['status']=="Rejected"){
    echo "<span class='badge bg-danger'>Rejected</span>";
}
else{
    echo "<span class='badge bg-secondary'>Pending</span>";
}
?>
</td>

<td>
<?php if($row['status']=="Pending"){ ?>

<a href="review_claims.php?action=approve&id=<?php echo $row['claim_id']; ?>"
class="btn btn-success btn-sm">
<i class="bi bi-check-circle"></i> Approve
</a>

<a href="review_claims.php?action=reject&id=<?php echo $row['claim_id']; ?>"
class="btn btn-danger btn-sm">
<i class="bi bi-x-circle"></i> Reject
</a>

<?php } else { ?>
<span class="text-muted">Decision Done</span>
<?php } ?>
</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>
</div>

<div class="row mt-4">

<div class="col-md-4">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h5><i class="bi bi-shield-exclamation text-danger"></i> Fraud Rules</h5>
<ul>
<li>Claim exceeds coverage</li>
<li>Expired policy</li>
<li>Non-eligible hospital</li>
<li>Duplicate claim pattern</li>
</ul>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h5><i class="bi bi-activity text-primary"></i> Risk Levels</h5>
<p><span class="badge bg-success">Low</span> 0–30</p>
<p><span class="badge bg-warning text-dark">Medium</span> 31–60</p>
<p><span class="badge bg-danger">High</span> 61–100</p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card border-0 shadow-sm">
<div class="card-body">
<h5><i class="bi bi-journal-check text-success"></i> Officer Actions</h5>
<p>Every approval/rejection is stored in audit logs and notification records.</p>
</div>
</div>
</div>

</div>

</div>
</div>
</div>

</body>
</html>
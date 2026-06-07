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
$rejectedClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Rejected'"))['total'];
$highRiskClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='High'"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Advanced Officer Claim Review</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f4f8ff;
    font-family:'Segoe UI',sans-serif;
}
.sidebar{
    width:260px;
    min-height:100vh;
    background:linear-gradient(180deg,#04245c,#063b85);
    color:white;
    position:fixed;
    left:0;
    top:0;
    padding:25px 18px;
}
.sidebar h4{
    font-weight:800;
    line-height:1.4;
}
.sidebar p{
    font-size:13px;
}
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:13px 15px;
    margin:8px 0;
    border-radius:12px;
    font-weight:600;
}
.sidebar a:hover,
.sidebar .active{
    background:#0d6efd;
}
.main{
    margin-left:260px;
    padding:28px;
}
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}
.page-title h2{
    color:#06245c;
    font-weight:800;
}
.profile-box{
    background:white;
    padding:12px 20px;
    border-radius:18px;
    box-shadow:0 5px 18px rgba(0,0,0,0.08);
    font-weight:700;
}
.stat-card{
    background:white;
    border:0;
    border-radius:18px;
    padding:22px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    height:140px;
    position:relative;
    overflow:hidden;
}
.stat-card h6{
    color:#6b7280;
    font-weight:700;
}
.stat-card h2{
    color:#061b46;
    font-weight:800;
}
.stat-card i{
    font-size:42px;
    position:absolute;
    right:22px;
    bottom:20px;
    opacity:0.9;
}
.card{
    border:0;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}
.card-header{
    background:white;
    border-bottom:1px solid #e5eaf2;
    padding:18px 22px;
    font-weight:800;
    color:#08245c;
}
.form-control,
.form-select{
    height:46px;
    border-radius:10px;
}
.btn{
    border-radius:10px;
    font-weight:700;
}
.table{
    margin-bottom:0;
}
.table thead th{
    background:#f3f6fb;
    color:#08245c;
    font-weight:800;
    white-space:nowrap;
}
.table td{
    vertical-align:middle;
}
.badge{
    padding:8px 12px;
    border-radius:8px;
}
.progress{
    height:25px;
    border-radius:20px;
    background:#e8edf3;
}
.progress-bar{
    font-weight:800;
}
.rule-box{
    min-height:160px;
}
.action-btn{
    padding:7px 12px;
    font-size:13px;
}
.alert-note{
    background:#e9f7ff;
    border-left:5px solid #0d6efd;
    color:#06456b;
}
</style>
</head>

<body>

<div class="sidebar">
    <h4><i class="bi bi-shield-check"></i> OFFICER PANEL</h4>
    <p>Claim Review & Fraud Verification</p>
    <hr>

    <a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="review_claims.php" class="active"><i class="bi bi-shield-lock"></i> Review Claims</a>
    <a href="../notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <a href="../audit_logs.php"><i class="bi bi-journal-check"></i> Audit Logs</a>
    <a href="../reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

    <div class="text-center mt-5">
        <i class="bi bi-person-badge" style="font-size:80px;color:#8ec5ff;"></i>
        <p class="mt-3 fw-bold">Secure Claim Decisions</p>
    </div>
</div>

<div class="main">

    <div class="topbar">
        <div class="page-title">
            <h2>Advanced Insurance Officer Claim Review</h2>
            <p class="text-muted">Review submitted claims, analyze fraud score and perform claim decisions</p>
        </div>

        <div class="profile-box">
            <i class="bi bi-person-circle fs-3 text-primary"></i>
            Insurance Officer
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <h6>Total Claims</h6>
                <h2><?php echo $totalClaims; ?></h2>
                <i class="bi bi-file-earmark-medical text-primary"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <h6>Pending Claims</h6>
                <h2><?php echo $pendingClaims; ?></h2>
                <i class="bi bi-hourglass-split text-warning"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <h6>Approved Claims</h6>
                <h2><?php echo $approvedClaims; ?></h2>
                <i class="bi bi-check-circle text-success"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <h6>High Risk Claims</h6>
                <h2><?php echo $highRiskClaims; ?></h2>
                <i class="bi bi-exclamation-triangle text-danger"></i>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
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
                    <select name="risk" class="form-select">
                        <option value="">All Risk Levels</option>
                        <option value="Low" <?php if($risk=="Low") echo "selected"; ?>>Low Risk</option>
                        <option value="Medium" <?php if($risk=="Medium") echo "selected"; ?>>Medium Risk</option>
                        <option value="High" <?php if($risk=="High") echo "selected"; ?>>High Risk</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
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

    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-file-earmark-medical"></i> Submitted Claims for Verification
        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">
                <thead>
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
                        <td><strong><?php echo $row['patient_name']; ?></strong></td>
                        <td><?php echo $row['policy_number']; ?></td>
                        <td><?php echo $row['hospital_name']; ?></td>
                        <td><?php echo $row['treatment_type']; ?></td>
                        <td>PKR <?php echo $row['claim_amount']; ?></td>

                        <td style="min-width:150px;">
                            <div class="progress">
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
                                class="btn btn-success btn-sm action-btn">
                                    <i class="bi bi-check-circle"></i> Approve
                                </a>

                                <a href="review_claims.php?action=reject&id=<?php echo $row['claim_id']; ?>"
                                class="btn btn-danger btn-sm action-btn">
                                    <i class="bi bi-x-circle"></i> Reject
                                </a>

                            <?php } else { ?>
                                <span class="text-muted fw-bold">Decision Done</span>
                            <?php } ?>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>

        </div>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card rule-box">
                <div class="card-body">
                    <h5 class="fw-bold text-danger">
                        <i class="bi bi-shield-exclamation"></i> Fraud Rules
                    </h5>
                    <ul class="mt-3">
                        <li>Claim amount exceeds coverage</li>
                        <li>Expired insurance policy</li>
                        <li>Non-eligible hospital</li>
                        <li>Duplicate claim pattern</li>
                        <li>Unusual high claim amount</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card rule-box">
                <div class="card-body">
                    <h5 class="fw-bold text-primary">
                        <i class="bi bi-activity"></i> Risk Levels
                    </h5>
                    <p class="mt-3"><span class="badge bg-success">Low</span> 0–30 Fraud Score</p>
                    <p><span class="badge bg-warning text-dark">Medium</span> 31–60 Fraud Score</p>
                    <p><span class="badge bg-danger">High</span> 61–100 Fraud Score</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card rule-box">
                <div class="card-body">
                    <h5 class="fw-bold text-success">
                        <i class="bi bi-journal-check"></i> Officer Actions
                    </h5>
                    <p class="mt-3">
                        Every approval and rejection decision is stored in audit logs and notification records.
                    </p>

                    <div class="alert alert-note mt-3">
                        This improves transparency, accountability and fraud monitoring.
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>
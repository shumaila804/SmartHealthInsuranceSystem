<?php
include("../database.php");

$claim = null;
$policy_number = "";

if(isset($_POST['track'])){
    $policy_number = $_POST['policy_number'];

    $query = mysqli_query($conn,
    "SELECT * FROM claims WHERE policy_number='$policy_number' ORDER BY submitted_at DESC LIMIT 1");

    if(mysqli_num_rows($query) > 0){
        $claim = mysqli_fetch_assoc($query);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Policyholder Claim Tracking</title>

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
.card{
    border:0;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}
.search-card{
    padding:25px;
}
.form-control{
    height:48px;
    border-radius:10px;
}
.btn-primary{
    height:48px;
    border-radius:10px;
    font-weight:700;
}
.status-box{
    background:#eaf8ee;
    color:#0b7c29;
    border-radius:18px;
    padding:30px;
    text-align:center;
}
.status-box i{
    font-size:65px;
}
.timeline{
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:relative;
    margin-top:25px;
}
.timeline::before{
    content:"";
    position:absolute;
    top:28px;
    left:50px;
    right:50px;
    height:5px;
    background:#21a64a;
    z-index:0;
}
.step{
    position:relative;
    z-index:1;
    text-align:center;
    width:20%;
}
.step-icon{
    width:58px;
    height:58px;
    border-radius:50%;
    background:#21a64a;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-size:24px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}
.step.pending .step-icon{
    background:#cfd6df;
    color:#334;
}
.badge{
    padding:8px 12px;
    border-radius:8px;
}
.table th{
    background:#f3f6fb;
}
.doc-row{
    display:flex;
    justify-content:space-between;
    padding:12px 0;
    border-bottom:1px solid #e5eaf2;
}
.summary-row{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #e5eaf2;
}
.payable{
    background:#eaf8ee;
    color:#0b7c29;
    padding:14px;
    border-radius:12px;
    font-weight:800;
}
.notification-badge{
    background:red;
    color:white;
    border-radius:50%;
    padding:3px 8px;
    font-size:12px;
}
</style>
</head>

<body>

<div class="sidebar">
    <h4><i class="bi bi-shield-plus"></i> SMART HEALTH<br>INSURANCE SYSTEM</h4>
    <p class="small">Claim Processing & Fraud Detection</p>
    <hr>

    <a href="../dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="track_claim.php" class="active"><i class="bi bi-file-earmark-text"></i> Track Claim Status</a>
    <a href="../notifications.php"><i class="bi bi-bell"></i> Notifications <span class="notification-badge">2</span></a>
    <a href="../reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

    <div class="text-center mt-5">
        <i class="bi bi-shield-check" style="font-size:80px;color:#8ec5ff;"></i>
        <p class="mt-3 fw-bold">Your Health, Our Priority</p>
    </div>
</div>

<div class="main">

    <div class="topbar mb-4">
        <div class="page-title">
            <h2>Policyholder Claim Tracking</h2>
            <p class="text-muted">Track and monitor your insurance claim status</p>
        </div>

        <div class="profile-box">
            <i class="bi bi-person-circle fs-3 text-primary"></i>
            Policyholder
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card search-card">
                <form method="POST" class="row">
                    <div class="col-md-9">
                        <label class="form-label fw-bold text-primary">Enter Policy Number</label>
                        <input type="text" name="policy_number" value="<?php echo $policy_number; ?>" class="form-control" placeholder="Example: POL1001" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="track" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Track Claim
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card search-card">
                <h5 class="fw-bold text-primary"><i class="bi bi-info-circle-fill"></i> Need Help?</h5>
                <p class="mb-2">For any issue related to your claim, contact our support team.</p>
                <button class="btn btn-outline-primary btn-sm">Contact Support</button>
            </div>
        </div>
    </div>

<?php if($claim){ ?>

    <div class="card p-4 mb-4">
        <div class="row align-items-center">

            <div class="col-md-3">
                <p class="mb-1">Claim ID</p>
                <h5>#<?php echo $claim['claim_id']; ?></h5>

                <p class="mb-1 mt-3">Policy Number</p>
                <h5><?php echo $claim['policy_number']; ?></h5>

                <p class="mb-1 mt-3">Treatment</p>
                <h5><?php echo $claim['treatment_type']; ?></h5>
            </div>

            <div class="col-md-3">
                <p class="mb-1">Patient Name</p>
                <h5><?php echo $claim['patient_name']; ?></h5>

                <p class="mb-1 mt-3">Hospital Name</p>
                <h5><?php echo $claim['hospital_name']; ?></h5>

                <p class="mb-1 mt-3">Submitted At</p>
                <h5><?php echo $claim['submitted_at']; ?></h5>
            </div>

            <div class="col-md-3">
                <p class="mb-1">Claim Amount</p>
                <h4 class="text-success">PKR <?php echo $claim['claim_amount']; ?></h4>

                <p class="mb-1 mt-3">Current Status</p>
                <?php
                if($claim['status']=="Approved"){
                    echo "<span class='badge bg-success'>Approved</span>";
                } elseif($claim['status']=="Rejected"){
                    echo "<span class='badge bg-danger'>Rejected</span>";
                } else {
                    echo "<span class='badge bg-warning text-dark'>Pending</span>";
                }
                ?>

                <p class="mb-1 mt-3">Risk Level</p>
                <?php
                if($claim['risk_level']=="High"){
                    echo "<span class='badge bg-danger'>High Risk</span>";
                } elseif($claim['risk_level']=="Medium"){
                    echo "<span class='badge bg-warning text-dark'>Medium Risk</span>";
                } else {
                    echo "<span class='badge bg-success'>Low Risk</span>";
                }
                ?>
            </div>

            <div class="col-md-3">
                <div class="status-box">
                    <i class="bi bi-check-circle-fill"></i>
                    <h5 class="mt-3">Claim Status</h5>
                    <p>Your claim is currently <strong><?php echo $claim['status']; ?></strong>.</p>
                </div>
            </div>

        </div>
    </div>

    <div class="card p-4 mb-4">
        <div class="timeline">
            <div class="step">
                <div class="step-icon"><i class="bi bi-file-earmark-plus"></i></div>
                <h6 class="mt-2">Claim Submitted</h6>
                <small><?php echo $claim['submitted_at']; ?></small>
            </div>

            <div class="step">
                <div class="step-icon"><i class="bi bi-person-check"></i></div>
                <h6 class="mt-2">Under Review</h6>
                <small>Officer Review</small>
            </div>

            <div class="step">
                <div class="step-icon"><i class="bi bi-shield-check"></i></div>
                <h6 class="mt-2">Verification</h6>
                <small>Fraud Analysis</small>
            </div>

            <div class="step">
                <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                <h6 class="mt-2"><?php echo $claim['status']; ?></h6>
                <small>Decision Done</small>
            </div>

            <div class="step pending">
                <div class="step-icon"><i class="bi bi-credit-card"></i></div>
                <h6 class="mt-2">Payment</h6>
                <small>Pending</small>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-md-7">
            <div class="card p-4">
                <h5 class="fw-bold">Claim History</h5>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $claim['submitted_at']; ?></td>
                            <td><span class="badge bg-primary">Submitted</span></td>
                            <td>Claim has been submitted successfully.</td>
                            <td>Hospital</td>
                        </tr>
                        <tr>
                            <td>-</td>
                            <td><span class="badge bg-info text-dark">Fraud Check</span></td>
                            <td>Fraud score calculated as <?php echo $claim['fraud_score']; ?>.</td>
                            <td>System</td>
                        </tr>
                        <tr>
                            <td>-</td>
                            <td><span class="badge bg-success"><?php echo $claim['status']; ?></span></td>
                            <td>Claim current status is <?php echo $claim['status']; ?>.</td>
                            <td>Officer</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-5">

           <div class="card p-4 mb-4">
    <h5 class="fw-bold">Claim Documents</h5>

    <div class="doc-row">
        <span><i class="bi bi-file-earmark-pdf text-danger"></i> Medical Report.pdf</span>
        <span>
            245 KB
            <a href="../download_report.php?claim_id=<?php echo $claim['claim_id']; ?>">
                <i class="bi bi-download text-primary"></i>
            </a>
        </span>
    </div>

    <div class="doc-row">
        <span><i class="bi bi-file-earmark-pdf text-danger"></i> Hospital Bill.pdf</span>
        <span>
            320 KB
            <a href="../download_report.php?claim_id=<?php echo $claim['claim_id']; ?>">
                <i class="bi bi-download text-primary"></i>
            </a>
        </span>
    </div>

    <div class="doc-row">
        <span><i class="bi bi-file-earmark-image text-success"></i> X-Ray Report.jpg</span>
        <span>
            150 KB
            <a href="../download_report.php?claim_id=<?php echo $claim['claim_id']; ?>">
                <i class="bi bi-download text-primary"></i>
            </a>
        </span>
    </div>

    <div class="text-center mt-3">
        <a href="../download_report.php?claim_id=<?php echo $claim['claim_id']; ?>"
           class="btn btn-primary">
           Download Full Report
        </a>
    </div>

</div>

            <div class="card p-4">
                <h5 class="fw-bold">Claim Summary</h5>

                <div class="summary-row">
                    <span>Claim Amount</span>
                    <strong>PKR <?php echo $claim['claim_amount']; ?></strong>
                </div>

                <div class="summary-row">
                    <span>Fraud Score</span>
                    <strong><?php echo $claim['fraud_score']; ?></strong>
                </div>

                <div class="summary-row">
                    <span>Deduction</span>
                    <strong>PKR 0</strong>
                </div>

                <div class="payable mt-3 d-flex justify-content-between">
                    <span>Payable Amount</span>
                    <span>PKR <?php echo $claim['claim_amount']; ?></span>
                </div>
            </div>

        </div>

    </div>

<?php } else { ?>

    <div class="card p-5 text-center">
        <i class="bi bi-search text-primary" style="font-size:60px;"></i>
        <h4 class="mt-3">Search Claim Status</h4>
        <p class="text-muted">Enter a valid policy number to view claim tracking details.</p>
    </div>

<?php } ?>

</div>

</body>
</html>
<?php
include("database.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$totalClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims"))['total'];
$approvedClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Approved'"))['total'];
$pendingClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Pending'"))['total'];
$rejectedClaims = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE status='Rejected'"))['total'];
$lowRisk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='Low'"))['total'];
$mediumRisk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='Medium'"))['total'];
$highRisk = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM claims WHERE risk_level='High'"))['total'];

$claims = mysqli_query($conn,"SELECT * FROM claims ORDER BY submitted_at DESC LIMIT 6");

$approvalRate = ($totalClaims > 0) ? round(($approvedClaims / $totalClaims) * 100, 1) : 0;
$rejectionRate = ($totalClaims > 0) ? round(($rejectedClaims / $totalClaims) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports & Analytics</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    background:#f4f8ff;
    margin:0;
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
    padding:14px 16px;
    margin:9px 0;
    border-radius:16px;
    font-weight:700;
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
.topbar h2{
    color:#071952;
    font-weight:900;
}
.profile-box{
    background:white;
    border-radius:18px;
    padding:12px 20px;
    box-shadow:0 5px 18px rgba(0,0,0,0.08);
    font-weight:700;
}
.stat-card{
    border:0;
    border-radius:18px;
    padding:24px;
    min-height:145px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}
.stat-card h6{
    font-weight:800;
    color:#071952;
}
.stat-card h1{
    font-weight:900;
}
.blue-bg{background:#eaf3ff;}
.green-bg{background:#ecf9f0;}
.yellow-bg{background:#fff8e6;}
.red-bg{background:#fdecec;}
.card{
    border:0;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}
.card-header{
    background:white;
    border-bottom:1px solid #e5eaf2;
    padding:18px 22px;
    font-weight:900;
    color:#071952;
}
.chart-box{
    height:280px;
}
.table th{
    background:#f3f6fb;
    color:#071952;
}
.badge{
    padding:8px 12px;
    border-radius:8px;
}
.report-note{
    background:#eaf4ff;
    color:#06245c;
    border-radius:16px;
    padding:18px;
    font-weight:700;
}
</style>
</head>

<body>

<div class="sidebar">
    <h4><i class="bi bi-bar-chart-line"></i> SMART HEALTH<br>REPORTS</h4>
    <p>Analytics & Claim Monitoring</p>
    <hr>

    <a href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="hospital/submit_claim.php"><i class="bi bi-file-earmark-plus"></i> Submit New Claim</a>
    <a href="policyholder/track_claim.php"><i class="bi bi-search"></i> Claim Tracking</a>
    <a href="officer/review_claims.php"><i class="bi bi-shield-check"></i> Officer Review</a>
    <a href="reports.php" class="active"><i class="bi bi-bar-chart"></i> Reports</a>
    <a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

    <div class="text-center mt-5">
        <i class="bi bi-graph-up-arrow" style="font-size:90px;color:#8ec5ff;"></i>
        <p class="fw-bold mt-3">Data Driven Decisions</p>
    </div>
</div>

<div class="main">

    <div class="topbar">
        <div>
            <h2><i class="bi bi-bar-chart-fill"></i> Reports & Analytics Dashboard</h2>
            <p class="text-muted">Monitor claim performance, approval trends and fraud risk distribution</p>
        </div>

        <div class="profile-box">
            <i class="bi bi-person-circle text-primary"></i>
            Role: <?php echo strtoupper($_SESSION['role']); ?>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="stat-card blue-bg">
                <h6>Total Claims</h6>
                <h1><?php echo $totalClaims; ?></h1>
                <p class="text-muted">All submitted claims</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card green-bg">
                <h6>Approved Claims</h6>
                <h1><?php echo $approvedClaims; ?></h1>
                <p class="text-muted">Approval Rate: <?php echo $approvalRate; ?>%</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card yellow-bg">
                <h6>Pending Claims</h6>
                <h1><?php echo $pendingClaims; ?></h1>
                <p class="text-muted">Awaiting officer review</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card red-bg">
                <h6>Rejected Claims</h6>
                <h1><?php echo $rejectedClaims; ?></h1>
                <p class="text-muted">Rejection Rate: <?php echo $rejectionRate; ?>%</p>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Claim Status Distribution
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
                <div class="card-header">
                    Fraud Risk Analysis
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-header">
            Recent Claim Report
        </div>

        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Claim ID</th>
                        <th>Patient</th>
                        <th>Policy</th>
                        <th>Hospital</th>
                        <th>Amount</th>
                        <th>Risk</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($row = mysqli_fetch_assoc($claims)){ ?>
                    <tr>
                        <td>CLM<?php echo str_pad($row['claim_id'],4,"0",STR_PAD_LEFT); ?></td>
                        <td><?php echo $row['patient_name']; ?></td>
                        <td><?php echo $row['policy_number']; ?></td>
                        <td><?php echo $row['hospital_name']; ?></td>
                        <td>PKR <?php echo $row['claim_amount']; ?></td>
                        <td>
                            <?php
                            if($row['risk_level']=="High"){
                                echo "<span class='badge bg-danger'>High</span>";
                            } elseif($row['risk_level']=="Medium"){
                                echo "<span class='badge bg-warning text-dark'>Medium</span>";
                            } else {
                                echo "<span class='badge bg-success'>Low</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($row['status']=="Approved"){
                                echo "<span class='badge bg-success'>Approved</span>";
                            } elseif($row['status']=="Rejected"){
                                echo "<span class='badge bg-danger'>Rejected</span>";
                            } else {
                                echo "<span class='badge bg-warning text-dark'>Pending</span>";
                            }
                            ?>
                        </td>
                        <td><?php echo $row['submitted_at']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="report-note">
        <i class="bi bi-info-circle-fill text-primary fs-4"></i>
        Reports help management analyze claim volume, approval ratio, rejection trends and fraud risk patterns.
    </div>

</div>

<script>
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending', 'Rejected'],
        datasets: [{
            data: [<?php echo $approvedClaims; ?>, <?php echo $pendingClaims; ?>, <?php echo $rejectedClaims; ?>],
            backgroundColor: ['#198754','#f0a800','#dc3545'],
            borderWidth:0
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        plugins:{ legend:{ position:'right' } }
    }
});

new Chart(document.getElementById('riskChart'), {
    type: 'bar',
    data: {
        labels: ['Low Risk', 'Medium Risk', 'High Risk'],
        datasets: [{
            label: 'Claims',
            data: [<?php echo $lowRisk; ?>, <?php echo $mediumRisk; ?>, <?php echo $highRisk; ?>],
            backgroundColor: ['#198754','#f0a800','#dc3545']
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        scales:{ y:{ beginAtZero:true } }
    }
});
</script>

</body>
</html>
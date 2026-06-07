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
$claims = mysqli_query($conn,"SELECT * FROM claims ORDER BY submitted_at DESC LIMIT 5");

$approvedPercent = ($totalClaims > 0) ? round(($approvedClaims / $totalClaims) * 100, 1) : 0;
$pendingPercent = ($totalClaims > 0) ? round(($pendingClaims / $totalClaims) * 100, 1) : 0;
$rejectedPercent = ($totalClaims > 0) ? round(($rejectedClaims / $totalClaims) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Hospital Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    background:#f4f8ff;
    font-family:'Segoe UI',sans-serif;
    margin:0;
}
.sidebar{
    width:260px;
    min-height:100vh;
    background:linear-gradient(180deg,#04245c,#063b85);
    color:white;
    position:fixed;
    top:0;
    left:0;
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
    border-radius:18px;
    font-weight:700;
}
.sidebar a:hover,
.sidebar .active{
    background:#0d6efd;
}
.sidebar-footer{
    position:absolute;
    bottom:22px;
    left:20px;
    right:20px;
    font-size:12px;
}
.main{
    margin-left:260px;
    padding:28px;
}
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}
.topbar h2{
    color:#071952;
    font-weight:900;
}
.icon-btn{
    width:55px;
    height:55px;
    border-radius:50%;
    background:white;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 5px 18px rgba(0,0,0,0.08);
    font-size:25px;
}
.profile-box{
    background:white;
    border-radius:18px;
    padding:12px 20px;
    box-shadow:0 5px 18px rgba(0,0,0,0.08);
    font-weight:700;
}
.banner{
    background:linear-gradient(90deg,#eef6ff,#ffffff);
    border-radius:20px;
    padding:35px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    position:relative;
    overflow:hidden;
}
.banner h3{
    color:#071952;
    font-weight:900;
}
.banner-icon{
    position:absolute;
    right:70px;
    top:25px;
    font-size:120px;
    color:#0d6efd;
    opacity:0.18;
}
.stat-card{
    border:0;
    border-radius:18px;
    padding:24px;
    min-height:150px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    position:relative;
    overflow:hidden;
}
.stat-card h6{
    font-weight:800;
    color:#071952;
}
.stat-card h1{
    font-weight:900;
    color:#000;
}
.stat-icon{
    width:55px;
    height:55px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:25px;
    float:left;
    margin-right:18px;
}
.blue-bg{background:#eaf3ff;}
.green-bg{background:#ecf9f0;}
.yellow-bg{background:#fff8e6;}
.red-bg{background:#fdecec;}
.blue-icon{background:#0d6efd;}
.green-icon{background:#198754;}
.yellow-icon{background:#f0a800;}
.red-icon{background:#dc3545;}
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
.quick-btn{
    padding:18px 25px;
    border-radius:14px;
    font-weight:800;
    text-decoration:none;
    display:block;
    text-align:center;
}
.chart-box{
    height:260px;
}
.table th{
    background:#f3f6fb;
    color:#071952;
}
.badge{
    padding:8px 12px;
    border-radius:8px;
}
.info-bar{
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
    <h4><i class="bi bi-shield-plus"></i> SMART HEALTH<br>INSURANCE SYSTEM</h4>
    <p>Claim Processing & Fraud Detection</p>
    <hr>

    <a href="dashboard.php" class="active"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="hospital/submit_claim.php"><i class="bi bi-file-earmark-plus"></i> Submit New Claim</a>
    <a href="policyholder/track_claim.php"><i class="bi bi-file-earmark-text"></i> Claim Tracking</a>
    <a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <a href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
    <a href="admin/manage_users.php"><i class="bi bi-people"></i> Manage Users</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

    <div class="text-center mt-5">
        <i class="bi bi-clipboard2-pulse" style="font-size:90px;color:#8ec5ff;"></i>
        <p class="fw-bold mt-3">Your Health, Our Priority</p>
    </div>

    <div class="sidebar-footer">
        © 2026 Smart Health Insurance System.
    </div>
</div>

<div class="main">

    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-btn">
                <i class="bi bi-list"></i>
            </div>
            <h2 class="mb-0">Hospital Dashboard</h2>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="bi bi-bell fs-3"></i>
                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">5</span>
            </div>

            <div class="profile-box">
                <i class="bi bi-building text-primary"></i>
                City Care Hospital<br>
                <small class="text-muted">Hospital User</small>
            </div>
        </div>
    </div>

    <div class="banner mb-4">
        <h3>Welcome, City Care Hospital</h3>
        <p class="mb-0 fs-5">Manage and track insurance claims efficiently.</p>
        <i class="bi bi-hospital banner-icon"></i>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="stat-card blue-bg">
                <div class="stat-icon blue-icon"><i class="bi bi-file-text"></i></div>
                <h6>Total Claims</h6>
                <h1><?php echo $totalClaims; ?></h1>
                <p class="text-muted">All time claims</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card green-bg">
                <div class="stat-icon green-icon"><i class="bi bi-check-lg"></i></div>
                <h6>Approved Claims</h6>
                <h1><?php echo $approvedClaims; ?></h1>
                <p class="text-muted"><?php echo $approvedPercent; ?>% of total claims</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card yellow-bg">
                <div class="stat-icon yellow-icon"><i class="bi bi-clock"></i></div>
                <h6>Pending Claims</h6>
                <h1><?php echo $pendingClaims; ?></h1>
                <p class="text-muted"><?php echo $pendingPercent; ?>% of total claims</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card red-bg">
                <div class="stat-icon red-icon"><i class="bi bi-x-lg"></i></div>
                <h6>Rejected Claims</h6>
                <h1><?php echo $rejectedClaims; ?></h1>
                <p class="text-muted"><?php echo $rejectedPercent; ?>% of total claims</p>
            </div>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Quick Actions</h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <a href="hospital/submit_claim.php" class="quick-btn blue-bg text-primary">
                        <i class="bi bi-plus-circle fs-3"></i> Submit New Claim
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="policyholder/track_claim.php" class="quick-btn green-bg text-success">
                        <i class="bi bi-search fs-3"></i> Track Claim
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="reports.php" class="quick-btn yellow-bg text-warning">
                        <i class="bi bi-bar-chart fs-3"></i> Reports
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="notifications.php" class="quick-btn red-bg text-danger">
                        <i class="bi bi-bell fs-3"></i> Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Claim Status Overview</div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <p class="fw-bold text-center mt-2">Total Claims: <?php echo $totalClaims; ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Recent Claims</span>
                    <a href="reports.php" class="text-primary text-decoration-none">View All</a>
                </div>

                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Claim ID</th>
                                <th>Patient Name</th>
                                <th>Amount (PKR)</th>
                                <th>Status</th>
                                <th>Submitted On</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php while($row = mysqli_fetch_assoc($claims)){ ?>
                            <tr>
                                <td>CLM<?php echo str_pad($row['claim_id'],4,"0",STR_PAD_LEFT); ?></td>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['claim_amount']; ?></td>
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
        </div>

    </div>

    <div class="info-bar">
        <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
        Ensure accurate information and complete documents to speed up claim approval process.
    </div>

</div>

<script>
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending', 'Rejected'],
        datasets: [{
            data: [<?php echo $approvedClaims; ?>, <?php echo $pendingClaims; ?>, <?php echo $rejectedClaims; ?>],
            backgroundColor: ['#198754', '#f0a800', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                position:'right'
            }
        }
    }
});
</script>

</body>
</html>
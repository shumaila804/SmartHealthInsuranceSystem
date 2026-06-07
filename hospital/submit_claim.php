<?php
include("../database.php");

if(isset($_POST['submit_claim']))
{
    $patient_name   = $_POST['patient_name'];
    $policy_number  = $_POST['policy_number'];
    $hospital_name  = $_POST['hospital_name'];
    $treatment_type = $_POST['treatment_type'];
    $claim_amount   = $_POST['claim_amount'];

    $fraud_score = 0;

    $policyQuery = mysqli_query($conn,"SELECT * FROM policies WHERE policy_number='$policy_number'");
    $policy = mysqli_fetch_assoc($policyQuery);

    if($policy){
        if($claim_amount > $policy['coverage_amount']){
            $fraud_score += 40;
        }
        if($policy['status'] == 'Expired'){
            $fraud_score += 30;
        }
    } else {
        $fraud_score += 50;
    }

    $hospitalQuery = mysqli_query($conn,"SELECT * FROM hospitals WHERE hospital_name='$hospital_name'");
    $hospital = mysqli_fetch_assoc($hospitalQuery);

    if($hospital){
        if($hospital['eligibility_status'] == 'Not Eligible'){
            $fraud_score += 20;
        }
    } else {
        $fraud_score += 20;
    }

    $duplicateQuery = mysqli_query($conn,
    "SELECT * FROM claims WHERE patient_name='$patient_name' AND treatment_type='$treatment_type'");

    if(mysqli_num_rows($duplicateQuery) > 0){
        $fraud_score += 10;
    }

    if($claim_amount > 500000){
        $fraud_score += 20;
    }

    if($fraud_score <= 30){
        $risk_level = "Low";
    }
    elseif($fraud_score <= 60){
        $risk_level = "Medium";
    }
    else{
        $risk_level = "High";
    }

    mysqli_query($conn,
    "INSERT INTO claims
    (patient_name, policy_number, hospital_name, treatment_type, claim_amount, fraud_score, risk_level)
    VALUES
    ('$patient_name','$policy_number','$hospital_name','$treatment_type','$claim_amount','$fraud_score','$risk_level')");

    mysqli_query($conn,
    "INSERT INTO audit_logs(action_performed, performed_by)
    VALUES('New claim submitted by hospital','Hospital User')");

    mysqli_query($conn,
    "INSERT INTO notifications(message,user_role)
    VALUES('New claim submitted by hospital for verification','officer')");

    $success = "Claim submitted successfully! Fraud Score: $fraud_score | Risk Level: $risk_level";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Hospital Claim Submission</title>

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
    font-weight:700;
    line-height:1.4;
}
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:13px 15px;
    margin:8px 0;
    border-radius:10px;
    font-weight:500;
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
.profile-box{
    background:white;
    padding:12px 20px;
    border-radius:18px;
    box-shadow:0 5px 18px rgba(0,0,0,0.08);
    font-weight:600;
}
.page-title h2{
    color:#06245c;
    font-weight:800;
}
.card{
    border:0;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}
.card-header{
    background:white;
    color:#0759d6;
    font-size:18px;
    font-weight:700;
    padding:18px 22px;
    border-bottom:1px solid #e5eaf2;
}
.form-label{
    font-weight:600;
    color:#08245c;
}
.form-control,
.form-select{
    height:45px;
    border-radius:10px;
}
textarea.form-control{
    height:95px;
}
.upload-box{
    border:2px dashed #b8c8e8;
    border-radius:15px;
    padding:28px;
    text-align:center;
    background:#fbfdff;
}
.upload-icon{
    font-size:35px;
    color:#0d6efd;
}
.btn-primary{
    background:#075be8;
    border:0;
    border-radius:10px;
    padding:12px;
    font-weight:600;
}
.btn-light{
    border-radius:10px;
    padding:12px;
    font-weight:600;
}
.alert{
    border-radius:12px;
}
</style>
</head>

<body>

<div class="sidebar">
    <h4><i class="bi bi-hospital"></i> SMART HEALTH<br>INSURANCE SYSTEM</h4>
    <p class="small">Claim Processing & Fraud Detection</p>
    <hr>

    <a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="submit_claim.php" class="active"><i class="bi bi-file-earmark-plus"></i> Submit New Claim</a>
    <a href="../policyholder/track_claim.php"><i class="bi bi-search"></i> Claim Tracking</a>
    <a href="../notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <a href="../reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
    <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <div class="page-title">
            <h2><i class="bi bi-clipboard2-pulse"></i> Claim Submission Form</h2>
            <p class="text-muted">Fill all required fields to submit a new insurance claim.</p>
        </div>

        <div class="profile-box">
            <i class="bi bi-building"></i> City Care Hospital<br>
            <small class="text-muted">Hospital User</small>
        </div>
    </div>

    <?php
    if(isset($success)){
        echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> $success</div>";
    }
    ?>

    <form method="POST" enctype="multipart/form-data">

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-person-vcard"></i> Patient & Policy Information
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Patient Name *</label>
                            <input type="text" name="patient_name" class="form-control" placeholder="Enter Patient Name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Policy Number *</label>
                            <input type="text" name="policy_number" class="form-control" placeholder="Example: POL1001" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hospital Name *</label>
                            <input type="text" name="hospital_name" class="form-control" placeholder="Enter Hospital Name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" placeholder="Enter Contact Number">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter Email Address">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-heart-pulse"></i> Treatment Details
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Treatment Type *</label>
                        <input type="text" name="treatment_type" class="form-control" placeholder="Enter Treatment Type" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Treatment</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diagnosed Disease / Issue</label>
                        <textarea class="form-control" placeholder="Enter diagnosed treatment description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Claim Amount (PKR) *</label>
                        <input type="number" name="claim_amount" class="form-control" placeholder="Enter Amount" required>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-cloud-arrow-up-fill"></i> Document Upload
                </div>

                <div class="card-body">
                    <p class="small fw-bold">Upload required documents (PDF, JPG, PNG - Max 5MB each)</p>

                    <div class="upload-box">
                        <div class="upload-icon">
                            <i class="bi bi-cloud-upload-fill"></i>
                        </div>
                        <p class="mb-2">Drag & drop files here</p>
                        <input type="file" class="form-control">
                    </div>

                    <small class="text-muted d-block mt-3">
                        Supported formats: PDF, JPG, PNG<br>
                        Max file size: 5MB per file
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-check-square-fill text-success"></i> Declaration
                </div>

                <div class="card-body">
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" required>
                        <label class="form-check-label">
                            I hereby declare that all the information provided is true and correct to the best of my knowledge.
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <button type="reset" class="btn btn-light w-100">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" name="submit_claim" class="btn btn-primary w-100">
                                <i class="bi bi-send-fill"></i> Submit Claim
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <strong>Fraud Detection:</strong> After submission, the system automatically calculates fraud score and assigns risk level.
                    </div>
                </div>
            </div>
        </div>

    </div>

    </form>

</div>

</body>
</html>
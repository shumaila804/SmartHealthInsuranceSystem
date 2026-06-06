```php
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

    // =========================
    // POLICY CHECK
    // =========================

    $policyQuery = mysqli_query($conn,
    "SELECT * FROM policies
     WHERE policy_number='$policy_number'");

    $policy = mysqli_fetch_assoc($policyQuery);

    if($policy)
    {
        // Claim exceeds coverage
        if($claim_amount > $policy['coverage_amount'])
        {
            $fraud_score += 40;
        }

        // Policy expired
        if($policy['status'] == 'Expired')
        {
            $fraud_score += 30;
        }
    }
    else
    {
        // Invalid policy
        $fraud_score += 50;
    }

    // =========================
    // HOSPITAL CHECK
    // =========================

    $hospitalQuery = mysqli_query($conn,
    "SELECT * FROM hospitals
     WHERE hospital_name='$hospital_name'");

    $hospital = mysqli_fetch_assoc($hospitalQuery);

    if($hospital)
    {
        if($hospital['eligibility_status'] == 'Not Eligible')
        {
            $fraud_score += 20;
        }
    }
    else
    {
        $fraud_score += 20;
    }

    // =========================
    // DUPLICATE CLAIM CHECK
    // =========================

    $duplicateQuery = mysqli_query($conn,
    "SELECT * FROM claims
     WHERE patient_name='$patient_name'
     AND treatment_type='$treatment_type'");

    if(mysqli_num_rows($duplicateQuery) > 0)
    {
        $fraud_score += 10;
    }

    // =========================
    // HIGH AMOUNT CHECK
    // =========================

    if($claim_amount > 500000)
    {
        $fraud_score += 20;
    }

    // =========================
    // RISK LEVEL
    // =========================

    if($fraud_score <= 30)
    {
        $risk_level = "Low";
    }
    elseif($fraud_score <= 60)
    {
        $risk_level = "Medium";
    }
    else
    {
        $risk_level = "High";
    }

    // =========================
    // INSERT CLAIM
    // =========================

    mysqli_query($conn,
    "INSERT INTO claims
    (
        patient_name,
        policy_number,
        hospital_name,
        treatment_type,
        claim_amount,
        fraud_score,
        risk_level
    )
    VALUES
    (
        '$patient_name',
        '$policy_number',
        '$hospital_name',
        '$treatment_type',
        '$claim_amount',
        '$fraud_score',
        '$risk_level'
    )");

    // =========================
    // AUDIT LOG
    // =========================

    mysqli_query($conn,
    "INSERT INTO audit_logs
    (
        action_performed,
        performed_by
    )
    VALUES
    (
        'New claim submitted by hospital',
        'Hospital User'
    )");

    // =========================
    // NOTIFICATION
    // =========================

    mysqli_query($conn,
    "INSERT INTO notifications
    (
        message,
        user_role
    )
    VALUES
    (
        'New claim submitted by hospital for verification',
        'officer'
    )");

    $success =
    "Claim Submitted Successfully!
    Fraud Score: ".$fraud_score."
    | Risk Level: ".$risk_level;
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Advanced Hospital Claim Submission</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body style="background:#f4f7fb;">

<div class="container mt-5">

<div class="card border-0 shadow">

<div class="card-header bg-primary text-white">
<h3>Advanced Hospital Claim Submission Portal</h3>
</div>

<div class="card-body">

<?php
if(isset($success))
{
echo "<div class='alert alert-success'>$success</div>";
}
?>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">
<label>Patient Name</label>
<input type="text"
name="patient_name"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Policy Number</label>
<input type="text"
name="policy_number"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Hospital Name</label>
<input type="text"
name="hospital_name"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Treatment Type</label>
<input type="text"
name="treatment_type"
class="form-control"
required>
</div>

<div class="col-md-12 mb-3">
<label>Claim Amount</label>
<input type="number"
name="claim_amount"
class="form-control"
required>
</div>

</div>

<button
type="submit"
name="submit_claim"
class="btn btn-success">

Submit Claim

</button>

</form>

</div>
</div>
</div>

</body>
</html>
```

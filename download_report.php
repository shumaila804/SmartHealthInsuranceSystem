<?php
$claim_id = $_GET['claim_id'] ?? 'CLM1001';

header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename=Claim_Report_$claim_id.txt");

echo "SMART HEALTH INSURANCE SYSTEM\n";
echo "Claim Processing and Fraud Detection System\n";
echo "========================================\n\n";

echo "Claim ID: $claim_id\n";
echo "Policy Number: POL1001\n";
echo "Patient Name: Shumaila Arif\n";
echo "Hospital Name: City Care Hospital\n";
echo "Treatment Type: Operation\n";
echo "Claim Amount: PKR 150000\n";
echo "Fraud Risk Score: 90\n";
echo "Risk Level: High Risk\n";
echo "Claim Status: Approved\n";
echo "Submitted Date: 2026-06-06\n\n";

echo "Report generated successfully.";
exit;
?>
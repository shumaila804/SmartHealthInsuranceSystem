<?php
session_start();
include("database.php");

if(isset($_SESSION['name']))
{
    $name = $_SESSION['name'];

    mysqli_query($conn,
    "INSERT INTO audit_logs(action_performed, performed_by)
    VALUES('User Logout','$name')");
}

session_unset();
session_destroy();

header("Location: login.php");
exit();
?>
<?php
include("database.php");

$error = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if(mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);

        if($password == $user['password'] || password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#0d6efd,#00c6ff);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}
.login-card{
    width:520px;
    background:white;
    padding:45px;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.login-card h1{
    color:#0d6efd;
    text-align:center;
    margin-bottom:10px;
}
.login-card p{
    text-align:center;
    color:#555;
    margin-bottom:30px;
}
.form-control{
    height:48px;
}
.btn{
    height:50px;
}
</style>
</head>

<body>

<div class="login-card">

    <h1>Smart Health Insurance</h1>
    <p>Claim Processing & Fraud Detection</p>

    <?php if($error != ""){ ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-4">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100">
            Login
        </button>

    </form>

</div>

</body>
</html>
<?php
include("../database.php");

if(isset($_POST['add_user']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    mysqli_query($conn,
    "INSERT INTO users(full_name,email,password,role)
    VALUES('$name','$email','$password','$role')");

    mysqli_query($conn,
    "INSERT INTO audit_logs(action_performed,performed_by)
    VALUES('New User Added: $name','Admin')");

    $success = "User Added Successfully";
}

if(isset($_GET['delete']))
{
    $id = $_GET['delete'];

    mysqli_query($conn,"DELETE FROM users WHERE user_id='$id'");

    mysqli_query($conn,
    "INSERT INTO audit_logs(action_performed,performed_by)
    VALUES('User Deleted ID $id','Admin')");

    header("Location: manage_users.php");
    exit();
}

$users = mysqli_query($conn,"SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body style="background:#eef3f9;">

<div class="container mt-4">

<div class="card shadow border-0">

<div class="card-header bg-dark text-white">

<h3>
<i class="bi bi-people"></i>
Admin User Management
</h3>

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

<div class="col-md-3">
<input
type="text"
name="name"
class="form-control"
placeholder="Full Name"
required>
</div>

<div class="col-md-3">
<input
type="email"
name="email"
class="form-control"
placeholder="Email"
required>
</div>

<div class="col-md-2">
<input
type="text"
name="password"
class="form-control"
placeholder="Password"
required>
</div>

<div class="col-md-2">

<select
name="role"
class="form-control">

<option>admin</option>
<option>hospital</option>
<option>officer</option>
<option>policyholder</option>

</select>

</div>

<div class="col-md-2">

<button
type="submit"
name="add_user"
class="btn btn-success w-100">

Add User

</button>

</div>

</div>

</form>

<hr>

<table class="table table-hover table-bordered">

<thead class="table-dark">

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php
while($row = mysqli_fetch_assoc($users))
{
?>

<tr>

<td><?php echo $row['user_id']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td>
<span class="badge bg-primary">
<?php echo $row['role']; ?>
</span>
</td>

<td>

<a
href="?delete=<?php echo $row['user_id']; ?>"
class="btn btn-danger btn-sm">

Delete

</a>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>
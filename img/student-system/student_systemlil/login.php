<?php
session_start();
include "config/db.php";

$dest = ['student'=>'student/dashboard.php','lecturer'=>'lecturer/dashboard.php','registration_officer'=>'admin/dashboard.php'];

if(!empty($_SESSION['user_id']) && isset($dest[$_SESSION['role'] ?? ''])){
    header("Location: ".$dest[$_SESSION['role']]); exit();
}

$error = "";
if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE username=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if($row && password_verify($_POST['password'], $row['password']) && isset($dest[$row['role']])){
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role']    = $row['role'];   // role comes from the database, not from the user
        header("Location: ".$dest[$row['role']]); exit();
    }
    $error = "Invalid username or password!";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - IAA Student System</title>
<style>
body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;background:#f0f2f5}
.card{background:#fff;padding:36px;border-radius:12px;box-shadow:0 4px 14px rgba(0,0,0,.12);text-align:center;width:340px;max-width:92%}
.card img{width:90px;margin-bottom:10px}
.card h2{margin:0 0 20px;color:#002666}
input{width:100%;padding:12px;margin:8px 0;border-radius:6px;border:1px solid #ccc;box-sizing:border-box}
button{width:100%;background:#007BFF;color:#fff;padding:12px;border:none;border-radius:6px;cursor:pointer;font-size:16px;margin-top:6px}
button:hover{background:#0056b3}
.error{color:#c00;margin-bottom:8px}
small{display:block;margin-top:16px;color:#777}
</style>
</head>
<body>
<div class="card">
  <img src="images/iaa_logo.jpeg" alt="IAA">
  <h2>Login</h2>
  <?php if($error) echo "<p class='error'>".htmlspecialchars($error)."</p>"; ?>
  <form method="POST" autocomplete="off">
    <input type="text" name="username" placeholder="Registration Number / Username" required autofocus>
    <input type="password" name="password" placeholder="Password" required>
    <button name="login">Login</button>
  </form>
  <small>Accounts are created by the Registration Officer.</small>
</div>
</body>
</html>

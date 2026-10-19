<?php
session_start();
include "../config/auth.php";
require_role('registration_officer');
$_SESSION['admin'] = "Admin";
include "../config/db.php";
include "../config/functions.php";

if(isset($_POST['save'])){
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    if(strlen($password) < 6){
        $msg = "<p class='error'>Password must be at least 6 characters.</p>";
    } else {
        $chk = mysqli_prepare($conn,"SELECT id FROM users WHERE username=?");
        mysqli_stmt_bind_param($chk,"s",$username); mysqli_stmt_execute($chk);
        if(mysqli_num_rows(mysqli_stmt_get_result($chk)) > 0){
            $msg = "<p class='error'>Username '".htmlspecialchars($username)."' already exists!</p>";
        } else {
            $photo = save_photo('photo', "../uploads/lecturers/");
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'lecturer'; // this form creates lecturers only
            $ins = mysqli_prepare($conn,"INSERT INTO users (username, full_name, phone, password, role, profile_photo) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($ins,"ssssss",$username,$fullname,$phone,$hash,$role,$photo);
            $msg = mysqli_stmt_execute($ins) ? "<p class='success'>Lecturer Registered Successfully</p>"
                                              : "<p class='error'>Error: ".htmlspecialchars(mysqli_error($conn))."</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Lecturer - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        
        /* SIDEBAR */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar-header {
            color: white;
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 15px;
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .sidebar-header p {
            margin: 5px 0 0 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav li {
            margin: 2px 0;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-nav a i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
        
        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #ffc107;
            padding-left: 25px;
        }
        
        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.2);
            border-left-color: #ffc107;
            font-weight: 500;
        }
        
        .sidebar-nav .divider {
            height: 1px;
            background: rgba(255,255,255,0.2);
            margin: 15px 20px;
        }
        
        .sidebar-nav .logout {
            margin-top: 20px;
        }
        
        .sidebar-nav .logout a {
            background: rgba(220, 53, 69, 0.8);
        }
        
        .sidebar-nav .logout a:hover {
            background: #dc3545;
        }
        
        /* MAIN CONTENT */
        .content {
            margin-left: 260px;
            padding: 25px;
            min-height: 100vh;
        }
        
        .content-header {
            background: white;
            padding: 20px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .content-header .badge {
            background: #e9ecef;
            color: #495057;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        /* CARD */
        .card {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            max-width: 500px;
            margin: 0 auto;
        }
        
        .card h2 {
            color: #002666;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            font-size: 14px;
            transition: border-color 0.15s ease-in-out;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-register {
            width: 100%;
            padding: 12px;
            background: #002666;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .btn-register:hover {
            background: #0047b3;
        }
        
        .success {
            text-align: center;
            color: #155724;
            background: #d4edda;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            text-align: center;
            color: #721c24;
            background: #f8d7da;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
        
        .info-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: -10px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-header h3, .sidebar-header p, .sidebar-nav a span {
                display: none;
            }
            .sidebar-nav a i {
                margin-right: 0;
                font-size: 1.4rem;
            }
            .content {
                margin-left: 70px;
            }
            .content-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3>Registration Panel</h3>
        <p>Academic Office</p>
    </div>
    
    <ul class="sidebar-nav">
        <li>
            <a href="dashboard.php">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="register_student.php">
                <i class="bi bi-person-plus-fill"></i>
                <span>Register Student</span>
            </a>
        </li>
        <li>
            <a href="view_students.php">
                <i class="bi bi-people-fill"></i>
                <span>View Students</span>
            </a>
        </li>
        <li>
            <a href="register_lecturer.php" class="active">
                <i class="bi bi-person-badge-fill"></i>
                <span>Register Lecturer</span>
            </a>
        </li>
        <li>
            <a href="view_lecturers.php">
                <i class="bi bi-person-workspace"></i>
                <span>View Lecturers</span>
            </a>
        </li>
        <li>
            <a href="view_courses.php">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span>Courses</span>
            </a>
        </li>
        
        <li class="divider"></li>
        
        <li class="logout">
            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    <div class="content-header">
        <h2>Register Lecturer</h2>
        <div class="badge">
            <i class="bi bi-person-circle"></i> 
            <?php echo htmlspecialchars($_SESSION['admin']); ?> 
            <span class="ms-2"><?php echo date('d/m/Y'); ?></span>
        </div>
    </div>

    <div class="card">
        <h2><i class="bi bi-person-badge-fill me-2" style="color: #002666;"></i>New Lecturer</h2>

        <?php if(isset($msg)) echo $msg; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" name="fullname" class="form-control" placeholder="Enter lecturer full name" required>
                </div>

            <div class="mb-2">
                <label class="form-label fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Login username" required>
                <div class="info-text">Lecturer will use this to login</div>
            </div>

            <div class="mb-2">
                <label class="form-label fw-bold">Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
            </div>

            

            <div class="mb-2">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter login password" required>
                <div class="info-text">Minimum 6 characters</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Profile Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <div class="info-text">Allowed: JPG, PNG, GIF. Max size: 2MB</div>
            </div>

            <button type="submit" name="save" class="btn-register">
                <i class="bi bi-person-plus-fill me-2"></i>Register Lecturer
            </button>
        </form>

        <hr class="my-4">

        <div class="text-center small text-muted">
            <i class="bi bi-info-circle-fill me-1"></i>
            After registering, the lecturer logs in, adds the courses they teach, and students of that program appear automatically.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
include "../config/auth.php";
require_role('registration_officer');
$_SESSION['admin'] = "Admin";
include "../config/db.php";
include "../config/functions.php";

if(isset($_POST['save'])){
    $name = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $sub_level = mysqli_real_escape_string($conn, $_POST['sub_level']);
    $program = $_POST['program'];
    $reg = mysqli_real_escape_string($conn, trim($_POST['regno'])); // USERNAME
    $academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
    $age = (int)$_POST['age'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'student'; // admin registers students only

    $exists = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM users WHERE username='$reg'"));
    if(!in_array($program, programs())){
        $msg = "Choose a valid program";
    } elseif($exists){
        $msg = "Registration number already exists";
    } else {
        $program = mysqli_real_escape_string($conn, $program);
        $photo = save_photo('photo', "../uploads/students/");
        mysqli_query($conn, "INSERT INTO students (fullname, gender, level, sub_level, program, registration_no, academic_year, age, photo)
            VALUES ('$name','$gender','$level','$sub_level','$program','$reg','$academic_year','$age','$photo')");
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$reg','$password','$role')");
        $msg = "Student Registered Successfully";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register Student</title>
<link rel="stylesheet" href="../css/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
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

/* MAIN CONTENT ADJUSTMENT */
.content-wrapper {
    margin-left: 260px;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.card {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    width: 450px;
    margin: 0 auto;
}
.card h2 {
    color: #002666;
    text-align: center;
    margin-bottom: 20px;
}
input[type="text"], input[type="number"], input[type="password"], select, input[type="file"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
button {
    width: 100%;
    padding: 12px;
    background: #002666;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}
button:hover { background: #0047b3; }
.success {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 10px;
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
    .content-wrapper {
        margin-left: 70px;
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
            <a href="register_student.php" class="active">
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
            <a href="register_lecturer.php">
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
<div class="content-wrapper">
    <form method="POST" enctype="multipart/form-data" class="card">
        <h2>Student Registration</h2>
        
        <?php if(isset($msg)) echo "<p class='success'>$msg</p>"; ?>
        
        <input type="text" name="fullname" placeholder="Full Name" required>
        
        <select name="gender" required>
            <option value="">-- Select Gender --</option>
            <option>Male</option>
            <option>Female</option>
        </select>
        
        <select name="level" required>
            <option value="">-- Select Level --</option>
            <option value="Undergraduate">Undergraduate</option>
            <option value="Postgraduate">Postgraduate</option>
        </select>
        
        <select name="sub_level" required>
            <option value="">-- Select Sub Level --</option>
            <option>Certificate</option>
            <option>Diploma</option>
            <option>Bachelor Degree</option>
            <option>Postgraduate Diploma</option>
            <option>Master Degree</option>
            <option>PhD</option>
        </select>
        
        <select name="program" required>
            <option value="">-- Select Program --</option>
            <option>Bachelor of Computer Science</option>
            <option>Bachelor of Information Technology</option>
            <option>Master of Computer Science</option>
        </select>
        
        <input type="text" name="regno" placeholder="Registration Number (Username)" required>
        
        <input type="password" name="password" placeholder="Set Login Password" required>
        
        <select name="academic_year" required>
            <option>2023/2024</option>
            <option>2024/2025</option>
            <option>2025/2026</option>
        </select>
        
        <input type="number" name="age" placeholder="Age of Student" required>
        
        <input type="file" name="photo" accept="image/*">
        
        <button name="save">Register Student</button>
    </form>
</div>

</body>
</html>
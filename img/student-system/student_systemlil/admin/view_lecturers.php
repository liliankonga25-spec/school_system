<?php
session_start();
include "../config/auth.php";
require_role('registration_officer');
$_SESSION['admin'] = "Admin";
include "../config/db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'registration_officer'){
    die("Access Denied");
}

/* ================= DELETE LECTURER ================= */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM courses WHERE lecturer_id='$id'");
    mysqli_query($conn,"DELETE FROM users WHERE id='$id' AND role='lecturer'");
    header("Location: view_lecturers.php");
    exit();
}

/* ================= UPDATE PASSWORD ================= */
if(isset($_POST['update_password'])){
    $id = (int)$_POST['lecturer_id'];
    $newpass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    mysqli_query($conn,"
        UPDATE users 
        SET password='$newpass' 
        WHERE id='$id' AND role='lecturer'
    ");

    $msg = "Password Updated Successfully!";
}

/* ================= UPLOAD PROFILE PHOTO ================= */
if(isset($_POST['upload_photo'])){
    $lecturer_id = $_POST['lecturer_id'];

    if(isset($_FILES['photo']) && $_FILES['photo']['error']==0){
        $photo_name = time()."_".$_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/lecturers/".$photo_name);

        mysqli_query($conn,"
            UPDATE users 
            SET profile_photo='$photo_name' 
            WHERE id='$lecturer_id'
        ");
        $msg = "Profile Photo Uploaded Successfully!";
    }
}

/* ================= FETCH LECTURERS ================= */
$lecturers = mysqli_query($conn,"SELECT * FROM users WHERE role='lecturer'");
?>
<!DOCTYPE html>
<html>
<head>
<title>View Lecturers</title>
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
    min-height: 100vh;
}

h2 {
    text-align: center;
    color: #002666;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
}

th, td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ccc;
}

th {
    background: #002666;
    color: white;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

tr:hover {
    background: #e0ebff;
}

img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.btn {
    padding: 6px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    margin: 2px;
}

.btn-delete {
    background: #dc3545;
    color: white;
    text-decoration: none;
}

.btn-update {
    background: #007bff;
    color: white;
}

.btn-upload {
    background: #28a745;
    color: white;
}

form {
    margin: 4px 0;
}

.success {
    color: #155724;
    text-align: center;
    font-weight: bold;
    margin-bottom: 10px;
    background: #d4edda;
    padding: 10px;
    border-radius: 6px;
}

input[type=password], input[type=file] {
    width: 100%;
    padding: 6px;
    margin-bottom: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
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
            <a href="register_lecturer.php">
                <i class="bi bi-person-badge-fill"></i>
                <span>Register Lecturer</span>
            </a>
        </li>
        <li>
            <a href="view_lecturers.php" class="active">
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
    <h2>Registered Lecturers</h2>

    <?php if(isset($msg)) echo "<p class='success'>$msg</p>"; ?>

    <table>
        <tr>
            <th>Username</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Profile Photo</th>
            <th>Upload Photo</th>
            <th>Update Password</th>
            <th>Action</th>
        </tr>

        <?php while($l = mysqli_fetch_assoc($lecturers)){ ?>
        <tr>
            <td><?= htmlspecialchars($l['username']) ?></td>
            <td><?= $l['phone'] ?? '-' ?></td>
            <td><?= $l['role'] ?></td>

            <td>
                <?php if($l['profile_photo']!=""){ ?>
                    <img src="../uploads/lecturers/<?= $l['profile_photo'] ?>" width="50" height="50">
                <?php } else echo "No Photo"; ?>
            </td>

            <!-- Upload Photo -->
            <td>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="lecturer_id" value="<?= $l['id'] ?>">
                    <input type="file" name="photo" required>
                    <button name="upload_photo" class="btn btn-upload">Upload</button>
                </form>
            </td>

            <!-- Update Password -->
            <td>
                <form method="POST">
                    <input type="hidden" name="lecturer_id" value="<?= $l['id'] ?>">
                    <input type="password" name="new_password" placeholder="New Password" required>
                    <button name="update_password" class="btn btn-update">Update</button>
                </form>
            </td>

            <!-- Delete -->
            <td>
                <a onclick="return confirm('Delete this lecturer?')"
                   href="?delete=<?= $l['id'] ?>" class="btn btn-delete" style="display: inline-block;">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
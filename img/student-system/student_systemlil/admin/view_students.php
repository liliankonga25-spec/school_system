<?php
session_start();
include "../config/auth.php";
require_role('registration_officer');
$_SESSION['admin'] = "Admin";
include "../config/db.php";

/* ================= DELETE STUDENT ================= */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    $q = mysqli_query($conn,"SELECT photo, registration_no FROM students WHERE id='$id'");
    $row = mysqli_fetch_assoc($q);

    if($row && $row['photo'] != ""){
        $path = "../uploads/students/".$row['photo'];
        if(file_exists($path)){
            unlink($path);
        }
    }

    // DELETE STUDENT
    mysqli_query($conn,"DELETE FROM students WHERE id='$id'");
    // DELETE LOGIN ACCOUNT
    mysqli_query($conn,"DELETE FROM users WHERE username='".mysqli_real_escape_string($conn,$row['registration_no'])."'");

    header("Location: view_students.php");
    exit();
}

/* ================= UPDATE PASSWORD FUNCTIONALITY ================= */
if(isset($_POST['update_password'])){
    $regno = mysqli_real_escape_string($conn, $_POST['regno']);
    $new_pass = $_POST['new_password'];
    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

    mysqli_query($conn,"UPDATE users 
        SET password='$hashed', plain_password=NULL 
        WHERE username='$regno'"
    );

    $msg = "Password updated successfully for $regno";
}

/* ================= FETCH STUDENTS + PASSWORD ================= */
$data = mysqli_query($conn,"
    SELECT s.*, u.password, u.plain_password
    FROM students s 
    LEFT JOIN users u ON s.registration_no = u.username
    ORDER BY s.id DESC
");

/* ================= FETCH PROGRAMS ================= */
$programs_res = mysqli_query($conn,"SELECT DISTINCT program FROM students ORDER BY program ASC");
$programs = [];
while($p=mysqli_fetch_assoc($programs_res)){
    $programs[]=$p['program'];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Registered Students</title>
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
    min-height: 100vh;
}

h2 {
    color: #002666;
    text-align: center;
    margin-bottom: 20px;
}

.search-box {
    width: 100%;
    max-width: 400px;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

#studentsTable {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

#studentsTable th,
#studentsTable td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}

#studentsTable th {
    background: #002666;
    color: #fff;
}

#studentsTable tr:nth-child(even) {
    background: #f2f2f2;
}

#studentsTable tr:hover {
    background: #cce0ff;
}

.action-btn {
    background: #cc0000;
    color: #fff;
    padding: 5px 8px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    display: block;
    margin-bottom: 4px;
    cursor: pointer;
    border: none;
    width: 100%;
}

.edit-btn {
    background: #28a745;
}

.action-btn:hover {
    opacity: .8;
}

.update-form {
    display: none;
    margin-top: 5px;
}

.update-form input {
    width: 100%;
    padding: 5px;
    margin-bottom: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.update-form button {
    background: #002666;
    color: white;
    border: none;
    padding: 5px;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
}

.success-msg {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 10px;
}

/* PRINT SECTION */
.print-section {
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
    align-items: center;
}

#printBtn {
    background: #002666;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
}

#printBtn:hover {
    background: #0047b3;
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
            <a href="view_students.php" class="active">
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
    <h2>Registered Students</h2>

    <?php if(isset($msg)) echo "<p class='success-msg'>$msg</p>"; ?>

    <input type="text" id="searchInput" class="search-box"
    placeholder="Search by Name, Sub Level or Registration Number">

    <div class="print-section">
        <label>Select Program to Print:</label>
        <select id="programSelect">
            <option value="">-- All Programs --</option>
            <?php foreach($programs as $p){ ?>
            <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
            <?php } ?>
        </select>
        <button id="printBtn">Print List</button>
    </div>

    <table id="studentsTable">
        <tr>
            <th>Photo</th>
            <th>Full Name</th>
            <th>Reg No</th>
            <th>Gender</th>
            <th>Level</th>
            <th>Sub Level</th>
            <th>Program</th>
            <th>Academic Year</th>
            <th>Age</th>
            <th>Actions</th>
            <th>Edit Password</th>
        </tr>

        <?php while($row=mysqli_fetch_assoc($data)){ ?>
        <tr data-program="<?= $row['program'] ?>">
            <td><?php if($row['photo']!=""){ ?><img src="../uploads/students/<?= $row['photo'] ?>" width="50"><?php } ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['registration_no']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['level']) ?></td>
            <td><?= htmlspecialchars($row['sub_level']) ?></td>
            <td><?= htmlspecialchars($row['program']) ?></td>
            <td><?= htmlspecialchars($row['academic_year']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td>
                <a class="action-btn" href="view_students.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
            </td>
            <td>
                <button class="action-btn edit-btn" onclick="toggleForm('form-<?= $row['id'] ?>')">Edit</button>
                <form method="POST" class="update-form" id="form-<?= $row['id'] ?>">
                    <input type="hidden" name="regno" value="<?= $row['registration_no'] ?>">
                    <input type="text" name="new_password" placeholder="New Password" required>
                    <button type="submit" name="update_password">Update</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<script>
// Toggle password update form
function toggleForm(formId) {
    var form = document.getElementById(formId);
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

// Search functionality
document.getElementById("searchInput").addEventListener("keyup", function(){
    let f = this.value.toLowerCase();
    document.querySelectorAll("#studentsTable tr").forEach((r, i)=>{
        if(i > 0) r.style.display = r.innerText.toLowerCase().includes(f) ? "" : "none";
    });
});

// Print functionality
document.getElementById("printBtn").addEventListener("click", ()=>{
    let p = document.getElementById("programSelect").value;
    let rows = document.querySelectorAll("#studentsTable tr[data-program]");
    let html = "<table border='1' style='border-collapse: collapse; width: 100%;'><tr><th>Name</th><th>Reg</th><th>Program</th></tr>";
    rows.forEach(r=>{
        if(p == "" || r.dataset.program == p){
            html += `<tr><td>${r.cells[1].innerText}</td><td>${r.cells[2].innerText}</td><td>${r.cells[6].innerText}</td></tr>`;
        }
    });
    html += "</table>";
    let w = window.open();
    w.document.write(html);
    w.print();
});
</script>

</body>
</html>
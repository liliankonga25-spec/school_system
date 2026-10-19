<?php
session_start();
include "../config/auth.php";
require_role('registration_officer');
$_SESSION['admin'] = "Admin";
include "../config/db.php";

// Get counts from database
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"))['total'];
$total_lecturers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='lecturer'"))['total'];
$total_programs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT program) as total FROM students"))['total'];
$total_results = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM results"))['total'];

// Get recent students for activities
$recent_students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC LIMIT 3");

// Get recent results with student details
$recent_results = mysqli_query($conn, "
    SELECT r.*, s.fullname, s.program, s.registration_no 
    FROM results r 
    JOIN students s ON r.student_id = s.id 
    ORDER BY r.id DESC LIMIT 5
");

// Get lecturer statistics
$lecturer_stats = mysqli_query($conn, "
    SELECT u.username,
           (SELECT COUNT(DISTINCT s.id) FROM students s JOIN courses c ON c.program=s.program WHERE c.lecturer_id=u.id) AS student_count,
           (SELECT COUNT(*) FROM results r WHERE r.lecturer_id=u.id) AS result_count
    FROM users u WHERE u.role='lecturer'
    ORDER BY student_count DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Registration Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        
        /* SIDEBAR YA KUDUMU */
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
        
        /* ENEO LA CONTENT */
        .content {
            margin-left: 260px;
            padding: 25px;
            min-height: 100vh;
        }
        
        /* HEADER YA CONTENT */
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
        
        /* CARDS ZA STATISTICS */
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .card-body {
            padding: 20px;
        }
        
        .stat-card .card-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .stat-card h2 {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.5;
        }
        
        /* MUONEKANO WA JEDWALI */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 25px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
        
        .table th {
            padding: 15px 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        
        .badge-score {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .program-badge {
            background: #e9ecef;
            color: #495057;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        /* LECTURER CARD */
        .lecturer-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .lecturer-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-item .number {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .stat-item .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: visible;
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
            <a href="dashboard.php" class="active">
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
        <h2>Registration Officer Dashboard</h2>
        <div class="badge">
            <i class="bi bi-person-circle"></i> 
            <?php echo htmlspecialchars($_SESSION['admin']); ?> 
            <span class="ms-2"><?php echo date('d/m/Y'); ?></span>
        </div>
    </div>
    
    <!-- Quick Stats Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Students</h5>
                            <h2><?php echo $total_students ?: 0; ?></h2>
                        </div>
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Lecturers</h5>
                            <h2><?php echo $total_lecturers ?: 0; ?></h2>
                        </div>
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Programs</h5>
                            <h2><?php echo $total_programs ?: 0; ?></h2>
                        </div>
                        <i class="bi bi-book-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Results</h5>
                            <h2><?php echo $total_results ?: 0; ?></h2>
                        </div>
                        <i class="bi bi-file-text-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lecturer Overview Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="table-container">
                <h4 class="mb-4">
                    <i class="bi bi-person-workspace me-2 text-primary"></i>
                    Lecturer Performance Overview
                </h4>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Lecturer</th>
                                <th>Students Assigned</th>
                                <th>Results Uploaded</th>
                                <th>Performance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($lecturer_stats) > 0): ?>
                                <?php while($stat = mysqli_fetch_assoc($lecturer_stats)): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-person-circle me-2 text-primary"></i>
                                        <?php echo htmlspecialchars($stat['username']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $stat['student_count'] ?: 0; ?> students</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo $stat['result_count'] ?: 0; ?> results</span>
                                    </td>
                                    <td>
                                        <?php 
                                        $performance = 0;
                                        if($stat['student_count'] > 0) {
                                            $performance = round(($stat['result_count'] / $stat['student_count']) * 100);
                                        }
                                        ?>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?php echo $performance; ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?php echo $performance; ?>% completion</small>
                                    </td>
                                    <td>
                                        <?php if($stat['result_count'] > 0): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No lecturers found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Results Table -->
    <div class="table-container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-file-earmark-spreadsheet-fill me-2 text-primary"></i>
                Recently Uploaded Results
            </h4>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Course</th>
                        <th>Marks</th>
                        <th>Grade Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($recent_results) > 0): ?>
                        <?php while($result = mysqli_fetch_assoc($recent_results)): ?>
                        <tr>
                            <td>
                                <i class="bi bi-person-circle me-2 text-primary"></i>
                                <?php echo htmlspecialchars($result['fullname']); ?>
                            </td>
                            <td>
                                <span class="program-badge"><?php echo htmlspecialchars($result['program']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($result['course']); ?></td>
                            <td><span class="badge-score"><?php echo $result['marks']; ?></span></td>
                            <td><?php echo $result['grade_point']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No results uploaded yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Activities and Quick Info -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="table-container">
                <h5 class="mb-3">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    Recent Activities
                </h5>
                <ul class="list-unstyled">
                    <?php if(mysqli_num_rows($recent_students) > 0): ?>
                        <?php while($student = mysqli_fetch_assoc($recent_students)): ?>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            New student registered: <?php echo $student['fullname']; ?> (<?php echo $student['registration_no']; ?>)
                            <small class="text-muted d-block"><?php echo date('d/m/Y H:i', strtotime($student['created_at'])); ?></small>
                        </li>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <li class="mb-2">
                        <i class="bi bi-person-plus text-primary me-2"></i>
                        Total Lecturers: <?php echo $total_lecturers ?: 0; ?>
                        <small class="text-muted d-block">Active in system</small>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="table-container">
                <h5 class="mb-3">
                    <i class="bi bi-calendar-check me-2 text-primary"></i>
                    System Overview
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Total Students:</strong> <?php echo $total_students ?: 0; ?>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Total Lecturers:</strong> <?php echo $total_lecturers ?: 0; ?>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Total Programs:</strong> <?php echo $total_programs ?: 0; ?>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Total Results:</strong> <?php echo $total_results ?: 0; ?>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Student-Lecturer Ratio:</strong> 
                        <?php 
                        if($total_lecturers > 0) {
                            echo round($total_students / $total_lecturers, 1) . ":1";
                        } else {
                            echo "N/A";
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Optional: Add active class to current page based on URL
    document.addEventListener('DOMContentLoaded', function() {
        const currentLocation = window.location.pathname;
        const navLinks = document.querySelectorAll('.sidebar-nav a');
        
        navLinks.forEach(link => {
            const linkPath = link.getAttribute('href');
            if (currentLocation.includes(linkPath)) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>
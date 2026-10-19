<?php
include "../config/db.php";
include "../config/auth.php";
include "../config/functions.php";
include "../config/layout.php";
require_role('student');
$uid_user = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$uid_user"));
$st = mysqli_prepare($conn,"SELECT * FROM students WHERE registration_no=? LIMIT 1");
mysqli_stmt_bind_param($st,"s",$user['username']);
mysqli_stmt_execute($st);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if(!$student) die("Student record not found. Contact the Registration Officer.");
$sid = (int)$student['id'];
$who = $student['fullname'];

function student_results($conn, $sid){
    return mysqli_query($conn,"SELECT r.*, COALESCE(u.full_name,u.username) AS lecturer_name
        FROM results r LEFT JOIN users u ON u.id=r.lecturer_id WHERE r.student_id=$sid ORDER BY r.course");
}

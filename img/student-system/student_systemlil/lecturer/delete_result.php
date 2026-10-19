<?php
include "../config/db.php"; include "../config/auth.php"; include "../config/functions.php";
require_role('lecturer');
$lid = (int)$_SESSION['user_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id = (int)$_POST['id'];
    $r = mysqli_fetch_assoc(mysqli_query($conn,"SELECT student_id FROM results WHERE id=$id AND lecturer_id=$lid"));
    if($r){
        mysqli_query($conn,"DELETE FROM results WHERE id=$id AND lecturer_id=$lid");
        update_student_gpa($r['student_id'],$conn);
        $_SESSION['flash'] = "Result deleted.";
    }
}
header("Location: my_results.php"); exit();

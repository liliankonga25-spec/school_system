<?php
function grade_point($marks){
    if($marks >= 70) return 5.0;
    if($marks >= 60) return 4.0;
    if($marks >= 50) return 3.0;
    if($marks >= 40) return 2.0;
    return 0.0;
}
function grade_letter($marks){
    if($marks >= 70) return 'A';
    if($marks >= 60) return 'B';
    if($marks >= 50) return 'C';
    if($marks >= 40) return 'D';
    return 'F';
}
function programs(){
    return ['Bachelor of Computer Science','Bachelor of Information Technology','Master of Computer Science'];
}
function calculate_gpa($student_id, $conn){
    $student_id = (int)$student_id;
    $res = mysqli_query($conn, "SELECT marks, credits FROM results WHERE student_id=$student_id");
    $pts = 0; $cr = 0;
    while($r = mysqli_fetch_assoc($res)){
        $pts += grade_point($r['marks']) * $r['credits'];
        $cr  += $r['credits'];
    }
    return $cr == 0 ? 0 : round($pts/$cr, 2);
}
function update_student_gpa($student_id, $conn){
    $gpa = calculate_gpa($student_id, $conn);
    $sid = (int)$student_id;
    mysqli_query($conn, "UPDATE students SET gpa='$gpa' WHERE id=$sid");
}

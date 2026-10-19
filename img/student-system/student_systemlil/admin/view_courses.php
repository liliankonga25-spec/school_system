<?php
include "../config/db.php"; include "../config/auth.php"; include "../config/functions.php"; include "../config/layout.php";
require_role('registration_officer');
$rows = mysqli_query($conn,"SELECT c.*, COALESCE(u.full_name,u.username) AS lecturer,
   (SELECT COUNT(*) FROM students s WHERE s.program=c.program) AS n_students,
   (SELECT COUNT(*) FROM results r WHERE r.course_id=c.id) AS n_results
   FROM courses c JOIN users u ON u.id=c.lecturer_id ORDER BY c.program, c.course_name");
panel_start("Courses","registration_officer","view_courses.php","Academic Office");
?>
<h3 class="mb-4">Courses added by lecturers</h3>
<div class="box"><div class="table-responsive"><table class="table">
<thead><tr><th>Course</th><th>Program</th><th>Lecturer</th><th>Credits</th><th>Students</th><th>Results</th></tr></thead><tbody>
<?php if(mysqli_num_rows($rows)==0) echo "<tr><td colspan='6' class='text-center text-muted'>No courses yet</td></tr>"; ?>
<?php while($c=mysqli_fetch_assoc($rows)): ?>
<tr><td><?= e($c['course_name']) ?></td><td><?= e($c['program']) ?></td><td><?= e($c['lecturer']) ?></td><td><?= $c['credits'] ?></td><td><?= $c['n_students'] ?></td><td><?= $c['n_results'] ?></td></tr>
<?php endwhile; ?></tbody></table></div></div>
<?php panel_end();

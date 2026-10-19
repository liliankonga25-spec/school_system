<?php
include "_init.php";
$n_courses  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM courses WHERE lecturer_id=$lid"))[0];
$n_students = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(DISTINCT s.id) FROM students s JOIN courses c ON c.program=s.program WHERE c.lecturer_id=$lid"))[0];
$n_results  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM results WHERE lecturer_id=$lid"))[0];
$recent = mysqli_query($conn,"SELECT r.*, s.fullname, s.registration_no FROM results r JOIN students s ON s.id=r.student_id WHERE r.lecturer_id=$lid ORDER BY r.uploaded_at DESC LIMIT 5");
panel_start("Lecturer Dashboard","lecturer","dashboard.php",$who);
?>
<h3 class="mb-4">Welcome, <?= e($who) ?> 👋</h3>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="stat bg-primary"><div>My Courses</div><h2><?= $n_courses ?></h2></div></div>
  <div class="col-md-4"><div class="stat bg-success"><div>Students (auto)</div><h2><?= $n_students ?></h2></div></div>
  <div class="col-md-4"><div class="stat bg-info"><div>Results Uploaded</div><h2><?= $n_results ?></h2></div></div>
</div>
<?php if($n_courses==0): ?>
<div class="alert alert-warning">You have not added any course yet. <a href="courses.php">Add your courses</a> — students of that program will appear automatically.</div>
<?php endif; ?>
<div class="box">
  <h5>Recently uploaded results</h5>
  <div class="table-responsive"><table class="table">
  <thead><tr><th>Student</th><th>Reg No</th><th>Course</th><th>Marks</th><th>Date</th></tr></thead><tbody>
  <?php if(mysqli_num_rows($recent)==0) echo "<tr><td colspan='5' class='text-muted text-center'>No results yet</td></tr>"; ?>
  <?php while($r=mysqli_fetch_assoc($recent)): ?>
    <tr><td><?= e($r['fullname']) ?></td><td><?= e($r['registration_no']) ?></td><td><?= e($r['course']) ?></td><td><?= e($r['marks']) ?></td><td><?= date('d/m/Y',strtotime($r['uploaded_at'])) ?></td></tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>
<?php panel_end();

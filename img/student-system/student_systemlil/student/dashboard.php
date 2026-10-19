<?php
include "_init.php";
$gpa = calculate_gpa($sid,$conn);
$s = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) n, COALESCE(SUM(credits),0) cr, COALESCE(AVG(marks),0) av FROM results WHERE student_id=$sid"));
$recent = mysqli_query($conn,"SELECT * FROM results WHERE student_id=$sid ORDER BY uploaded_at DESC LIMIT 5");
panel_start("Student Dashboard","student","dashboard.php",$who);
?>
<div class="box d-flex align-items-center gap-3">
  <?php if($student['photo'] && file_exists("../uploads/students/".$student['photo'])): ?><img class="avatar-lg" src="../uploads/students/<?= e($student['photo']) ?>">
  <?php else: ?><div class="ph" style="width:80px;height:80px;font-size:2rem"><?= e(strtoupper(substr($who,0,1))) ?></div><?php endif; ?>
  <div><h4 class="mb-1">Welcome, <?= e($who) ?></h4>
  <div class="text-muted"><?= e($student['registration_no']) ?> · <?= e($student['program']) ?> · <?= e($student['academic_year']) ?></div></div>
</div>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="stat bg-primary"><div>GPA</div><h2><?= number_format($gpa,2) ?></h2></div></div>
  <div class="col-6 col-md-3"><div class="stat bg-success"><div>Courses</div><h2><?= $s['n'] ?></h2></div></div>
  <div class="col-6 col-md-3"><div class="stat bg-info"><div>Credits</div><h2><?= $s['cr'] ?></h2></div></div>
  <div class="col-6 col-md-3"><div class="stat bg-secondary"><div>Average</div><h2><?= round($s['av'],1) ?></h2></div></div>
</div>
<div class="box">
  <div class="d-flex justify-content-between"><h5>Latest results</h5>
    <a class="btn btn-sm btn-outline-primary" href="my_results.php">View all</a></div>
  <div class="table-responsive"><table class="table"><thead><tr><th>Course</th><th>Marks</th><th>Grade</th></tr></thead><tbody>
  <?php if(mysqli_num_rows($recent)==0) echo "<tr><td colspan='3' class='text-muted text-center'>No results published yet</td></tr>"; ?>
  <?php while($r=mysqli_fetch_assoc($recent)): ?>
    <tr><td><?= e($r['course']) ?></td><td><?= $r['marks'] ?></td><td><?= grade_letter($r['marks']) ?></td></tr>
  <?php endwhile; ?></tbody></table></div>
</div>
<?php panel_end();

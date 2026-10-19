<?php
include "_init.php";
$msg = $err = "";
$cid = (int)($_GET['course_id'] ?? $_POST['course_id'] ?? 0);

$mine = mysqli_query($conn,"SELECT * FROM courses WHERE lecturer_id=$lid ORDER BY program, course_name");
$course = null;
if($cid){
    $course = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM courses WHERE id=$cid AND lecturer_id=$lid"));
}

if($course && isset($_POST['save_marks'])){
    $saved = 0;
    $st = mysqli_prepare($conn,"INSERT INTO results (student_id,lecturer_id,course_id,course,marks,credits,grade_point)
                                VALUES (?,?,?,?,?,?,?)
                                ON DUPLICATE KEY UPDATE marks=VALUES(marks), credits=VALUES(credits), grade_point=VALUES(grade_point), lecturer_id=VALUES(lecturer_id), course=VALUES(course)");
    foreach(($_POST['marks'] ?? []) as $sid => $m){
        if($m === '' ) continue;
        $sid = (int)$sid; $m = (int)round((float)$m);
        if($m < 0 || $m > 100) { $err = "Marks must be between 0 and 100."; continue; }
        // only students of this course's program
        $ok = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM students WHERE id=$sid AND program='".mysqli_real_escape_string($conn,$course['program'])."'"))[0];
        if(!$ok) continue;
        $gp = grade_point($m);
        mysqli_stmt_bind_param($st,"iiisiid",$sid,$lid,$cid,$course['course_name'],$m,$course['credits'],$gp);
        if(mysqli_stmt_execute($st)){ $saved++; update_student_gpa($sid,$conn); }
    }
    if($saved) $msg = "Saved results for $saved student(s).";
}

$students = $existing = [];
if($course){
    $p = mysqli_real_escape_string($conn,$course['program']);
    $q = mysqli_query($conn,"SELECT * FROM students WHERE program='$p' ORDER BY fullname");
    while($r=mysqli_fetch_assoc($q)) $students[]=$r;
    $q = mysqli_query($conn,"SELECT student_id, marks FROM results WHERE course_id=$cid");
    while($r=mysqli_fetch_assoc($q)) $existing[$r['student_id']]=$r['marks'];
}
panel_start("Upload Results","lecturer","upload_results.php",$who);
?>
<h3 class="mb-4">Upload Results</h3>
<?php if($msg) echo "<div class='alert alert-success'>".e($msg)."</div>"; if($err) echo "<div class='alert alert-danger'>".e($err)."</div>"; ?>
<div class="box">
  <form method="GET" class="row g-2">
    <div class="col-md-8"><select class="form-select" name="course_id" onchange="this.form.submit()">
      <option value="">-- Select the course --</option>
      <?php while($c=mysqli_fetch_assoc($mine)): ?>
        <option value="<?= $c['id'] ?>" <?= $c['id']==$cid?'selected':'' ?>><?= e($c['course_name']) ?> — <?= e($c['program']) ?></option>
      <?php endwhile; ?>
    </select></div>
    <div class="col-md-4"><a href="courses.php" class="btn btn-outline-primary w-100">Manage courses</a></div>
  </form>
</div>
<?php if($course): ?>
<div class="box">
  <h5><?= e($course['course_name']) ?> <small class="text-muted">· <?= e($course['program']) ?> · <?= $course['credits'] ?> credits</small></h5>
  <?php if(!$students): ?>
    <div class="alert alert-info mb-0">No student is registered in this program yet.</div>
  <?php else: ?>
  <form method="POST">
    <input type="hidden" name="course_id" value="<?= $cid ?>">
    <div class="table-responsive"><table class="table align-middle">
      <thead><tr><th>Reg No</th><th>Student</th><th style="width:140px">Marks (0-100)</th><th>Grade</th></tr></thead><tbody>
      <?php foreach($students as $s): $m = $existing[$s['id']] ?? ''; ?>
        <tr><td><?= e($s['registration_no']) ?></td><td><?= e($s['fullname']) ?></td>
            <td><input class="form-control" type="number" min="0" max="100" step="1" name="marks[<?= $s['id'] ?>]" value="<?= e($m) ?>"></td>
            <td><?= $m!=='' ? grade_letter($m) : '-' ?></td></tr>
      <?php endforeach; ?>
      </tbody></table></div>
    <button class="btn btn-success" name="save_marks"><i class="bi bi-cloud-upload"></i> Save results</button>
    <small class="text-muted ms-2">Empty rows are skipped. Saving again updates the marks.</small>
  </form>
  <?php endif; ?>
</div>
<div class="box"><small class="text-muted">Grading: 70-100 A (5.0) · 60-69 B (4.0) · 50-59 C (3.0) · 40-49 D (2.0) · below 40 F (0.0)</small></div>
<?php endif; ?>
<?php panel_end();

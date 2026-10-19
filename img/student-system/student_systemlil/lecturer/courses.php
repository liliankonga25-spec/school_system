<?php
include "_init.php";
$msg = $err = "";

if(isset($_POST['add_course'])){
    $name = trim($_POST['course_name']);
    $prog = $_POST['program'];
    $cred = max(1, (int)$_POST['credits']);
    if($name === "" || !in_array($prog, programs())){
        $err = "Enter the course name and choose a program.";
    } else {
        $st = mysqli_prepare($conn,"INSERT IGNORE INTO courses (lecturer_id,course_name,program,credits) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($st,"issi",$lid,$name,$prog,$cred);
        mysqli_stmt_execute($st);
        $msg = mysqli_stmt_affected_rows($st) ? "Course added. Students of $prog are now linked automatically." : "You already added this course for that program.";
    }
}
if(isset($_POST['delete_course'])){
    $cid = (int)$_POST['course_id'];
    $has = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM results WHERE course_id=$cid AND lecturer_id=$lid"))[0];
    if($has > 0) $err = "Cannot delete: results were already uploaded for this course.";
    else { mysqli_query($conn,"DELETE FROM courses WHERE id=$cid AND lecturer_id=$lid"); $msg = "Course deleted."; }
}
$courses = mysqli_query($conn,"SELECT c.*, (SELECT COUNT(*) FROM students s WHERE s.program=c.program) AS n_students,
                                (SELECT COUNT(*) FROM results r WHERE r.course_id=c.id) AS n_results
                               FROM courses c WHERE c.lecturer_id=$lid ORDER BY c.program, c.course_name");
panel_start("My Courses","lecturer","courses.php",$who);
?>
<h3 class="mb-4">My Courses</h3>
<?php if($msg) echo "<div class='alert alert-success'>".e($msg)."</div>"; if($err) echo "<div class='alert alert-danger'>".e($err)."</div>"; ?>
<div class="box">
  <h5>Add a course you teach</h5>
  <form method="POST" class="row g-2">
    <div class="col-md-4"><input class="form-control" name="course_name" placeholder="Course name e.g. Database Systems" required></div>
    <div class="col-md-4"><select class="form-select" name="program" required>
      <option value="">-- Program of students --</option>
      <?php foreach(programs() as $p) echo "<option>".e($p)."</option>"; ?>
    </select></div>
    <div class="col-md-2"><input class="form-control" type="number" name="credits" value="3" min="1" max="20" title="Credits"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100" name="add_course">Add</button></div>
  </form>
</div>
<div class="box">
  <div class="table-responsive"><table class="table align-middle">
  <thead><tr><th>Course</th><th>Program</th><th>Credits</th><th>Students</th><th>Results</th><th></th></tr></thead><tbody>
  <?php if(mysqli_num_rows($courses)==0) echo "<tr><td colspan='6' class='text-muted text-center'>No courses yet</td></tr>"; ?>
  <?php while($c=mysqli_fetch_assoc($courses)): ?>
    <tr><td><strong><?= e($c['course_name']) ?></strong></td><td><?= e($c['program']) ?></td><td><?= $c['credits'] ?></td>
        <td><span class="badge bg-success"><?= $c['n_students'] ?></span></td><td><?= $c['n_results'] ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-success" href="upload_results.php?course_id=<?= $c['id'] ?>"><i class="bi bi-upload"></i> Results</a>
          <form method="POST" class="d-inline" onsubmit="return confirm('Delete this course?')">
            <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" name="delete_course"><i class="bi bi-trash"></i></button>
          </form></td></tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>
<?php panel_end();

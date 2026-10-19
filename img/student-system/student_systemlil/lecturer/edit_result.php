<?php
include "_init.php";
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT r.*, s.fullname, s.registration_no FROM results r JOIN students s ON s.id=r.student_id WHERE r.id=$id AND r.lecturer_id=$lid"));
if(!$res){ header("Location: my_results.php"); exit(); }
$err = "";
if(isset($_POST['update_result'])){
    $m = (int)$_POST['marks'];
    if($m<0 || $m>100) $err = "Marks must be between 0 and 100.";
    else {
        $gp = grade_point($m);
        mysqli_query($conn,"UPDATE results SET marks=$m, grade_point=$gp WHERE id=$id AND lecturer_id=$lid");
        update_student_gpa($res['student_id'],$conn);
        $_SESSION['flash'] = "Result updated.";
        header("Location: my_results.php"); exit();
    }
}
panel_start("Edit Result","lecturer","my_results.php",$who);
?>
<h3 class="mb-4">Edit Result</h3>
<div class="box" style="max-width:520px">
  <?php if($err) echo "<div class='alert alert-danger'>".e($err)."</div>"; ?>
  <p><strong><?= e($res['fullname']) ?></strong> (<?= e($res['registration_no']) ?>)<br><span class="text-muted"><?= e($res['course']) ?></span></p>
  <form method="POST">
    <input type="hidden" name="id" value="<?= $id ?>">
    <label class="form-label">Marks (0-100)</label>
    <input class="form-control mb-3" type="number" min="0" max="100" name="marks" value="<?= e($res['marks']) ?>" required>
    <button class="btn btn-success" name="update_result">Save</button> <a class="btn btn-light" href="my_results.php">Cancel</a>
  </form>
</div>
<?php panel_end();

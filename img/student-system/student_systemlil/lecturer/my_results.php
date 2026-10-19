<?php
include "_init.php";
$msg = $_SESSION['flash'] ?? ""; unset($_SESSION['flash']);
$rows = mysqli_query($conn,"SELECT r.*, s.fullname, s.registration_no, s.program FROM results r JOIN students s ON s.id=r.student_id
                            WHERE r.lecturer_id=$lid ORDER BY r.course, s.fullname");
panel_start("My Results","lecturer","my_results.php",$who);
?>
<h3 class="mb-4">My Results</h3>
<?php if($msg) echo "<div class='alert alert-success'>".e($msg)."</div>"; ?>
<div class="box">
  <input id="q" class="form-control mb-3" placeholder="Search by student, reg no or course...">
  <div class="table-responsive"><table class="table align-middle" id="t">
  <thead><tr><th>Reg No</th><th>Student</th><th>Course</th><th>Marks</th><th>Grade</th><th>Date</th><th></th></tr></thead><tbody>
  <?php if(mysqli_num_rows($rows)==0) echo "<tr><td colspan='7' class='text-muted text-center'>No results uploaded</td></tr>"; ?>
  <?php while($r=mysqli_fetch_assoc($rows)): ?>
    <tr><td><?= e($r['registration_no']) ?></td><td><?= e($r['fullname']) ?></td><td><?= e($r['course']) ?></td>
        <td><span class="badge bg-<?= $r['marks']>=40?'success':'danger' ?>"><?= $r['marks'] ?></span></td>
        <td><?= grade_letter($r['marks']) ?></td><td><?= date('d/m/Y',strtotime($r['uploaded_at'])) ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-primary" href="edit_result.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="delete_result.php" onsubmit="return confirm('Delete this result?')">
            <input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td></tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>
<script>document.getElementById('q').onkeyup=function(){var f=this.value.toLowerCase();document.querySelectorAll('#t tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(f)?'':'none')}</script>
<?php panel_end();

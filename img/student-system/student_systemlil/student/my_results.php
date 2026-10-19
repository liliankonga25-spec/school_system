<?php
include "_init.php";
$gpa = calculate_gpa($sid,$conn);
$rows = student_results($conn,$sid);
panel_start("My Results","student","my_results.php",$who);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h3 class="mb-0">My Results</h3>
  <div>
    <a class="btn btn-primary" href="download_result.php" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
    <a class="btn btn-success" href="download_result.php?format=csv"><i class="bi bi-file-earmark-excel"></i> Download Excel</a>
  </div>
</div>
<div class="box">
  <div class="table-responsive"><table class="table align-middle">
  <thead><tr><th>Course</th><th>Marks</th><th>Grade</th><th>Grade Point</th><th>Credits</th><th>Lecturer</th></tr></thead><tbody>
  <?php if(mysqli_num_rows($rows)==0) echo "<tr><td colspan='6' class='text-muted text-center'>No results published yet</td></tr>"; ?>
  <?php while($r=mysqli_fetch_assoc($rows)): ?>
    <tr><td><?= e($r['course']) ?></td><td><span class="badge bg-<?= $r['marks']>=40?'success':'danger' ?>"><?= $r['marks'] ?></span></td>
        <td><?= grade_letter($r['marks']) ?></td><td><?= number_format(grade_point($r['marks']),1) ?></td><td><?= $r['credits'] ?></td><td><?= e($r['lecturer_name']) ?></td></tr>
  <?php endwhile; ?>
  </tbody><tfoot><tr><th colspan="3" class="text-end">GPA</th><th colspan="3"><?= number_format($gpa,2) ?></th></tr></tfoot></table></div>
</div>
<?php panel_end();

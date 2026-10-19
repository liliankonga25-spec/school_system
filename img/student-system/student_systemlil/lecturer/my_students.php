<?php
include "_init.php";
$students = mysqli_query($conn,"SELECT s.*, GROUP_CONCAT(DISTINCT c.course_name ORDER BY c.course_name SEPARATOR ', ') AS my_courses
    FROM students s JOIN courses c ON c.program=s.program
    WHERE c.lecturer_id=$lid GROUP BY s.id ORDER BY s.program, s.fullname");
panel_start("My Students","lecturer","my_students.php",$who);
?>
<h3 class="mb-2">My Students</h3>
<p class="text-muted">Students are listed automatically from the programs of the courses you teach.</p>
<div class="box">
  <input id="q" class="form-control mb-3" placeholder="Search...">
  <div class="table-responsive"><table class="table align-middle" id="t">
  <thead><tr><th></th><th>Reg No</th><th>Name</th><th>Program</th><th>Year</th><th>My courses</th></tr></thead><tbody>
  <?php if(mysqli_num_rows($students)==0) echo "<tr><td colspan='6' class='text-muted text-center'>No students yet. Add your courses first.</td></tr>"; ?>
  <?php while($s=mysqli_fetch_assoc($students)): ?>
    <tr>
      <td><?php if($s['photo'] && file_exists("../uploads/students/".$s['photo'])): ?><img class="avatar" src="../uploads/students/<?= e($s['photo']) ?>"><?php else: ?><div class="ph"><?= e(strtoupper(substr($s['fullname'],0,1))) ?></div><?php endif; ?></td>
      <td><strong><?= e($s['registration_no']) ?></strong></td><td><?= e($s['fullname']) ?></td>
      <td><?= e($s['program']) ?></td><td><?= e($s['academic_year']) ?></td><td><?= e($s['my_courses']) ?></td>
    </tr>
  <?php endwhile; ?>
  </tbody></table></div>
</div>
<script>document.getElementById('q').onkeyup=function(){var f=this.value.toLowerCase();document.querySelectorAll('#t tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(f)?'':'none')}</script>
<?php panel_end();

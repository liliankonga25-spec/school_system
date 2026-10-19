<?php
include "../config/db.php"; include "../config/auth.php";
require_role('registration_officer');
$data = mysqli_query($conn,"SELECT * FROM students");
?>
<h2>Student Report</h2>
<script>window.print();</script>
<table border="1" width="100%"><tr><th>Name</th><th>Level</th><th>Program</th></tr>
<?php while($r=mysqli_fetch_assoc($data)){ ?>
<tr><td><?= e($r['fullname']) ?></td><td><?= e($r['level']) ?></td><td><?= e($r['program']) ?></td></tr>
<?php } ?></table>

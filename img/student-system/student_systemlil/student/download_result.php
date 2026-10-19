<?php
include "_init.php";
$gpa  = calculate_gpa($sid,$conn);
$rows = student_results($conn,$sid);

/* Excel/CSV download */
if(($_GET['format'] ?? '') === 'csv'){
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Results_'.preg_replace('/[^A-Za-z0-9_-]/','_',$student['registration_no']).'.csv"');
    $o = fopen('php://output','w');
    fwrite($o, "\xEF\xBB\xBF");
    fputcsv($o, ['Name',$student['fullname']]);
    fputcsv($o, ['Reg No',$student['registration_no']]);
    fputcsv($o, ['Program',$student['program']]);
    fputcsv($o, []);
    fputcsv($o, ['Course','Marks','Grade','Grade Point','Credits','Lecturer']);
    while($r=mysqli_fetch_assoc($rows)) fputcsv($o,[$r['course'],$r['marks'],grade_letter($r['marks']),grade_point($r['marks']),$r['credits'],$r['lecturer_name']]);
    fputcsv($o, ['GPA',$gpa]);
    exit();
}
/* PDF: printable page, use "Save as PDF" in the print dialog (no library needed) */
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Results - <?= e($student['registration_no']) ?></title>
<style>
body{font-family:Arial,sans-serif;max-width:800px;margin:30px auto;color:#000}
.head{text-align:center}.head img{width:80px}.head h2{margin:6px 0;color:#002666}
table{width:100%;border-collapse:collapse;margin-top:14px}
th,td{border:1px solid #444;padding:8px;text-align:center}th{background:#002666;color:#fff}
.info td{border:0;text-align:left;padding:3px}
.no-print{text-align:center;margin-bottom:16px}
@media print{.no-print{display:none}}
</style></head><body>
<div class="no-print"><button onclick="window.print()">Print / Save as PDF</button></div>
<div class="head"><img src="../images/iaa_logo.jpeg"><h2>Institute of Accountancy Arusha</h2><div>Student Results Slip</div></div>
<table class="info" style="margin-top:20px">
<tr><td><b>Name:</b> <?= e($student['fullname']) ?></td><td><b>Reg No:</b> <?= e($student['registration_no']) ?></td></tr>
<tr><td><b>Program:</b> <?= e($student['program']) ?></td><td><b>Academic Year:</b> <?= e($student['academic_year']) ?></td></tr>
</table>
<table><thead><tr><th>Course</th><th>Marks</th><th>Grade</th><th>Grade Point</th><th>Credits</th><th>Lecturer</th></tr></thead><tbody>
<?php while($r=mysqli_fetch_assoc($rows)): ?>
<tr><td><?= e($r['course']) ?></td><td><?= $r['marks'] ?></td><td><?= grade_letter($r['marks']) ?></td><td><?= number_format(grade_point($r['marks']),1) ?></td><td><?= $r['credits'] ?></td><td><?= e($r['lecturer_name']) ?></td></tr>
<?php endwhile; ?>
<tr><th colspan="3" style="text-align:right">GPA</th><th colspan="3"><?= number_format($gpa,2) ?></th></tr>
</tbody></table>
<p style="margin-top:30px;font-size:12px">Printed on <?= date('d/m/Y') ?></p>
<script>window.onload=function(){window.print()}</script>
</body></html>

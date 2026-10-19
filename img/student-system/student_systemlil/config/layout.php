<?php
/* Shared sidebar layout for lecturer / student / admin(new) pages. */
function panel_start($title, $role, $active, $who){
    $menus = [
      'lecturer' => [
        'dashboard.php'=>['speedometer2','Dashboard'],
        'courses.php'=>['journal-bookmark-fill','My Courses'],
        'my_students.php'=>['people-fill','My Students'],
        'upload_results.php'=>['upload','Upload Results'],
        'my_results.php'=>['file-text-fill','My Results'],
        'profile.php'=>['person-circle','Profile']],
      'student' => [
        'dashboard.php'=>['speedometer2','Dashboard'],
        'my_results.php'=>['file-text-fill','My Results'],
        'profile.php'=>['person-circle','Profile']],
      'registration_officer' => [
        'dashboard.php'=>['speedometer2','Dashboard'],
        'register_student.php'=>['person-plus-fill','Register Student'],
        'view_students.php'=>['people-fill','View Students'],
        'register_lecturer.php'=>['person-badge-fill','Register Lecturer'],
        'view_lecturers.php'=>['person-workspace','View Lecturers'],
        'view_courses.php'=>['journal-bookmark-fill','Courses']],
    ];
    $label = ['lecturer'=>'Lecturer Panel','student'=>'Student Panel','registration_officer'=>'Registration Panel'][$role];
    ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../css/panel.css">
</head><body>
<button class="menu-btn" onclick="document.querySelector('.sidebar').classList.toggle('open')"><i class="bi bi-list"></i></button>
<div class="sidebar">
  <div class="sidebar-header"><h3><?= $label ?></h3><p><?= e($who) ?></p></div>
  <ul class="sidebar-nav">
  <?php foreach($menus[$role] as $file=>$m): ?>
    <li><a href="<?= $file ?>" class="<?= $active===$file?'active':'' ?>"><i class="bi bi-<?= $m[0] ?>"></i><span><?= $m[1] ?></span></a></li>
  <?php endforeach; ?>
    <li class="divider"></li>
    <li class="logout"><a href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
  </ul>
</div>
<div class="content">
<?php }
function panel_end(){ ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
<?php }

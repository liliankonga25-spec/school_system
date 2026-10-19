<?php
include "_init.php";
$msg = $err = "";

if(isset($_POST['update_profile'])){
    $phone = trim($_POST['phone']); $email = trim($_POST['email']);
    if($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = "Invalid email address.";
    else {
        $photo = save_photo('photo', "../uploads/students/");
        if(!empty($_FILES['photo']['name']) && $photo==="") $err = "Photo must be JPG/PNG/GIF under 2MB.";
        else {
            if($photo !== ""){
                $q = mysqli_prepare($conn,"UPDATE students SET phone=?, email=?, photo=? WHERE id=?");
                mysqli_stmt_bind_param($q,"sssi",$phone,$email,$photo,$sid);
            } else {
                $q = mysqli_prepare($conn,"UPDATE students SET phone=?, email=? WHERE id=?");
                mysqli_stmt_bind_param($q,"ssi",$phone,$email,$sid);
            }
            mysqli_stmt_execute($q); $msg = "Profile updated.";
        }
    }
}
if(isset($_POST['change_password'])){
    $cur=$_POST['current_password']; $new=$_POST['new_password']; $cf=$_POST['confirm_password'];
    if(!password_verify($cur,$user['password'])) $err = "Current password is incorrect.";
    elseif($new !== $cf) $err = "New passwords do not match.";
    elseif(strlen($new) < 6) $err = "Password must be at least 6 characters.";
    else {
        $h = password_hash($new, PASSWORD_DEFAULT);
        $q = mysqli_prepare($conn,"UPDATE users SET password=?, plain_password=NULL WHERE id=?");
        mysqli_stmt_bind_param($q,"si",$h,$uid_user); mysqli_stmt_execute($q); $msg = "Password changed.";
    }
}
$student = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM students WHERE id=$sid"));
panel_start("My Profile","student","profile.php",$who);
?>
<h3 class="mb-4">My Profile</h3>
<?php if($msg) echo "<div class='alert alert-success'>".e($msg)."</div>"; if($err) echo "<div class='alert alert-danger'>".e($err)."</div>"; ?>
<div class="row g-3">
 <div class="col-lg-6"><div class="box">
  <div class="text-center mb-3">
   <?php if($student['photo'] && file_exists("../uploads/students/".$student['photo'])): ?><img class="avatar-lg" src="../uploads/students/<?= e($student['photo']) ?>">
   <?php else: ?><div class="ph mx-auto" style="width:120px;height:120px;font-size:2.5rem"><?= e(strtoupper(substr($who,0,1))) ?></div><?php endif; ?>
  </div>
  <form method="POST" enctype="multipart/form-data">
    <div class="row g-2 mb-2 text-muted small">
      <div class="col-6">Name<br><b class="text-dark"><?= e($student['fullname']) ?></b></div>
      <div class="col-6">Reg No<br><b class="text-dark"><?= e($student['registration_no']) ?></b></div>
      <div class="col-6">Program<br><b class="text-dark"><?= e($student['program']) ?></b></div>
      <div class="col-6">Academic year<br><b class="text-dark"><?= e($student['academic_year']) ?></b></div>
    </div>
    <hr>
    <label class="form-label">Phone</label><input class="form-control mb-2" name="phone" value="<?= e($student['phone'] ?? '') ?>">
    <label class="form-label">Email</label><input class="form-control mb-2" type="email" name="email" value="<?= e($student['email'] ?? '') ?>">
    <label class="form-label">Photo</label><input class="form-control mb-3" type="file" name="photo" accept="image/*">
    <button class="btn btn-primary" name="update_profile">Save profile</button>
  </form>
 </div></div>
 <div class="col-lg-6"><div class="box">
  <h5>Change password</h5>
  <form method="POST">
    <input class="form-control mb-2" type="password" name="current_password" placeholder="Current password" required>
    <input class="form-control mb-2" type="password" name="new_password" placeholder="New password (min 6)" required>
    <input class="form-control mb-3" type="password" name="confirm_password" placeholder="Confirm new password" required>
    <button class="btn btn-warning" name="change_password">Change password</button>
  </form>
 </div></div>
</div>
<?php panel_end();

<?php
include "_init.php";
$msg = $err = "";

if(isset($_POST['update_profile'])){
    $full = trim($_POST['full_name']); $phone = trim($_POST['phone']);
    $photo = save_photo('photo', "../uploads/lecturers/");
    if(!empty($_FILES['photo']['name']) && $photo==="") $err = "Photo must be JPG/PNG/GIF under 2MB.";
    else {
        if($photo !== ""){
            $st = mysqli_prepare($conn,"UPDATE users SET full_name=?, phone=?, profile_photo=? WHERE id=?");
            mysqli_stmt_bind_param($st,"sssi",$full,$phone,$photo,$lid);
        } else {
            $st = mysqli_prepare($conn,"UPDATE users SET full_name=?, phone=? WHERE id=?");
            mysqli_stmt_bind_param($st,"ssi",$full,$phone,$lid);
        }
        mysqli_stmt_execute($st); $msg = "Profile updated.";
    }
}
if(isset($_POST['change_password'])){
    $cur=$_POST['current_password']; $new=$_POST['new_password']; $cf=$_POST['confirm_password'];
    if(!password_verify($cur,$me['password'])) $err = "Current password is incorrect.";
    elseif($new !== $cf) $err = "New passwords do not match.";
    elseif(strlen($new) < 6) $err = "Password must be at least 6 characters.";
    else {
        $h = password_hash($new, PASSWORD_DEFAULT);
        $st = mysqli_prepare($conn,"UPDATE users SET password=? WHERE id=?");
        mysqli_stmt_bind_param($st,"si",$h,$lid); mysqli_stmt_execute($st); $msg = "Password changed.";
    }
}
$me  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$lid"));
$who = $me['full_name'] ?: $me['username'];
panel_start("Profile","lecturer","profile.php",$who);
?>
<h3 class="mb-4">My Profile</h3>
<?php if($msg) echo "<div class='alert alert-success'>".e($msg)."</div>"; if($err) echo "<div class='alert alert-danger'>".e($err)."</div>"; ?>
<div class="row g-3">
 <div class="col-lg-6"><div class="box">
  <div class="text-center mb-3">
   <?php if($me['profile_photo'] && file_exists("../uploads/lecturers/".$me['profile_photo'])): ?>
     <img class="avatar-lg" src="../uploads/lecturers/<?= e($me['profile_photo']) ?>">
   <?php else: ?><div class="ph mx-auto" style="width:120px;height:120px;font-size:2.5rem"><?= e(strtoupper(substr($who,0,1))) ?></div><?php endif; ?>
  </div>
  <form method="POST" enctype="multipart/form-data">
    <label class="form-label">Username</label><input class="form-control mb-2" value="<?= e($me['username']) ?>" disabled>
    <label class="form-label">Full name</label><input class="form-control mb-2" name="full_name" value="<?= e($me['full_name']) ?>" required>
    <label class="form-label">Phone</label><input class="form-control mb-2" name="phone" value="<?= e($me['phone']) ?>">
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

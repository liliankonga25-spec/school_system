<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* Allow only the given role; everyone else goes back to login. */
function require_role($role){
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header("Location: ../login.php");
        exit();
    }
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* Save an uploaded image safely. Returns file name or "" . */
function save_photo($field, $dir){
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== 0) return "";
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif']) || $_FILES[$field]['size'] > 2*1024*1024) return "";
    if (!@getimagesize($_FILES[$field]['tmp_name'])) return "";
    $name = time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    return move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $name) ? $name : "";
}

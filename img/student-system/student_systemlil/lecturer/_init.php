<?php
include "../config/db.php";
include "../config/auth.php";
include "../config/functions.php";
include "../config/layout.php";
require_role('lecturer');
$lid = (int)$_SESSION['user_id'];
$me  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$lid"));
$who = $me['full_name'] ?: $me['username'];

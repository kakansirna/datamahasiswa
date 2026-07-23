<?php
session_start();
$_SESSION = [];
session_destroy();
$_SESSION['flash_success'] = "Anda telah berhasil keluar dari sistem.";
header("location:login.php");
exit;
?>

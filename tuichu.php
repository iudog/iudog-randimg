<?php
session_start();
unset($_SESSION['shifou_denglu']);
session_destroy();
header('Location: denglu.php');
exit;
?>
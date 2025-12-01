<?php
require_once 'php/session_handler.php';
logoutUser();
header('Location: login.php');
exit;
?>

<?php
require_once __DIR__ . '/admin_session.php';

// Unset all session variables
$_SESSION = array();

session_destroy();

// Redirect to login page
header('Location: login.php');
exit;

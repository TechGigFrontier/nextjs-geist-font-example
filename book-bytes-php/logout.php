<?php
require_once 'includes/config.php';

// Destroy session and redirect to home
session_start();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header('Location: index.php');
exit();
?>

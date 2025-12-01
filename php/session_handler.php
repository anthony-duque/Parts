<?php
// Session handler for authentication
session_start();

// Default admin credentials (stored in memory, no database)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change this in production

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Require login - redirect if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Login user
function loginUser($username, $password) {
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        return true;
    }
    return false;
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
}

// Get logged in username
function getLoggedInUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : '';
}
?>

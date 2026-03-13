<?php
// پەیوەندی بە داتابەیسەوە
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'market_system';

$conn = mysqli_connect($host, $username, $password, $database);

if(!$conn) {
    die("پەیوەندی بە داتابەیسەوە شکستی هێنا: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

// دەستپێکردنی session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// فانکشن بۆ پشکنینی چوونەژوورەوە
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// فانکشن بۆ پشکنینی ڕۆڵ
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}

// فانکشن بۆ پشکنینی دەسەڵات
function checkPermission($allowed_roles = []) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: dashboard.php");
        exit();
    }
}
?>

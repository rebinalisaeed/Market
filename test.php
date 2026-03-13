<?php
// test.php - بۆ پشکنینی کارکردنی سیستەم
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>پشکنینی سیستەم</h1>";

// پشکنینی PHP
echo "<h2>1. وەشانی PHP:</h2>";
echo phpversion();
echo "<br><br>";

// پشکنینی config.php
echo "<h2>2. پشکنینی config.php:</h2>";
if (file_exists('config.php')) {
    echo "✅ config.php هەیە<br>";
    include 'config.php';
    
    if (isset($conn) && $conn) {
        echo "✅ پەیوەندی بە داتابەیسەوە سەرکەوتوو بوو<br>";
        
        // پشکنینی بوونی خشتەکان
        $tables = ['users', 'categories', 'products'];
        foreach ($tables as $table) {
            $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
            if (mysqli_num_rows($result) > 0) {
                echo "✅ خشتەی $table هەیە<br>";
            } else {
                echo "❌ خشتەی $table نییە - تکایە database.sql جێبەجێ بکە<br>";
            }
        }
    } else {
        echo "❌ پەیوەندی بە داتابەیسەوە شکستی هێنا: " . mysqli_connect_error();
    }
} else {
    echo "❌ config.php نەدۆزرایەوە";
}

// پشکنینی فایلەکان
echo "<h2>3. پشکنینی فایلەکان:</h2>";
$files = ['login.php', 'dashboard.php', 'sell.php', 'new_product.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file هەیە<br>";
    } else {
        echo "❌ $file نییە<br>";
    }
}

// لینکی چوونەژوورەوە
echo "<h2>4. چوونەژوورەوە:</h2>";
echo "<a href='login.php' style='font-size:20px;'>▶ چوونە ناوی سیستەم</a>";
?>

<?php
session_start();
require_once 'config.php';

// تەنها سوپەر ئەدمین دەتوانێت بەکارهێنەرانی سیستەم ببینێت
if (!hasRole('super_admin')) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// زیادکردنی بەکارهێنەری نوێ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "ئەم ناوە پێشتر تۆمار کراوە";
    } else {
        $query = "INSERT INTO users (username, password, full_name, role) 
                  VALUES ('$username', '$password', '$full_name', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = "بەکارهێنەر بە سەرکەوتوویی زیاد کرا";
        } else {
            $error = "هەڵە ڕوویدا: " . mysqli_error($conn);
        }
    }
}

// سڕینەوەی بەکارهێنەر
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != 1) { // نەتوانرێت بەکارهێنەری super admin بسڕدرێتەوە
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        $success = "بەکارهێنەر سڕدرایەوە";
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id");
?>

<!DOCTYPE html>
<html dir="rtl" lang="ckb">
<head>
    <meta charset="UTF-8">
    <title>بەڕێوەبردنی بەکارهێنەران</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { font-family: Tahoma; margin: 0; padding: 0; box-sizing: border-box; }
        .top-bar { background: #1e3c72; color: white; height: 30px; line-height: 30px; padding: 0 20px; display: flex; justify-content: space-between; position: fixed; top: 0; left: 0; right: 0; }
        .container { margin-top: 30px; padding: 20px; max-width: 1000px; margin-left: auto; margin-right: auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .add-form { background: #f5f5f5; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #1e3c72; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background: #1e3c72; color: white; }
        .delete-btn { color: #c62828; cursor: pointer; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><i class="fas fa-user"></i> <?php echo $_SESSION['full_name']; ?></div>
        <div><i class="fas fa-calendar"></i> <?php echo date('Y-m-d'); ?></div>
    </div>
    
    <div class="container">
        <h1>بەڕێوەبردنی بەکارهێنەران</h1>
        
        <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="add-form">
            <h3>زیادکردنی بەکارهێنەری نوێ</h3>
            <form method="POST">
                <div class="form-group">
                    <label>ناوی بەکارهێنەر:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>تێپەڕەوشە:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>ناوی تەواو:</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>ڕۆڵ:</label>
                    <select name="role">
                        <option value="super_admin">سوپەر ئەدمین</option>
                        <option value="admin">ئەدمین</option>
                        <option value="store">کۆگا</option>
                        <option value="cashier">فرۆشیار</option>
                    </select>
                </div>
                <button type="submit" name="add_user">زیادکردن</button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ناوی بەکارهێنەر</th>
                    <th>ناوی تەواو</th>
                    <th>ڕۆڵ</th>
                    <th>بەرواری تۆمار</th>
                    <th>دوایین چوونەژوورەوە</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td><?php echo $user['last_login'] ?? '-'; ?></td>
                    <td>
                        <?php if($user['id'] != 1): ?>
                        <a href="?delete=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('دڵنیایت لە سڕینەوە؟')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

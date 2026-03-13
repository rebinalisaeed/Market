<?php
session_start();
require_once 'config.php';

checkPermission(['super_admin', 'admin']);

$error = '';
$success = '';

// نوێکردنەوەی ڕێکخستنەکان
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_company'])) {
        $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
        $company_phone = mysqli_real_escape_string($conn, $_POST['company_phone']);
        $company_address = mysqli_real_escape_string($conn, $_POST['company_address']);
        $tax_rate = (float)$_POST['tax_rate'];
        
        // ئەمە تەنها بۆ نموونەیە، لە ڕاستیدا دەبێت لە خشتەی ڕێکخستنەکان هەڵبگیرێت
        $_SESSION['company_name'] = $company_name;
        $success = "ڕێکخستنەکان نوێکرانەوە";
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ckb">
<head>
    <meta charset="UTF-8">
    <title>ڕێکخستنەکان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { font-family: Tahoma; margin: 0; padding: 0; box-sizing: border-box; }
        .top-bar { background: #1e3c72; color: white; height: 30px; line-height: 30px; padding: 0 20px; display: flex; justify-content: space-between; position: fixed; top: 0; left: 0; right: 0; }
        .container { margin-top: 30px; padding: 20px; max-width: 800px; margin-left: auto; margin-right: auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .settings-form { background: #f5f5f5; padding: 30px; border-radius: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #1e3c72; color: white; border: none; padding: 15px 30px; border-radius: 5px; cursor: pointer; width: 100%; font-size: 18px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .back-button { display: inline-block; margin-bottom: 20px; color: #1e3c72; text-decoration: none; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><i class="fas fa-user"></i> <?php echo $_SESSION['full_name']; ?></div>
        <div><i class="fas fa-calendar"></i> <?php echo date('Y-m-d'); ?></div>
    </div>
    
    <div class="container">
        <a href="dashboard.php" class="back-button"><i class="fas fa-arrow-right"></i> گەڕانەوە</a>
        <h1>ڕێکخستنەکانی سیستەم</h1>
        
        <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="settings-form">
            <form method="POST">
                <h3 style="margin-bottom: 20px;">زانیاری کۆمپانیا</h3>
                
                <div class="form-group">
                    <label>ناوی کۆمپانیا/مارکێت:</label>
                    <input type="text" name="company_name" value="<?php echo $_SESSION['company_name'] ?? 'مارکێتەکەم'; ?>">
                </div>
                
                <div class="form-group">
                    <label>ژمارە تەلەفۆن:</label>
                    <input type="text" name="company_phone" value="٠٧٧٠٠٠٠٠٠٠">
                </div>
                
                <div class="form-group">
                    <label>ناونیشان:</label>
                    <input type="text" name="company_address" value="سلێمانی، بازاڕی گەورە">
                </div>
                
                <div class="form-group">
                    <label>ڕێژەی باج (٪):</label>
                    <input type="number" name="tax_rate" step="0.1" value="0">
                </div>
                
                <button type="submit" name="update_company">پاشەکەوتکردن</button>
            </form>
            
            <hr style="margin: 30px 0;">
            
            <h3 style="margin-bottom: 20px;">ڕێکخستنەکانی سیستەم</h3>
            
            <div style="display: grid; gap: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>زمانی سیستەم:</span>
                    <select style="padding: 8px; border-radius: 5px;">
                        <option>کوردی</option>
                        <option>عەرەبی</option>
                        <option>ئینگلیزی</option>
                    </select>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>جۆری دراو:</span>
                    <select style="padding: 8px; border-radius: 5px;">
                        <option>دینار عێراقی</option>
                        <option>دۆلار</option>
                    </select>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>چاپکردنی وەسڵ:</span>
                    <input type="checkbox" checked>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

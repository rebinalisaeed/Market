<?php
session_start();
require_once 'config.php';

// پشکنینی چوونەژوورەوە
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// تەنها ئەدمین و کۆگا دەتوانن کاڵا زیاد بکەن
if($_SESSION['role'] == 'cashier') {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// وەرگرتنی لیستی کاتێگۆریەکان
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// زیادکردنی کاڵای نوێ
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $purchase_price = (float)$_POST['purchase_price'];
    $selling_price = (float)$_POST['selling_price'];
    $quantity = (int)$_POST['quantity'];
    $min_quantity = (int)$_POST['min_quantity'];
    $expiry_date = !empty($_POST['expiry_date']) ? "'".$_POST['expiry_date']."'" : "NULL";
    
    // پشکنینی بوونی باڕکۆد
    $check = mysqli_query($conn, "SELECT id FROM products WHERE barcode='$barcode'");
    if(mysqli_num_rows($check) > 0) {
        $error = "ئەم باڕکۆدە پێشتر تۆمار کراوە";
    } 
    // پشکنینی ناوی کاڵا
    elseif(empty($name)) {
        $error = "تکایە ناوی کاڵا بنووسە";
    }
    // پشکنینی نرخ
    elseif($selling_price <= 0 || $purchase_price <= 0) {
        $error = "نرخی کاڵا دەبێت زیاتر بێت لە سفر";
    }
    else {
        // زیادکردنی کاڵا بۆ داتابەیس
        $query = "INSERT INTO products (barcode, name, category_id, purchase_price, selling_price, quantity, min_quantity, expiry_date) 
                  VALUES ('$barcode', '$name', $category_id, $purchase_price, $selling_price, $quantity, $min_quantity, $expiry_date)";
        
        if(mysqli_query($conn, $query)) {
            $success = "کاڵا بە سەرکەوتوویی تۆمار کرا";
            
            // پاککردنەوەی فۆرم (ئارەزوومەندانە)
            $_POST = array();
        } else {
            $error = "هەڵە ڕوویدا: " . mysqli_error($conn);
        }
    }
}

// زیادکردنی کاتێگۆری نوێ
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $shelf_name = mysqli_real_escape_string($conn, $_POST['shelf_name']);
    
    if(!empty($cat_name)) {
        $query = "INSERT INTO categories (name, shelf_name) VALUES ('$cat_name', '$shelf_name')";
        if(mysqli_query($conn, $query)) {
            $success = "کاتێگۆری نوێ زیاد کرا";
            // دووبارە بارکردنەوەی لیستی کاتێگۆریەکان
            $categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
        } else {
            $error = "هەڵە ڕوویدا لە زیادکردنی کاتێگۆری";
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ckb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی کاڵای نوێ - سیستەمی مارکێت</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tahoma', 'Arial', sans-serif;
        }
        
        body {
            background: #f0f2f5;
        }
        
        /* Top Bar */
        .top-bar {
            background: #1e3c72;
            color: white;
            height: 30px;
            line-height: 30px;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .user-info i, .date-info i {
            margin-left: 8px;
            margin-right: 8px;
        }
        
        /* Container */
        .container {
            margin-top: 50px;
            padding: 20px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Back Button */
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #1e3c72;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 15px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .back-button:hover {
            background: #1e3c72;
            color: white;
            transform: translateX(-5px);
        }
        
        .back-button i {
            margin-left: 5px;
        }
        
        h1 {
            color: #1e3c72;
            margin-bottom: 20px;
            text-align: center;
            font-size: 28px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        /* Messages */
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-right: 4px solid #f44336;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-right: 4px solid #4CAF50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .error i, .success i {
            font-size: 20px;
        }
        
        /* Main Form */
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-container h2 {
            color: #1e3c72;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-container h2 i {
            color: #4CAF50;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 5px;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
            font-size: 14px;
        }
        
        label i {
            margin-left: 5px;
            color: #1e3c72;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fafafa;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #1e3c72;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }
        
        input:hover, select:hover {
            border-color: #999;
        }
        
        /* Category Section */
        .category-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .category-section h3 {
            color: #1e3c72;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .category-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .category-form input {
            flex: 1;
            min-width: 200px;
        }
        
        .btn-small {
            padding: 12px 25px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-small:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a4a7a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4);
        }
        
        .btn-submit i {
            font-size: 20px;
        }
        
        /* Help Text */
        .help-text {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .help-text i {
            color: #2196F3;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .category-form {
                flex-direction: column;
            }
            
            .container {
                padding: 10px;
            }
        }
        
        /* Barcode Preview */
        .barcode-preview {
            background: #f0f2f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            text-align: center;
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
            border: 2px dashed #1e3c72;
        }
        
        /* Price Input Group */
        .price-input {
            position: relative;
        }
        
        .price-input input {
            padding-left: 50px;
        }
        
        .price-input::after {
            content: 'د.ع';
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <i class="fas fa-tag"></i>
            <span><?php echo $_SESSION['role']; ?></span>
        </div>
        <div class="date-info">
            <i class="fas fa-calendar-alt"></i>
            <span><?php echo date('Y-m-d'); ?></span>
            <i class="fas fa-clock"></i>
            <span><?php echo date('H:i:s'); ?></span>
        </div>
    </div>
    
    <!-- Main Container -->
    <div class="container">
        <!-- Back Button -->
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-right"></i> گەڕانەوە بۆ داشبۆرد
        </a>
        
        <!-- Page Title -->
        <h1><i class="fas fa-box"></i> ناساندنی کاڵای نوێ</h1>
        
        <!-- Error Message -->
        <?php if($error): ?>
        <div class="error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <!-- Success Message -->
        <?php if($success): ?>
        <div class="success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <!-- Main Form -->
        <div class="form-container">
            <h2>
                <i class="fas fa-edit"></i>
                زانیاری کاڵا
            </h2>
            
            <form method="POST" action="" id="productForm">
                <div class="form-grid">
                    <!-- Barcode -->
                    <div class="form-group">
                        <label><i class="fas fa-barcode"></i> باڕکۆد *</label>
                        <input type="text" name="barcode" id="barcode" 
                               value="<?php echo isset($_POST['barcode']) ? htmlspecialchars($_POST['barcode']) : ''; ?>"
                               placeholder="بۆ نموونە: 626101010101" required>
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i>
                            باڕکۆد دەبێت ناوازە بێت
                        </div>
                    </div>
                    
                    <!-- Product Name -->
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> ناوی کاڵا *</label>
                        <input type="text" name="name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               placeholder="بۆ نموونە: شیری مەڕ" required>
                    </div>
                    
                    <!-- Category -->
                    <div class="form-group">
                        <label><i class="fas fa-folder"></i> کاتێگۆری *</label>
                        <select name="category_id" required>
                            <option value="">-- هەڵبژاردن --</option>
                            <?php 
                            mysqli_data_seek($categories, 0); // ڕێست کردن
                            while($cat = mysqli_fetch_assoc($categories)): 
                            ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                                <?php echo $cat['shelf_name'] ? " - {$cat['shelf_name']}" : ''; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <!-- Quantity -->
                    <div class="form-group">
                        <label><i class="fas fa-cubes"></i> بڕی کاڵا *</label>
                        <input type="number" name="quantity" 
                               value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : '0'; ?>"
                               min="0" step="1" required>
                    </div>
                    
                    <!-- Purchase Price -->
                    <div class="form-group">
                        <label><i class="fas fa-shopping-basket"></i> نرخی کڕین *</label>
                        <div class="price-input">
                            <input type="number" name="purchase_price" 
                                   value="<?php echo isset($_POST['purchase_price']) ? $_POST['purchase_price'] : ''; ?>"
                                   min="1" step="0.001" required placeholder="0.000">
                        </div>
                    </div>
                    
                    <!-- Selling Price -->
                    <div class="form-group">
                        <label><i class="fas fa-cash-register"></i> نرخی فرۆشتن *</label>
                        <div class="price-input">
                            <input type="number" name="selling_price" 
                                   value="<?php echo isset($_POST['selling_price']) ? $_POST['selling_price'] : ''; ?>"
                                   min="1" step="0.001" required placeholder="0.000">
                        </div>
                    </div>
                    
                    <!-- Minimum Quantity -->
                    <div class="form-group">
                        <label><i class="fas fa-exclamation-triangle"></i> کەمترین بڕ</label>
                        <input type="number" name="min_quantity" 
                               value="<?php echo isset($_POST['min_quantity']) ? $_POST['min_quantity'] : '5'; ?>"
                               min="1" step="1">
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i>
                            ئاگادارکردنەوە کاتێک کاڵا کەم دەبێتەوە
                        </div>
                    </div>
                    
                    <!-- Expiry Date -->
                    <div class="form-group">
                        <label><i class="fas fa-calendar-times"></i> بەرواری بەسەرچوون</label>
                        <input type="date" name="expiry_date" 
                               value="<?php echo isset($_POST['expiry_date']) ? $_POST['expiry_date'] : ''; ?>"
                               min="<?php echo date('Y-m-d'); ?>">
                        <div class="help-text">
                            <i class="fas fa-info-circle"></i>
                            ئەگەر بەسەرچوونی هەیە دیاری بکە
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-sticky-note"></i> تێبینی (ئارەزوومەندانە)</label>
                        <textarea name="notes" rows="3" placeholder="تێبینی زیادە..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="add_product" class="btn-submit">
                    <i class="fas fa-save"></i>
                    تۆمارکردنی کاڵا
                </button>
            </form>
        </div>
        
        <!-- Add New Category Section -->
        <div class="form-container">
            <h2>
                <i class="fas fa-folder-plus"></i>
                زیادکردنی کاتێگۆری نوێ
            </h2>
            
            <form method="POST" action="" class="category-form">
                <input type="text" name="category_name" placeholder="ناوی کاتێگۆری" required>
                <input type="text" name="shelf_name" placeholder="ناوی ڕەفە (ئارەزوومەندانە)">
                <button type="submit" name="add_category" class="btn-small">
                    <i class="fas fa-plus"></i>
                    زیادکردن
                </button>
            </form>
            
            <!-- Categories List -->
            <div style="margin-top: 20px;">
                <h4 style="color: #1e3c72; margin-bottom: 10px;">کاتێگۆریەکان:</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php 
                    mysqli_data_seek($categories, 0);
                    while($cat = mysqli_fetch_assoc($categories)): 
                    ?>
                    <span style="background: #e0e0e0; padding: 5px 10px; border-radius: 20px; font-size: 13px;">
                        <i class="fas fa-folder"></i> <?php echo $cat['name']; ?>
                        <?php echo $cat['shelf_name'] ? "({$cat['shelf_name']})" : ''; ?>
                    </span>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        // دروستکردنی باڕکۆدی ڕێکخراو
        function generateBarcode() {
            const prefix = '626'; // پیشگرێکی نیشتمانی
            const timestamp = Date.now().toString().slice(-8);
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return prefix + timestamp + random;
        }
        
        // ئەگەر بۆکسی باڕکۆد بەتاڵ بوو، باڕکۆدێک دروست بکە
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.getElementById('barcode');
            if (!barcodeInput.value) {
                barcodeInput.value = generateBarcode();
            }
            
            // پیشاندانی پێشبینی نرخ
            const purchasePrice = document.querySelector('input[name="purchase_price"]');
            const sellingPrice = document.querySelector('input[name="selling_price"]');
            
            if (sellingPrice.value && purchasePrice.value) {
                const profit = sellingPrice.value - purchasePrice.value;
                const profitPercent = ((profit / purchasePrice.value) * 100).toFixed(1);
                console.log('قازانج: ' + profit + ' (' + profitPercent + '%)');
            }
        });
        
        // پشکنینی نرخەکان
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const purchase = parseFloat(document.querySelector('input[name="purchase_price"]').value) || 0;
            const selling = parseFloat(document.querySelector('input[name="selling_price"]').value) || 0;
            
            if (selling < purchase) {
                if (!confirm('ئاگاداربە! نرخی فرۆشتن کەمترە لە نرخی کڕین. ئایا دڵنیایت؟')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>

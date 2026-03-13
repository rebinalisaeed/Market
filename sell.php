<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// تەنها فرۆشیار و ئەدمین و سوپەر ئەدمین دەتوانن فرۆشتن بکەن
if($_SESSION['role'] == 'store') {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $quantity = (int)$_POST['quantity'];
    
    $query = "SELECT * FROM products WHERE barcode='$barcode'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
        
        if($product['quantity'] < $quantity) {
            $error = "بڕی کاڵای تەواو نییە!";
        } else {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['selling_price'],
                'quantity' => $quantity,
                'total' => $product['selling_price'] * $quantity
            ];
            $success = "کاڵا زیاد کرا";
        }
    } else {
        $error = "باڕکۆد ناسراو نییە";
    }
}

if(isset($_GET['remove_from_cart'])) {
    $index = $_GET['remove_from_cart'];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

if(isset($_POST['complete_sale'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $paid_amount = (float)$_POST['paid_amount'];
    $total_amount = (float)$_POST['total_amount'];
    $remaining = $total_amount - $paid_amount;
    
    mysqli_begin_transaction($conn);
    
    try {
        // دروستکردنی وەسڵ
        $invoice_number = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        $user_id = $_SESSION['user_id'];
        
        $query = "INSERT INTO sales (invoice_number, user_id, customer_name, total_amount, paid_amount, remaining_amount) 
                  VALUES ('$invoice_number', $user_id, '$customer_name', $total_amount, $paid_amount, $remaining)";
        mysqli_query($conn, $query);
        $sale_id = mysqli_insert_id($conn);
        
        // تۆمارکردنی کاڵاکان
        foreach($_SESSION['cart'] as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $total = $item['total'];
            
            $query = "INSERT INTO sale_items (sale_id, product_id, quantity, price, total) 
                      VALUES ($sale_id, $product_id, $quantity, $price, $total)";
            mysqli_query($conn, $query);
            
            // کەمکردنەوەی بڕی کاڵا لە کۆگا
            mysqli_query($conn, "UPDATE products SET quantity = quantity - $quantity WHERE id = $product_id");
        }
        
        mysqli_commit($conn);
        
        // پاککردنەوەی سەبەتە
        unset($_SESSION['cart']);
        
        header("Location: invoice.php?id=$sale_id");
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "هەڵەیەک ڕوویدا: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ckb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فرۆشتن - سیستەمی مارکێت</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tahoma', 'Arial', sans-serif;
        }
        
        body {
            background: white;
        }
        
        .top-bar {
            background: #1e3c72;
            color: white;
            height: 30px;
            line-height: 30px;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .container {
            margin-top: 30px;
            padding: 20px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .sell-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .add-product {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
        }
        
        .add-product h2 {
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        button {
            background: #1e3c72;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button:hover {
            background: #2a4a7a;
        }
        
        .cart {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
        }
        
        .cart h2 {
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .cart-items {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            align-items: center;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item .remove {
            color: #f44336;
            cursor: pointer;
            text-align: center;
        }
        
        .cart-total {
            margin-top: 20px;
            text-align: left;
            font-size: 20px;
            font-weight: bold;
            color: #1e3c72;
        }
        
        .complete-sale {
            margin-top: 20px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 10px;
        }
        
        .complete-sale h3 {
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            color: #1e3c72;
            text-decoration: none;
            font-size: 16px;
        }
        
        .back-button i {
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="user-info">
            <i class="fas fa-user"></i>
            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
        <div>
            <i class="fas fa-calendar"></i>
            <?php echo date('Y-m-d'); ?>
        </div>
    </div>
    
    <div class="container">
        <a href="dashboard.php" class="back-button"><i class="fas fa-arrow-right"></i> گەڕانەوە بۆ داشبۆرد</a>
        <h1>کاڵا فرۆشتن</h1>
        
        <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="sell-section">
            <div class="add-product">
                <h2>زیادکردنی کاڵا</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>باڕکۆد:</label>
                        <input type="text" name="barcode" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label>ژمارە:</label>
                        <input type="number" name="quantity" value="1" min="1" required>
                    </div>
                    
                    <button type="submit" name="add_to_cart">زیادکردن</button>
                </form>
            </div>
            
            <div class="cart">
                <h2>کاڵاکانی فرۆشتن</h2>
                
                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <div class="cart-items">
                    <?php 
                    $total = 0;
                    foreach($_SESSION['cart'] as $index => $item): 
                        $total += $item['total'];
                    ?>
                    <div class="cart-item">
                        <span><?php echo $item['name']; ?></span>
                        <span><?php echo $item['price']; ?> د.ع</span>
                        <span><?php echo $item['quantity']; ?></span>
                        <span><?php echo $item['total']; ?> د.ع</span>
                        <span class="remove" onclick="window.location.href='?remove_from_cart=<?php echo $index; ?>'">
                            <i class="fas fa-trash"></i>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-total">
                    کۆی گشتی: <?php echo number_format($total); ?> د.ع
                </div>
                
                <div class="complete-sale">
                    <h3>تەواوکردنی فرۆشتن</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>ناوی کڕیار (ئارەزوومەندانە):</label>
                            <input type="text" name="customer_name">
                        </div>
                        
                        <div class="form-group">
                            <label>بڕی پارەی وەرگیراو:</label>
                            <input type="number" name="paid_amount" step="0.001" value="<?php echo $total; ?>" required>
                        </div>
                        
                        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                        
                        <button type="submit" name="complete_sale">تەواوکردن</button>
                    </form>
                </div>
                
                <?php else: ?>
                <p>هیچ کاڵایەک نییە لە سەبەتەدا</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

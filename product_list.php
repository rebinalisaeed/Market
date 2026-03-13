<?php
session_start();
require_once 'config.php';

checkPermission(['super_admin', 'admin', 'store']);

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$where = [];
if ($search) {
    $where[] = "(name LIKE '%$search%' OR barcode LIKE '%$search%')";
}
if ($category > 0) {
    $where[] = "category_id = $category";
}

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";
        
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY p.name ASC";

$products = mysqli_query($conn, $sql);
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html dir="rtl" lang="ckb">
<head>
    <meta charset="UTF-8">
    <title>لیستی کاڵاکان</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ستایلەکان */
        * { font-family: Tahoma; margin: 0; padding: 0; box-sizing: border-box; }
        .top-bar { background: #1e3c72; color: white; height: 30px; line-height: 30px; padding: 0 20px; display: flex; justify-content: space-between; position: fixed; top: 0; left: 0; right: 0; }
        .container { margin-top: 30px; padding: 20px; max-width: 1200px; margin-left: auto; margin-right: auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .search-box { background: #f5f5f5; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .search-form { display: flex; gap: 10px; flex-wrap: wrap; }
        .search-form input, .search-form select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1; }
        .search-form button { padding: 10px 20px; background: #1e3c72; color: white; border: none; border-radius: 5px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background: #1e3c72; color: white; }
        tr:hover { background: #f5f5f5; }
        .low-stock { background: #ffebee; color: #c62828; font-weight: bold; }
        .expired { background: #ffebee; color: #c62828; }
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
        <h1>لیستی کاڵاکان</h1>
        
        <div class="search-box">
            <form class="search-form" method="GET">
                <input type="text" name="search" placeholder="گەڕان بە ناو یان باڕکۆد..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category">
                    <option value="0">هەموو کاتێگۆریەکان</option>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit"><i class="fas fa-search"></i> گەڕان</button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>باڕکۆد</th>
                    <th>ناو</th>
                    <th>کاتێگۆری</th>
                    <th>نرخی کڕین</th>
                    <th>نرخی فرۆشتن</th>
                    <th>بڕ</th>
                    <th>بەرواری بەسەرچوون</th>
                    <th>باری</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = mysqli_fetch_assoc($products)): 
                    $row_class = '';
                    if ($product['quantity'] <= $product['min_quantity']) {
                        $row_class = 'low-stock';
                    }
                    if ($product['expiry_date'] && $product['expiry_date'] < date('Y-m-d')) {
                        $row_class = 'expired';
                    }
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $product['barcode']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['category_name']; ?></td>
                    <td><?php echo number_format($product['purchase_price']); ?></td>
                    <td><?php echo number_format($product['selling_price']); ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['expiry_date'] ? date('Y-m-d', strtotime($product['expiry_date'])) : '-'; ?></td>
                    <td>
                        <?php if($product['quantity'] <= $product['min_quantity']): ?>
                            <span style="color: #c62828;">کەمە</span>
                        <?php elseif($product['expiry_date'] && $product['expiry_date'] < date('Y-m-d')): ?>
                            <span style="color: #c62828;">بەسەرچووە</span>
                        <?php else: ?>
                            <span style="color: #2e7d32;">باشە</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

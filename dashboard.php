<?php
session_start();
require_once 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$full_name = $_SESSION['full_name'];

// وەرگرتنی ڕێکەوت و کات
$current_date = date('Y-m-d');
$current_time = date('H:i:s');
$day_of_week = array('یەکشەممە', 'دووشەممە', 'سێشەممە', 'چوارشەممە', 'پێنجشەممە', 'هەینی', 'شەممە')[date('w')];
?>

<!DOCTYPE html>
<html dir="rtl" lang="ckb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پەڕەی سەرەکی - سیستەمی مارکێت</title>
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
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info i {
            margin-left: 8px;
        }
        
        .date-info {
            display: flex;
            align-items: center;
        }
        
        .date-info i {
            margin-right: 8px;
        }
        
        .date-info span {
            margin-left: 15px;
        }
        
        /* Dashboard Container */
        .dashboard {
            margin-top: 30px;
            padding: 20px;
            background: white;
        }
        
        .dashboard-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
        }
        
        /* Grid Layout */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Box Styles */
        .box {
            border-radius: 10px;
            padding: 20px;
            color: white;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100px;
        }
        
        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .box-icon {
            font-size: 40px;
            width: 40%;
            text-align: center;
        }
        
        .box-title {
            width: 60%;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            padding-right: 10px;
        }
        
        /* Blue boxes (1-7) */
        .box-blue-1 { background: #2196F3; }
        .box-blue-2 { background: #1976D2; }
        .box-blue-3 { background: #0D47A1; }
        .box-blue-4 { background: #42A5F5; }
        .box-blue-5 { background: #1E88E5; }
        .box-blue-6 { background: #1565C0; }
        .box-blue-7 { background: #0D3B66; }
        
        /* Green boxes (8-12) */
        .box-green { background: #4CAF50; }
        
        /* Orange boxes (13-17) */
        .box-orange { background: #FF9800; }
        
        /* Red boxes (18-27) */
        .box-red { background: #f44336; }
        
        /* Brown boxes (28-32) */
        .box-brown { background: #8B4513; }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .grid-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 900px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="user-info">
            <i class="fas fa-user"></i>
            <span><?php echo htmlspecialchars($full_name); ?> (<?php echo $role; ?>)</span>
        </div>
        <div class="date-info">
            <i class="fas fa-calendar-alt"></i>
            <span><?php echo $current_date; ?></span>
            <i class="fas fa-clock"></i>
            <span><?php echo $current_time; ?></span>
            <i class="fas fa-calendar-day"></i>
            <span><?php echo $day_of_week; ?></span>
        </div>
    </div>
    
    <div class="dashboard">
        <h2 class="dashboard-title">داشبۆردی سەرەکی</h2>
        
        <div class="grid-container">
            <!-- Box 1-7: شین -->
            <div class="box box-blue-1" onclick="window.location.href='sell.php'">
                <div class="box-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="box-title">کاڵا فرۆشتن</div>
            </div>
            
            <div class="box box-blue-2" onclick="window.location.href='invoices.php'">
                <div class="box-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="box-title">وەسڵی فرۆشراوەکان</div>
            </div>
            
            <div class="box box-blue-3" onclick="window.location.href='cash_income.php'">
                <div class="box-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="box-title">پارە وەرگرتن لە کاشێر</div>
            </div>
            
            <div class="box box-blue-4" onclick="window.location.href='cash_expense.php'">
                <div class="box-icon"><i class="fas fa-money-bill-alt"></i></div>
                <div class="box-title">پارە خەرجکردن</div>
            </div>
            
            <div class="box box-blue-5" onclick="window.location.href='damage.php'">
                <div class="box-icon"><i class="fas fa-trash-alt"></i></div>
                <div class="box-title">تۆمارکردنی بەهەدەردراوەکان</div>
            </div>
            
            <div class="box box-blue-6" onclick="window.location.href='shelf_price.php'">
                <div class="box-icon"><i class="fas fa-tags"></i></div>
                <div class="box-title">دروستکردنی نرخ و ناو بۆسەر ڕەفە</div>
            </div>
            
            <div class="box box-blue-7" onclick="window.location.href='barcode.php'">
                <div class="box-icon"><i class="fas fa-barcode"></i></div>
                <div class="box-title">دروستکردنی باڕکۆد</div>
            </div>
            
            <!-- Box 8-12: سەوز -->
            <div class="box box-green" onclick="window.location.href='new_product.php'">
                <div class="box-icon"><i class="fas fa-box"></i></div>
                <div class="box-title">ناساندنی کاڵای نوێ</div>
            </div>
            
            <div class="box box-green" onclick="window.location.href='product_list.php'">
                <div class="box-icon"><i class="fas fa-list"></i></div>
                <div class="box-title">لیستی کاڵاکان</div>
            </div>
            
            <div class="box box-green" onclick="window.location.href='add_invoice.php'">
                <div class="box-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="box-title">تۆمارکردنی وەسڵی کردراو</div>
            </div>
            
            <div class="box box-green" onclick="window.location.href='invoices_list.php'">
                <div class="box-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="box-title">وەسڵە کردراوەکان</div>
            </div>
            
            <div class="box box-green" onclick="window.location.href='company_debts.php'">
                <div class="box-icon"><i class="fas fa-building"></i></div>
                <div class="box-title">قەرزی کۆمپانیاکان</div>
            </div>
            
            <!-- Box 13-17: پرتەقاڵی -->
            <div class="box box-orange" onclick="window.location.href='debtors.php'">
                <div class="box-icon"><i class="fas fa-users"></i></div>
                <div class="box-title">لیستی قەرزارەکان</div>
            </div>
            
            <div class="box box-orange" onclick="window.location.href='receive_debt.php'">
                <div class="box-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="box-title">وەرگرتنەوەی قەرز</div>
            </div>
            
            <div class="box box-orange" onclick="window.location.href='installment_alert.php'">
                <div class="box-icon"><i class="fas fa-bell"></i></div>
                <div class="box-title">ئاگادارکردنەوەی قیست</div>
            </div>
            
            <div class="box box-orange" onclick="window.location.href='installment_dashboard.php'">
                <div class="box-icon"><i class="fas fa-chart-line"></i></div>
                <div class="box-title">داشبۆردی قیست</div>
            </div>
            
            <div class="box box-orange" onclick="window.location.href='cash.php'">
                <div class="box-icon"><i class="fas fa-coins"></i></div>
                <div class="box-title">قاسە</div>
            </div>
            
            <!-- Box 18-27: سور -->
            <div class="box box-red" onclick="window.location.href='profits.php'">
                <div class="box-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="box-title">قازانجەکان</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='expired.php'">
                <div class="box-icon"><i class="fas fa-calendar-times"></i></div>
                <div class="box-title">بەسەرچووەکان</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='low_stock.php'">
                <div class="box-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="box-title">کاڵا کەمبووەکان</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='sold_inventory.php'">
                <div class="box-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="box-title">جەردی فرۆشراوەکان</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='cash_report.php'">
                <div class="box-icon"><i class="fas fa-calculator"></i></div>
                <div class="box-title">کەشف حساب پارە</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='customer_account.php'">
                <div class="box-icon"><i class="fas fa-user-check"></i></div>
                <div class="box-title">کەشف حسابی کڕیار</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='purchase_account.php'">
                <div class="box-icon"><i class="fas fa-shopping-bag"></i></div>
                <div class="box-title">کەشف حسابی کڕین</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='money_report.php'">
                <div class="box-icon"><i class="fas fa-file-alt"></i></div>
                <div class="box-title">ڕاپۆرتی پارە</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='inventory.php'">
                <div class="box-icon"><i class="fas fa-warehouse"></i></div>
                <div class="box-title">جەردکردن</div>
            </div>
            
            <div class="box box-red" onclick="window.location.href='users.php'">
                <div class="box-icon"><i class="fas fa-users-cog"></i></div>
                <div class="box-title">بەکارهێنەری سیستەم</div>
            </div>
            
            <!-- Box 28-32: قاوەیی -->
            <div class="box box-brown" onclick="window.location.href='backup.php'">
                <div class="box-icon"><i class="fas fa-database"></i></div>
                <div class="box-title">پاشەکەوتی داتاکان</div>
            </div>
            
            <div class="box box-brown" onclick="window.location.href='daily_log.php'">
                <div class="box-icon"><i class="fas fa-book"></i></div>
                <div class="box-title">دەفتەری ڕۆژانە</div>
            </div>
            
            <div class="box box-brown" onclick="window.location.href='requirements.php'">
                <div class="box-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="box-title">پێداویستی</div>
            </div>
            
            <div class="box box-brown" onclick="window.location.href='settings.php'">
                <div class="box-icon"><i class="fas fa-cog"></i></div>
                <div class="box-title">ڕێکخستن</div>
            </div>
            
            <div class="box box-brown" onclick="window.location.href='logout.php'">
                <div class="box-icon"><i class="fas fa-sign-out-alt"></i></div>
                <div class="box-title">دەرچوون</div>
            </div>
        </div>
    </div>
    
    <script>
        // نوێکردنەوەی کات لە بانەڕەکەدا
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('ckb');
            document.querySelector('.date-info span:nth-child(4)').textContent = timeString;
        }
        setInterval(updateTime, 1000);
    </script>
</body>
</html>

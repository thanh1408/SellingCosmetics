<?php
session_start();

// Kết nối cơ sở dữ liệu
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$database = 'db_mypham';

$conn = new mysqli($host, $username_db, $password_db, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra user_id và quyền admin
$username = '';
$isAdmin = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $username = $user['username'];
        $isAdmin = stripos($username, 'admin') !== false;
    }
    $stmt->close();
}

// Nếu không phải admin, hiển thị thông báo lỗi
if (!$isAdmin) {
?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Không có quyền truy cập</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 0;
                background: linear-gradient(135deg, #F8EDEB, #FBE8A6);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .error-container {
                background: #fff;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                width: 400px;
                text-align: center;
            }
            .error-container h2 {
                color: #D81B60;
                margin-bottom: 20px;
            }
            .error-container p {
                color: #333;
                margin-bottom: 20px;
            }
            .error-container a {
                background: linear-gradient(135deg, #D81B60, #F48FB1);
                color: #FAF0E6;
                padding: 10px 20px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            .error-container a:hover {
                background: linear-gradient(135deg, #AD1457, #EC407A);
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h2>Không có quyền truy cập</h2>
            <p>Bạn cần tài khoản admin để truy cập trang này.</p>
            <a href="home.php">Quay lại trang chủ</a>
        </div>
    </body>
    </html>
<?php
    $conn->close();
    exit();
}

// Xác định trang hiện tại từ tham số URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Lấy từ khóa tìm kiếm nếu có
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Lấy dữ liệu thống kê
$totalOrders = 0;
$itemsSold = 0;
$itemsDelivering = 0;
$itemsCommented = 0;

$resultOrders = $conn->query("SELECT COUNT(*) as total FROM orders");
if ($resultOrders) $totalOrders = $resultOrders->fetch_assoc()['total'];

$resultProducts = $conn->query("SELECT SUM(quantity) as sold FROM orders");
if ($resultProducts) $itemsSold = $resultProducts->fetch_assoc()['sold'] ?? 0;

$resultDelivering = $conn->query("SELECT COUNT(*) as delivering FROM orders WHERE status = 'Chờ xử lý'");
if ($resultDelivering) $itemsDelivering = $resultDelivering->fetch_assoc()['delivering'];

$resultReviews = $conn->query("SELECT COUNT(*) as reviewed FROM product_reviews");
if ($resultReviews) $itemsCommented = $resultReviews->fetch_assoc()['reviewed'];

// Lấy dữ liệu sản phẩm
$products = [];
if ($page == 'product' && $search) {
    $stmt = $conn->prepare("SELECT id, name, category, price FROM product WHERE name LIKE ? OR category LIKE ?");
    $searchParam = "%$search%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $resultProducts = $stmt->get_result();
    while ($row = $resultProducts->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
} else {
    $resultProducts = $conn->query("SELECT id, name, category, price FROM product");
    if ($resultProducts) {
        while ($row = $resultProducts->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

// Lấy dữ liệu đơn hàng
$orders = [];
if ($page == 'order' && $search) {
    $stmt = $conn->prepare("
        SELECT o.id as stt, o.user_id, o.product_name, o.quantity, o.price, o.address, o.status, o.created_at
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE u.username LIKE ? OR o.status LIKE ? OR o.created_at LIKE ?
    ");
    $searchParam = "%$search%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $resultOrders = $stmt->get_result();
    while ($row = $resultOrders->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
} else {
    $resultOrders = $conn->query("SELECT id as stt, user_id, product_name, quantity, price, address, status, created_at FROM orders");
    if ($resultOrders) {
        while ($row = $resultOrders->fetch_assoc()) {
            $orders[] = $row;
        }
    }
}

// Lấy dữ liệu slider
$sliders = [];
if ($page == 'slider' && $search) {
    $stmt = $conn->prepare("SELECT id, name, image, link, `order` FROM sliders WHERE name LIKE ? OR link LIKE ?");
    $searchParam = "%$search%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $resultSliders = $stmt->get_result();
    while ($row = $resultSliders->fetch_assoc()) {
        $sliders[] = $row;
    }
    $stmt->close();
} else {
    $resultSliders = $conn->query("SELECT id, name, image, link, `order` FROM sliders");
    if ($resultSliders) {
        while ($row = $resultSliders->fetch_assoc()) {
            $sliders[] = $row;
        }
    }
}

// Lấy dữ liệu khách hàng
$customers = [];
if ($page == 'customer' && $search) {
    $stmt = $conn->prepare("SELECT id, username as name, phone, email FROM users WHERE (username LIKE ? OR email LIKE ? OR phone LIKE ?) AND username NOT LIKE '%admin%'");
    $searchParam = "%$search%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $resultCustomers = $stmt->get_result();
    while ($row = $resultCustomers->fetch_assoc()) {
        $customers[] = $row;
    }
    $stmt->close();
} else {
    $resultCustomers = $conn->query("SELECT id, username as name, phone, email FROM users WHERE username NOT LIKE '%admin%'");
    if ($resultCustomers) {
        while ($row = $resultCustomers->fetch_assoc()) {
            $customers[] = $row;
        }
    }
}

// Lấy dữ liệu danh mục
$categories = [];
if ($page == 'category' && $search) {
    $stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE name LIKE ?");
    $searchParam = "%$search%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $resultCategories = $stmt->get_result();
    while ($row = $resultCategories->fetch_assoc()) {
        $categories[] = $row;
    }
    $stmt->close();
} else {
    $resultCategories = $conn->query("SELECT id, name, description FROM categories");
    if ($resultCategories) {
        while ($row = $resultCategories->fetch_assoc()) {
            $categories[] = $row;
        }
    }
}

// Lấy dữ liệu voucher
$vouchers = [];
if ($page == 'voucher' && $search) {
    $stmt = $conn->prepare("SELECT id, code, discount, discount_type, min_order_value, expires_at, is_active FROM vouchers WHERE code LIKE ?");
    $searchParam = "%$search%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $resultVouchers = $stmt->get_result();
    while ($row = $resultVouchers->fetch_assoc()) {
        $vouchers[] = $row;
    }
    $stmt->close();
} else {
    $resultVouchers = $conn->query("SELECT id, code, discount, discount_type, min_order_value, expires_at, is_active FROM vouchers");
    if ($resultVouchers) {
        while ($row = $resultVouchers->fetch_assoc()) {
            $vouchers[] = $row;
        }
    }
}

// Xử lý CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=category");
        exit();
    }
    if (isset($_POST['edit_category'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=category");
        exit();
    }
    if (isset($_POST['delete_category']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=category");
        exit();
    }
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $stmt = $conn->prepare("INSERT INTO product (name, category, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $category, $price);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=product");
        exit();
    }
    if (isset($_POST['edit_product'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $stmt = $conn->prepare("UPDATE product SET name = ?, category = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $name, $category, $price, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=product");
        exit();
    }
    if (isset($_POST['delete_product']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=product");
        exit();
    }
    if (isset($_POST['add_customer'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, phone, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $password);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=customer");
        exit();
    }
    if (isset($_POST['edit_customer'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("UPDATE users SET username = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $email, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=customer");
        exit();
    }
    if (isset($_POST['delete_customer']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=customer");
        exit();
    }
    if (isset($_POST['add_slider'])) {
        $name = $_POST['name'];
        $image = $_POST['image'];
        $link = $_POST['link'];
        $order = $_POST['order'];
        $stmt = $conn->prepare("INSERT INTO sliders (name, image, link, `order`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $image, $link, $order);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=slider");
        exit();
    }
    if (isset($_POST['edit_slider'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $image = $_POST['image'];
        $link = $_POST['link'];
        $order = $_POST['order'];
        $stmt = $conn->prepare("UPDATE sliders SET name = ?, image = ?, link = ?, `order` = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $image, $link, $order, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=slider");
        exit();
    }
    if (isset($_POST['delete_slider']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM sliders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=slider");
        exit();
    }
    if (isset($_POST['edit_order'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=order");
        exit();
    }
    if (isset($_POST['delete_order']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=order");
        exit();
    }
    // Xử lý CRUD cho voucher
    if (isset($_POST['add_voucher'])) {
        $code = $_POST['code'];
        $discount = $_POST['discount'];
        $discount_type = $_POST['discount_type'];
        $min_order_value = $_POST['min_order_value'];
        $expires_at = $_POST['expires_at'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO vouchers (code, discount, discount_type, min_order_value, expires_at, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsdsi", $code, $discount, $discount_type, $min_order_value, $expires_at, $is_active);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=voucher");
        exit();
    }
    if (isset($_POST['edit_voucher'])) {
        $id = $_POST['id'];
        $code = $_POST['code'];
        $discount = $_POST['discount'];
        $discount_type = $_POST['discount_type'];
        $min_order_value = $_POST['min_order_value'];
        $expires_at = $_POST['expires_at'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE vouchers SET code = ?, discount = ?, discount_type = ?, min_order_value = ?, expires_at = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("sdsdsii", $code, $discount, $discount_type, $min_order_value, $expires_at, $is_active, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=voucher");
        exit();
    }
    if (isset($_POST['delete_voucher']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM vouchers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: ?page=voucher");
        exit();
    }
}

// Đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luna Admin - Quản lý mỹ phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F5F6FA;
            display: flex;
            color: #333;
        }
        .sidebar {
            width: 250px;
            background: #FFFFFF;
            height: 100vh;
            padding: 20px;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #E5E7EB;
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .sidebar h2 {
            margin: 0;
            color: #EC4899;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .toggle-btn {
            font-size: 20px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #6B7280;
            transition: all 0.3s ease;
        }
        .toggle-btn i {
            vertical-align: middle;
            margin-bottom: 28px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #4B5563;
            text-decoration: none;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 15px;
            font-weight: 500;
        }
        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
            color: #6B7280;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: #EC4899;
            color: #FFFFFF;
            transform: translateX(3px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .sidebar a:hover i,
        .sidebar a.active i {
            color: #FFFFFF;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
            background-color: #F5F6FA;
        }
        .header {
            background: #FFFFFF;
            color: #EC4899;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 22px;
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .header div {
            display: flex;
            align-items: center;
        }
        .header span {
            margin-right: 15px;
            font-weight: 500;
            font-size: 14px;
            color: #6B7280;
        }
        .header a {
            color: #EC4899;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .header a:hover {
            background: #EC4899;
            color: #FFFFFF;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-box {
            background: #FFFFFF;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .stat-box h3 {
            margin: 0;
            font-size: 28px;
            color: #EC4899;
            font-weight: 600;
        }
        .stat-box p {
            margin: 5px 0 0;
            color: #6B7280;
            font-size: 13px;
            font-weight: 500;
        }
        .table-container {
            background: #FFFFFF;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
        }
        th {
            background: #EC4899;
            color: #FFFFFF;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        tr:hover {
            background-color: #F1F5F9;
            transition: background-color 0.3s ease;
        }
        .add-btn,
        .edit-btn,
        .delete-btn,
        .submit-btn,
        .search-btn {
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }
        .add-btn,
        .edit-btn,
        .submit-btn,
        .search-btn {
            background: #EC4899;
            color: #FFFFFF;
        }
        .add-btn:hover,
        .edit-btn:hover,
        .submit-btn:hover,
        .search-btn:hover {
            background: #DB2777;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .delete-btn {
            background: #EF4444;
            color: #FFFFFF;
        }
        .delete-btn:hover {
            background: #DC2626;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .form-container {
            background: #FFFFFF;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
            display: none;
        }
        .form-container.active {
            display: block;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            color: #4B5563;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 13px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #EC4899;
            outline: none;
            box-shadow: 0 0 6px rgba(236, 72, 153, 0.2);
        }
        .search-container {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .search-container .inner-search {
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 350px;
            position: relative;
        }
        .search-container input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .search-container input:focus {
            border-color: #EC4899;
            box-shadow: 0 0 6px rgba(236, 72, 153, 0.2);
            outline: none;
        }
        .search-container .fa-magnifying-glass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #EC4899;
            font-size: 16px;
        }
        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .actions form {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Luna Admin</h2>
            <button id="toggle-sidebar" class="toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <a href="?page=home" class="<?php echo $page == 'home' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Trang chủ</a>
        <a href="?page=category" class="<?php echo $page == 'category' ? 'active' : ''; ?>"><i class="fas fa-list"></i> Danh mục</a>
        <a href="?page=product" class="<?php echo $page == 'product' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Sản phẩm</a>
        <a href="?page=order" class="<?php echo $page == 'order' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
        <a href="?page=slider" class="<?php echo $page == 'slider' ? 'active' : ''; ?>"><i class="fas fa-images"></i> Slider</a>
        <a href="?page=customer" class="<?php echo $page == 'customer' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Khách hàng</a>
        <a href="?page=voucher" class="<?php echo $page == 'voucher' ? 'active' : ''; ?>"><i class="fas fa-ticket-alt"></i> Voucher</a>
        <a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </div>
    <div class="content">
        <div class="header">
            <h1><?php
                if ($page == 'home') echo 'TRANG CHỦ';
                elseif ($page == 'category') echo 'QUẢN LÝ DANH MỤC';
                elseif ($page == 'product') echo 'QUẢN LÝ SẢN PHẨM';
                elseif ($page == 'order') echo 'QUẢN LÝ ĐƠN HÀNG';
                elseif ($page == 'slider') echo 'QUẢN LÝ SLIDER';
                elseif ($page == 'customer') echo 'QUẢN LÝ KHÁCH HÀNG';
                elseif ($page == 'voucher') echo 'QUẢN LÝ VOUCHER';
                ?></h1>
            <div>
                <span>Xin chào <?php echo htmlspecialchars($username); ?></span>
            </div>
        </div>
        <?php if ($page == 'home'): ?>
            <div class="stats">
                <div class="stat-box">
                    <h3><?php echo $totalOrders; ?></h3>
                    <p>Tổng đơn hàng</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $itemsSold; ?></h3>
                    <p>Số lượng sản phẩm đã bán</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $itemsCommented; ?></h3>
                    <p>Sản phẩm được bình luận</p>
                </div>
            </div>
        <?php elseif ($page == 'category'): ?>
            <div class="search-container">
                <button class="add-btn" onclick="document.getElementById('add-category-form').classList.add('active')"><i class="fas fa-plus"></i> Thêm danh mục</button>
                <div class="inner-search">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="category">
                        <input type="text" name="search" placeholder="Tìm kiếm danh mục..." value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-magnifying-glass"></i>
                    </form>
                    <button type="submit" form="category-search-form" class="search-btn"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
            <div class="table-container">
                <table class="category-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description']); ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo addslashes($category['name']); ?>', '<?php echo addslashes($category['description']); ?>')"><i class="fas fa-edit"></i> Sửa</button>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" name="delete_category" class="delete-btn"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="add-category-form" class="form-container">
                <h3>Thêm danh mục</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Tên danh mục</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <button type="submit" name="add_category" class="submit-btn"><i class="fas fa-save"></i> Thêm</button>
                    <button onclick="document.getElementById('add-category-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
            <div id="edit-category-form" class="form-container">
                <h3>Sửa danh mục</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-name">Tên danh mục</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Mô tả</label>
                        <textarea id="edit-description" name="description" required></textarea>
                    </div>
                    <button type="submit" name="edit_category" class="submit-btn"><i class="fas fa-save"></i> Lưu</button>
                    <button onclick="document.getElementById('edit-category-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        <?php elseif ($page == 'product'): ?>
            <div class="search-container">
                <button class="add-btn" onclick="document.getElementById('add-product-form').classList.add('active')"><i class="fas fa-plus"></i> Thêm sản phẩm</button>
                <div class="inner-search">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="product">
                        <input type="text" name="search" placeholder="Tìm kiếm sản phẩm hoặc danh mục..." value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-magnifying-glass"></i>
                    </form>
                    <button type="submit" form="product-search-form" class="search-btn"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
            <div class="table-container">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo addslashes($product['category']); ?>', <?php echo $product['price']; ?>)"><i class="fas fa-edit"></i> Sửa</button>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="delete_product" class="delete-btn"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="add-product-form" class="form-container">
                <h3>Thêm sản phẩm</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Tên sản phẩm</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Danh mục</label>
                        <input type="text" id="category" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Giá</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Số lượng</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="product_image">Ảnh</label>
                        <input type="text" id="product_image" name="product_image" required>
                    </div>
                    <button type="submit" name="add_product" class="submit-btn"><i class="fas fa-save"></i> Thêm</button>
                    <button onclick="document.getElementById('add-product-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
            <div id="edit-product-form" class="form-container">
                <h3>Sửa sản phẩm</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-name">Tên sản phẩm</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-category">Danh mục</label>
                        <input type="text" id="edit-category" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-price">Giá</label>
                        <input type="number" id="edit-price" name="price" step="0.01" required>
                    </div>
                    <button type="submit" name="edit_product" class="submit-btn"><i class="fas fa-save"></i> Lưu</button>
                    <button onclick="document.getElementById('edit-product-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        <?php elseif ($page == 'order'): ?>
            <div class="search-container">
                <div class="inner-search">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="order">
                        <input type="text" name="search" placeholder="Tìm kiếm khách hàng, trạng thái, ngày đặt..." value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-magnifying-glass"></i>
                    </form>
                    <button type="submit" form="order-search-form" class="search-btn"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
            <div class="table-container">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Số ĐT</th>
                            <th>Giá trị đơn hàng</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultUsers = $conn->query("SELECT id, username, phone FROM users");
                        $users = [];
                        if ($resultUsers) {
                            while ($row = $resultUsers->fetch_assoc()) {
                                $users[$row['id']] = $row;
                            }
                        }
                        foreach ($orders as $order):
                            $customerName = isset($users[$order['user_id']]) ? $users[$order['user_id']]['username'] : 'Không xác định';
                            $customerPhone = isset($users[$order['user_id']]) ? $users[$order['user_id']]['phone'] : '';
                        ?>
                            <tr>
                                <td><?php echo $order['stt']; ?></td>
                                <td><?php echo htmlspecialchars($customerName); ?></td>
                                <td><?php echo $order['created_at']; ?></td>
                                <td><?php echo htmlspecialchars($customerPhone); ?></td>
                                <td><?php echo number_format($order['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="editOrder(<?php echo $order['stt']; ?>, '<?php echo addslashes($order['status']); ?>')"><i class="fas fa-edit"></i> Sửa</button>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                        <input type="hidden" name="id" value="<?php echo $order['stt']; ?>">
                                        <button type="submit" name="delete_order" class="delete-btn"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="edit-order-form" class="form-container">
                <h3>Sửa đơn hàng</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-status">Trạng thái</label>
                        <select id="edit-status" name="status" required>
                            <option value="Chờ xử lý">Chờ xử lý</option>
                            <option value="Đã xác nhận">Đã xác nhận</option>
                            <option value="Đang giao">Đang giao</option>
                            <option value="Đã giao">Đã giao</option>
                            <option value="Đã hủy">Đã hủy</option>
                        </select>
                    </div>
                    <button type="submit" name="edit_order" class="submit-btn"><i class="fas fa-save"></i> Lưu</button>
                    <button onclick="document.getElementById('edit-order-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        <?php elseif ($page == 'slider'): ?>
            <div class="search-container">
                <button class="add-btn" onclick="document.getElementById('add-slider-form').classList.add('active')"><i class="fas fa-plus"></i> Thêm slider</button>
                <div class="inner-search">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="slider">
                        <input type="text" name="search" placeholder="Tìm kiếm slider hoặc liên kết..." value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-magnifying-glass"></i>
                    </form>
                    <button type="submit" form="slider-search-form" class="search-btn"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
            <div class="table-container">
                <table class="slider-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên slider</th>
                            <th>Hình ảnh</th>
                            <th>Liên kết</th>
                            <th>Thứ tự</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sliders as $slider): ?>
                            <tr>
                                <td><?php echo $slider['id']; ?></td>
                                <td><?php echo htmlspecialchars($slider['name']); ?></td>
                                <td><?php echo htmlspecialchars($slider['image']); ?></td>
                                <td><?php echo htmlspecialchars($slider['link']); ?></td>
                                <td><?php echo $slider['order']; ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="editSlider(<?php echo $slider['id']; ?>, '<?php echo addslashes($slider['name']); ?>', '<?php echo addslashes($slider['image']); ?>', '<?php echo addslashes($slider['link']); ?>', <?php echo $slider['order']; ?>)"><i class="fas fa-edit"></i> Sửa</button>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                        <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                                        <button type="submit" name="delete_slider" class="delete-btn"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="add-slider-form" class="form-container">
                <h3>Thêm slider</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Tên slider</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Hình ảnh</label>
                        <input type="text" id="image" name="image" required>
                    </div>
                    <div class="form-group">
                        <label for="link">Liên kết</label>
                        <input type="text" id="link" name="link" required>
                    </div>
                    <div class="form-group">
                        <label for="order">Thứ tự</label>
                        <input type="number" id="order" name="order" required>
                    </div>
                    <button type="submit" name="add_slider" class="submit-btn"><i class="fas fa-save"></i> Thêm</button>
                    <button onclick="document.getElementById('add-slider-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
            <div id="edit-slider-form" class="form-container">
                <h3>Sửa slider</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-name">Tên slider</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-image">Hình ảnh</label>
                        <input type="text" id="edit-image" name="image" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-link">Liên kết</label>
                        <input type="text" id="edit-link" name="link" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-order">Thứ tự</label>
                        <input type="number" id="edit-order" name="order" required>
                    </div>
                    <button type="submit" name="edit_slider" class="submit-btn"><i class="fas fa-save"></i> Lưu</button>
                    <button onclick="document.getElementById('edit-slider-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        <?php elseif ($page == 'customer'): ?>
            <div class="search-container">
                <button class="add-btn" onclick="document.getElementById('add-customer-form').classList.add('active')"><i class="fas fa-plus"></i> Thêm khách hàng</button>
                <div class="inner-search">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="customer">
                        <input type="text" name="search" placeholder="Tìm kiếm khách hàng, email, số điện thoại..." value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-magnifying-glass"></i>
                    </form>
                    <button type="submit" form="customer-search-form" class="search-btn"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
            <div class="table-container">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo $customer['id']; ?></td>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="editCustomer(<?php echo $customer['id']; ?>, '<?php echo addslashes($customer['name']); ?>', '<?php echo addslashes($customer['email']); ?>', '<?php echo addslashes($customer['phone']); ?>')"><i class="fas fa-edit"></i> Sửa</button>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                                        <button type="submit" name="delete_customer" class="delete-btn"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="add-customer-form" class="form-container">
                <h3>Thêm khách hàng</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Tên khách hàng</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="add_customer" class="submit-btn"><i class="fas fa-save"></i> Thêm</button>
                    <button onclick="document.getElementById('add-customer-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
            <div id="edit-customer-form" class="form-container">
                <h3>Sửa khách hàng</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-name">Tên khách hàng</label>
                        <input type="text" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" id="edit-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">Số điện thoại</label>
                        <input type="text" id="edit-phone" name="phone" required>
                    </div>
                    <button type="submit" name="edit_customer" class="submit-btn"><i class="fas fa-save"></i> Lưu</button>
                    <button onclick="document.getElementById('edit-customer-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        <?php elseif ($page == 'voucher'): ?>
            <div class="search-container">
                <button class="add-btn" onclick="document.getElementById('add-voucher-form').classList.add('active')"><i class="fas fa-plus"></i> Thêm voucher</button>
                <div class="inner-search">
                    <form method="GET" action="">
                        <input type="hidden" name="page" value="voucher">
                        <input type="text" name="search" placeholder="Tìm kiếm mã voucher..." value="<?php echo htmlspecialchars($search); ?>">
                        <i class="fas fa-magnifying-glass"></i>
                    </form>
                    <button type="submit" form="voucher-search-form" class="search-btn"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
            <div class="table-container">
                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã Voucher</th>
                            <th>Giảm giá</th>
                            <th>Loại giảm giá</th>
                            <th>Giá trị đơn hàng tối thiểu</th>
                            <th>Ngày hết hạn</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vouchers as $voucher): ?>
                            <tr>
                                <td><?php echo $voucher['id']; ?></td>
                                <td><?php echo htmlspecialchars($voucher['code']); ?></td>
                                <td><?php echo number_format($voucher['discount'], 0, ',', '.') . ($voucher['discount_type'] == 'percent' ? '%' : ' VNĐ'); ?></td>
                                <td><?php echo $voucher['discount_type'] == 'percent' ? 'Phần trăm' : 'Cố định'; ?></td>
                                <td><?php echo number_format($voucher['min_order_value'], 0, ',', '.') . ' VNĐ'; ?></td>
                                <td><?php echo $voucher['expires_at']; ?></td>
                                <td><?php echo $voucher['is_active'] ? 'Hoạt động' : 'Không hoạt động'; ?></td>
                                <td class="actions">
                                    <button class="edit-btn" onclick="editVoucher(<?php echo $voucher['id']; ?>, '<?php echo addslashes($voucher['code']); ?>', <?php echo $voucher['discount']; ?>, '<?php echo $voucher['discount_type']; ?>', <?php echo $voucher['min_order_value']; ?>, '<?php echo $voucher['expires_at']; ?>', <?php echo $voucher['is_active']; ?>)"><i class="fas fa-edit"></i> Sửa</button>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                        <input type="hidden" name="id" value="<?php echo $voucher['id']; ?>">
                                        <button type="submit" name="delete_voucher" class="delete-btn"><i class="fas fa-trash"></i> Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="add-voucher-form" class="form-container">
                <h3>Thêm voucher</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="code">Mã Voucher</label>
                        <input type="text" id="code" name="code" required>
                    </div>
                    <div class="form-group">
                        <label for="discount">Giảm giá</label>
                        <input type="number" id="discount" name="discount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="discount_type">Loại giảm giá</label>
                        <select id="discount_type" name="discount_type" required>
                            <option value="percent">Phần trăm</option>
                            <option value="fixed">Cố định</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="min_order_value">Giá trị đơn hàng tối thiểu</label>
                        <input type="number" id="min_order_value" name="min_order_value" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="expires_at">Ngày hết hạn</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" required>
                    </div>
                    <div class="form-group">
                        <label for="is_active">Trạng thái</label>
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label for="is_active">Hoạt động</label>
                    </div>
                    <button type="submit" name="add_voucher" class="submit-btn"><i class="fas fa-save"></i> Thêm</button>
                    <button onclick="document.getElementById('add-voucher-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
            <div id="edit-voucher-form" class="form-container">
                <h3>Sửa voucher</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-code">Mã Voucher</label>
                        <input type="text" id="edit-code" name="code" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-discount">Giảm giá</label>
                        <input type="number" id="edit-discount" name="discount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-discount_type">Loại giảm giá</label>
                        <select id="edit-discount_type" name="discount_type" required>
                            <option value="percent">Phần trăm</option>
                            <option value="fixed">Cố định</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-min_order_value">Giá trị đơn hàng tối thiểu</label>
                        <input type="number" id="edit-min_order_value" name="min_order_value" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-expires_at">Ngày hết hạn</label>
                        <input type="datetime-local" id="edit-expires_at" name="expires_at" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-is_active">Trạng thái</label>
                        <input type="checkbox" id="edit-is_active" name="is_active" value="1">
                        <label for="edit-is_active">Hoạt động</label>
                    </div>
                    <button type="submit" name="edit_voucher" class="submit-btn"><i class="fas fa-save"></i> Lưu</button>
                    <button onclick="document.getElementById('edit-voucher-form').classList.remove('active')" class="add-btn"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function editCategory(id, name, description) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-category-form').classList.add('active');
        }
        function editProduct(id, name, category, price) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-category').value = category;
            document.getElementById('edit-price').value = price;
            document.getElementById('edit-product-form').classList.add('active');
        }
        function editOrder(id, status) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-order-form').classList.add('active');
        }
        function editSlider(id, name, image, link, order) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-image').value = image;
            document.getElementById('edit-link').value = link;
            document.getElementById('edit-order').value = order;
            document.getElementById('edit-slider-form').classList.add('active');
        }
        function editCustomer(id, name, email, phone) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-customer-form').classList.add('active');
        }
        function editVoucher(id, code, discount, discount_type, min_order_value, expires_at, is_active) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-code').value = code;
            document.getElementById('edit-discount').value = discount;
            document.getElementById('edit-discount_type').value = discount_type;
            document.getElementById('edit-min_order_value').value = min_order_value;
            document.getElementById('edit-expires_at').value = expires_at;
            document.getElementById('edit-is_active').checked = is_active;
            document.getElementById('edit-voucher-form').classList.add('active');
        }
        document.getElementById('toggle-sidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('hidden');
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
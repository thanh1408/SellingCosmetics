<?php
session_start();
require_once "connect.php";

// Bật hiển thị lỗi để dễ debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];

}

// Chỉ xử lý khi có POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
    $option = isset($_POST['product_option']) ? trim($_POST['product_option']) : '';
    $price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
    $qty = isset($_POST['product_qty']) ? (int)$_POST['product_qty'] : 1;
    $session_id = session_id();

    // Khởi tạo session giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Cập nhật session cart (giỏ hàng trong phiên)
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }

    // Truy vấn ảnh sản phẩm nếu chưa có
    $sql = "SELECT product_image FROM product WHERE name = ? AND category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $option);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $product_image = $row['product_image'];
    } else {
        error_log("❌ Không tìm thấy sản phẩm với name='$name' và category='$option'");
        $product_image = 'https://example.com/default_image.jpg'; // ảnh mặc định nếu không tìm thấy
    }

    // Kiểm tra nếu đã tồn tại trong CSDL thì tăng số lượng
    $sql = "SELECT * FROM cart_items WHERE session_id = ? AND product_name = ? AND product_option = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $session_id, $name, $option);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $new_qty = $row['quantity'] + $qty;
        $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update->bind_param("ii", $new_qty, $row['id']);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO cart_items (session_id, product_name, product_option, price, quantity, product_image) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("sssdis", $session_id, $name, $option, $price, $qty, $product_image);
        $insert->execute();
    }

    $conn->close();

    $_SESSION['toast_message'] = "🎉 Đã thêm vào giỏ hàng!";
    header("Location: cart.php");
    exit();
}
?>

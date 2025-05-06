<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

// Lấy dữ liệu từ form
$name = trim($_POST['product_name']);
$option = trim($_POST['product_option']);
$price = (float)$_POST['product_price'];
$qty = isset($_POST['product_qty']) ? (int)$_POST['product_qty'] : 1;

$session_id = session_id();

// Truy vấn ảnh từ bảng product
$sql = "SELECT product_image FROM product WHERE name = ? AND category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $name, $option);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra và gán ảnh
if ($row = $result->fetch_assoc()) {
    $product_image = $row['product_image'];
} else {
    // Debug nếu không tìm thấy
    error_log("❌ Không tìm thấy sản phẩm với name='$name' và category='$option'");
    $product_image = 'https://example.com/default_image.jpg';
}

// Kiểm tra sản phẩm đã tồn tại trong giỏ chưa
$sql = "SELECT * FROM cart_items WHERE session_id = ? AND product_name = ? AND product_option = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $session_id, $name, $option);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Nếu đã có, tăng số lượng
    $new_qty = $row['quantity'] + $qty;
    $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_qty, $row['id']);
    $update->execute();
} else {
    // Nếu chưa có, thêm mới và lưu cả ảnh
    $insert = $conn->prepare("INSERT INTO cart_items (session_id, product_name, product_option, price, quantity, product_image) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("sssdis", $session_id, $name, $option, $price, $qty, $product_image);
    $insert->execute();
}

$conn->close();

// Thông báo toast + chuyển hướng
$_SESSION['toast_message'] = "🎉 Đã thêm vào giỏ hàng!";
header("Location: cart.php");
exit;

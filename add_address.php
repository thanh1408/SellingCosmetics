<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ho_ten = $_POST["ho_ten"];
    $so_dien_thoai = $_POST["so_dien_thoai"];
    $dia_chi_day_du = $_POST["dia_chi_day_du"];
    $mac_dinh = isset($_POST["mac_dinh"]) ? 1 : 0;
    if ($mac_dinh) {
        $stmt = $conn->prepare("UPDATE dia_chi SET mac_dinh = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    $stmt = $conn->prepare("INSERT INTO dia_chi (user_id, ho_ten, so_dien_thoai, dia_chi_day_du, mac_dinh) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $ho_ten, $so_dien_thoai, $dia_chi_day_du, $mac_dinh]);

    $success = true;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Địa Chỉ - Shop Mỹ Phẩm</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
        background-color: #fafafa;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: #fff;
        max-width: 480px;
        width: 100%;
        margin: auto;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
        border: 1px solid #f1f1f1;
    }

    h2 {
        text-align: center;
        font-size: 26px;
        margin-bottom: 25px;
        color: #d63384;
        font-weight: 600;
    }

    label {
        font-weight: 500;
        display: block;
        margin-bottom: 6px;
    }

    input[type="text"], input[type="tel"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }

    input[type="text"]:focus, input[type="tel"]:focus {
        border-color: #d63384;
        box-shadow: 0 0 8px rgba(245, 61, 45, 0.3);
        outline: none;
    }

    button {
        width: 100%;
        padding: 14px;
        background-color: #d63384;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #c2186a;
    }

    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4CAF50;
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        font-size: 16px;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Thêm Địa Chỉ</h2>
    <form method="POST">
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

        <label for="ho_ten">Họ tên:</label>
        <input type="text" name="ho_ten" id="ho_ten" required>

        <label for="so_dien_thoai">Số điện thoại:</label>
        <input type="tel" name="so_dien_thoai" id="so_dien_thoai" required>

        <label for="dia_chi_day_du">Địa chỉ:</label>
        <input type="text" name="dia_chi_day_du" id="dia_chi_day_du" required>

        <label for="mac_dinh">
            <input type="checkbox" name="mac_dinh" id="mac_dinh">
            Đặt làm địa chỉ mặc định
        </label>

        <button type="submit">Lưu Địa Chỉ</button>
    </form>
</div>

<?php if ($success): ?>
    <div class="toast">🎉 Lưu địa chỉ thành công! Đang chuyển đến trang xác nhận...</div>
    <script>
        setTimeout(() => {
            window.location.href = "confirm_order.php";
        }, 2000);
    </script>
<?php endif; ?>

</body>
</html>

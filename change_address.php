<?php
session_start();
// Giả sử user_id đã lưu trong session sau khi đăng nhập
$user_id = $_SESSION['user_id'] ?? 0;

$conn = new mysqli("localhost", "root", "", "db_mypham");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT * FROM dia_chi WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thay đổi địa chỉ</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .address-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }

        .address-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .default {
            color: green;
            font-weight: bold;
        }

        .btn {
            padding: 6px 10px;
            background: #e84a70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #d03b60;
        }

        .btn {
            padding: 8px 16px;
            background-color: #28a745;
            /* màu xanh */
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-left: 10px;
        }

        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="address-container">
        <h2>Địa chỉ giao hàng</h2>
        <?php if ($result->num_rows > 0): ?>
            <form action="update_address.php" method="post">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="address-item">
                        <input type="radio" name="address_id" value="<?= $row['id'] ?>" <?= $row['mac_dinh'] ? 'checked' : '' ?>>
                        <strong><?= htmlspecialchars($row['ho_ten']) ?></strong><br>
                        SĐT: <?= htmlspecialchars($row['so_dien_thoai']) ?><br>
                        Địa chỉ: <?= htmlspecialchars($row['dia_chi_day_du']) ?><br>
                        <?php if ($row['mac_dinh']): ?>
                            <span class="default">(Địa chỉ mặc định)</span>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                <br>
                <a href="add_address.php" class="btn">+ Thêm địa chỉ mới</a>
                <button type="submit" class="btn">OK</button>
            </form>
        <?php else: ?>
            <p>Bạn chưa có địa chỉ nào.</p>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>
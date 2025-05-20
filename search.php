<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "db_mypham");
$conn->set_charset("utf8");

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Lấy từ khóa tìm kiếm nếu có
$query = isset($_GET['query']) ? trim(urldecode($_GET['query'])) : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm - Luna Beauty</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
    /* Global Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #fff5f7 0%, #f8e9ec 100%);
        color: #333;
        min-height: 100vh;
        padding: 40px 20px;
    }

    /* Result Container */
    .result {
        background: #fff;
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .result:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }

    h2 {
        text-align: center;
        color: #ff6b81;
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 32px;
    }

    h2 em {
        font-style: italic;
        color: #555;
    }

    /* Product Grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }

    .product {
        background: #fff;
        border: 2px solid #ffe4e1;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }

    .product img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 16px;
        transition: transform 0.3s ease;
    }


    .product-name {
        color: #ff6b81;
        font-size: 18px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 12px;
        min-height: 48px;
        line-height: 1.4;
    }

    .product-info {
        color: #555;
        font-size: 15px;
        margin-bottom: 16px;
        text-align: center;
    }

    .product-info .price {
        color: #333;
        font-weight: 600;
    }

    /* Buttons */
    .product button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .product button.cart-btn {
        background: linear-gradient(90deg, #ff6b81 0%, #ff8e53 100%);
        color: #fff;
        margin-bottom: 12px;
    }

    .product button.cart-btn:hover {
        background: linear-gradient(90deg, #ff4d68 0%, #ff7036 100%);
        box-shadow: 0 8px 24px rgba(255, 107, 129, 0.5);
        transform: translateY(-3px);
    }

    .product button.buy-btn {
        background: #fff;
        color: #ff6b81;
        border: 2px solid #ff6b81;
    }

    .product button.buy-btn:hover {
        background: #ff6b81;
        color: #fff;
        box-shadow: 0 6px 16px rgba(255, 107, 129, 0.4);
    }

    /* No Results */
    .no-results {
        text-align: center;
        color: #555;
        font-size: 18px;
        padding: 20px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .result {
            max-width: 100%;
            padding: 32px;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .product img {
            height: 180px;
        }
    }

    @media (max-width: 768px) {
        body {
            padding: 24px 16px;
        }

        .result {
            padding: 24px;
        }

        h2 {
            font-size: 28px;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .product {
            padding: 16px;
        }

        .product img {
            height: 160px;
        }

        .product-name {
            font-size: 16px;
            min-height: 44px;
        }

        .product-info {
            font-size: 14px;
        }

        .product button {
            padding: 10px;
            font-size: 14px;
        }
    }

    @media (max-width: 600px) {
        body {
            padding: 16px;
        }

        .result {
            padding: 16px;
        }

        h2 {
            font-size: 24px;
        }

        .product-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .product {
            padding: 12px;
        }

        .product img {
            height: 140px;
        }

        .product-name {
            font-size: 15px;
            min-height: 40px;
        }

        .product-info {
            font-size: 13px;
        }

        .product button {
            padding: 8px;
            font-size: 13px;
        }
    }
</style>

<body>
    <div class="result">
        <?php if (!empty($query)): ?>
            <h2>Kết quả tìm kiếm cho: <em><?= htmlspecialchars($query) ?></em></h2>
            <?php
            // Truy vấn tìm kiếm không phân biệt chữ hoa/thường
            $sql = "SELECT * FROM product WHERE LOWER(name) LIKE ? OR LOWER(category) LIKE ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $param = '%' . mb_strtolower($query, 'UTF-8') . '%';
                $stmt->bind_param("ss", $param, $param);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div class="product-grid">';
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product">';
                        echo '<img src="images/' . htmlspecialchars($row['product_image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '<div class="product-name">' . htmlspecialchars($row['name']) . '</div>';
                        echo '<div class="product-info">';
                        echo htmlspecialchars($row['category']) . ' - <span class="price">' . number_format($row['price']) . ' VNĐ</span>';
                        echo '</div>';
                        echo '<form method="POST" action="add_to_cart.php">';
                        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                        echo '<input type="hidden" name="product_name" value="' . htmlspecialchars($row['name']) . '">';
                        echo '<input type="hidden" name="product_option" value="' . htmlspecialchars($row['category']) . '">';
                        echo '<input type="hidden" name="product_price" value="' . $row['price'] . '">';
                        echo '<input type="hidden" name="product_qty" value="1">';
                        echo '<button type="submit" class="cart-btn"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>';
                        echo '</form>';

                        echo '<form method="POST" action="checkout.php">';
                        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                        echo '<input type="hidden" name="buy_now" value="1">';
                        echo '<button type="submit" class="buy-btn"><i class="fas fa-credit-card"></i> Mua ngay</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p class="no-results">Không tìm thấy sản phẩm nào phù hợp.</p>';
                }
                $stmt->close();
            } else {
                echo '<p class="no-results">Lỗi khi chuẩn bị truy vấn: ' . htmlspecialchars($conn->error) . '</p>';
            }
            ?>
        <?php else: ?>
            <p class="no-results">Vui lòng nhập từ khóa tìm kiếm.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>
</body>

</html>

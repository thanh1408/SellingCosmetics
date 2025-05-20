<?php
session_start();
require_once "connect.php"; // File kết nối database

// Kết nối CSDL
$conn = mysqli_connect("localhost", "root", "", "db_mypham");
mysqli_set_charset($conn, "utf8");

// Kiểm tra lỗi kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM product WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Không tìm thấy sản phẩm!";
    exit;
}

// Tạo token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Kiểm tra và lấy username của người dùng hiện tại
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $current_user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Xử lý gửi, chỉnh sửa, xóa đánh giá và trả lời
$comment_success = false;
$comment_error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $comment_error = "Yêu cầu không hợp lệ.";
    } elseif (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    } else {
        $user_id = $_SESSION['user_id'];

        // Xử lý trả lời đánh giá
        if (isset($_POST['reply_to_review_id'])) {
            $review_id = intval($_POST['reply_to_review_id']);
            $reply_content = trim($_POST['reply_content']);

            if (empty($reply_content)) {
                $comment_error = "Vui lòng nhập nội dung trả lời.";
            } else {
                $sql = "INSERT INTO review_replies (review_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iis", $review_id, $user_id, $reply_content);
                if (mysqli_stmt_execute($stmt)) {
                    $comment_success = true;
                    header("Location: product_detail.php?id=$id");
                    exit();
                } else {
                    $comment_error = "Lỗi khi gửi trả lời: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        // Xử lý xóa trả lời
        elseif (isset($_POST['delete_reply_id'])) {
            $delete_id = intval($_POST['delete_reply_id']);
            $sql = "DELETE FROM review_replies WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $delete_id, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $comment_success = true;
                header("Location: product_detail.php?id=$id");
                exit();
            } else {
                $comment_error = "Lỗi khi xóa trả lời: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        // Xử lý chỉnh sửa trả lời
        elseif (isset($_POST['edit_reply_id'])) {
            $edit_id = intval($_POST['edit_reply_id']);
            $new_content = trim($_POST['edit_reply_content']);

            if (empty($new_content)) {
                $comment_error = "Vui lòng nhập nội dung trả lời.";
            } else {
                $sql = "UPDATE review_replies SET content = ? WHERE id = ? AND user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sii", $new_content, $edit_id, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $comment_success = true;
                    header("Location: product_detail.php?id=$id");
                    exit();
                } else {
                    $comment_error = "Lỗi khi chỉnh sửa trả lời: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        // Xử lý xóa đánh giá
        elseif (isset($_POST['delete_review_id'])) {
            $delete_id = intval($_POST['delete_review_id']);
            $sql = "DELETE FROM product_reviews WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $delete_id, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $comment_success = true;
                header("Location: product_detail.php?id=$id");
                exit();
            } else {
                $comment_error = "Lỗi khi xóa đánh giá: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        // Xử lý chỉnh sửa đánh giá
        elseif (isset($_POST['edit_review_id'])) {
            $edit_id = intval($_POST['edit_review_id']);
            $new_comment = trim($_POST['edit_comment']);

            if (empty($new_comment)) {
                $comment_error = "Vui lòng nhập nội dung đánh giá.";
            } else {
                $sql = "UPDATE product_reviews SET comment = ? WHERE id = ? AND user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sii", $new_comment, $edit_id, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $comment_success = true;
                    header("Location: product_detail.php?id=$id");
                    exit();
                } else {
                    $comment_error = "Lỗi khi chỉnh sửa đánh giá: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        // Xử lý gửi đánh giá mới
        elseif (isset($_POST['submit_review'])) {
            $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
            $comment = trim($_POST['comment']);

            if ($rating < 1 || $rating > 5) {
                $comment_error = "Vui lòng chọn số sao từ 1 đến 5.";
            } elseif (empty($comment)) {
                $comment_error = "Vui lòng nhập nội dung đánh giá.";
            } else {
                $sql = "INSERT INTO product_reviews (product_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iiis", $id, $user_id, $rating, $comment);
                if (mysqli_stmt_execute($stmt)) {
                    $comment_success = true;
                    header("Location: product_detail.php?id=$id");
                    exit();
                } else {
                    $comment_error = "Lỗi khi gửi đánh giá: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Lấy danh sách đánh giá
$sql = "SELECT r.*, u.username FROM product_reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$reviews_result = mysqli_stmt_get_result($stmt);
$reviews = [];
while ($row = mysqli_fetch_assoc($reviews_result)) {
    $reviews[] = $row;
}
mysqli_stmt_close($stmt);

// Lấy danh sách trả lời
$replies = [];
foreach ($reviews as $review) {
    $sql = "SELECT rr.*, u.username FROM review_replies rr JOIN users u ON rr.user_id = u.id WHERE rr.review_id = ? ORDER BY rr.created_at ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $review['id']);
    mysqli_stmt_execute($stmt);
    $replies_result = mysqli_stmt_get_result($stmt);
    $replies[$review['id']] = [];
    while ($row = mysqli_fetch_assoc($replies_result)) {
        $replies[$review['id']][] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Luna Beauty</title>
    <link rel="stylesheet" href="assets/css/product.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    <div class="container">
        <div class="image-section">
            <div class="main-image">
                <img id="mainProductImage" src="<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="thumbnail-list" id="thumbnailList">
                <!-- Thumbnails sẽ được thêm động qua JavaScript -->
            </div>
            <div class="comment-section">
                <h3>Đánh giá sản phẩm</h3>
                <?php if ($comment_success): ?>
                    <div class="toast success">Thao tác thành công!</div>
                    <script>
                        setTimeout(() => {
                            document.querySelector('.toast').remove();
                        }, 3000);
                    </script>
                <?php endif; ?>
                <?php if ($comment_error): ?>
                    <div class="toast error"><?= htmlspecialchars($comment_error) ?></div>
                    <script>
                        setTimeout(() => {
                            document.querySelector('.toast').remove();
                        }, 3000);
                    </script>
                <?php endif; ?>
                <form method="POST" class="comment-form">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="star-rating">
                        <span class="star" data-value="1"><i class="fas fa-star"></i></span>
                        <span class="star" data-value="2"><i class="fas fa-star"></i></span>
                        <span class="star" data-value="3"><i class="fas fa-star"></i></span>
                        <span class="star" data-value="4"><i class="fas fa-star"></i></span>
                        <span class="star" data-value="5"><i class="fas fa-star"></i></span>
                        <input type="hidden" name="rating" id="rating" value="0">
                    </div>
                    <textarea name="comment" placeholder="Viết đánh giá của bạn..." required></textarea>
                    <button type="submit" name="submit_review">Gửi đánh giá</button>
                </form>
                <div class="comment-list">
                    <?php if (count($reviews) > 0): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="comment">
                                <div class="comment-header">
                                    <span class="username"><?= htmlspecialchars($review['username']) ?></span>
                                    <span class="date"><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></span>
                                </div>
                                <div class="comment-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>"><i class="fas fa-star"></i></span>
                                    <?php endfor; ?>
                                </div>
                                <div class="comment-content"><?= nl2br(htmlspecialchars($review['comment'])) ?></div>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button class="dots-menu-btn" onclick="toggleActionsMenu(<?= $review['id'] ?>)">...</button>
                                    <div class="actions-menu" id="actions-menu-<?= $review['id'] ?>">
                                        <?php if ($review['user_id'] == $_SESSION['user_id']): ?>
                                            <button class="edit-comment-btn" onclick="showEditForm(<?= $review['id'] ?>, '<?= htmlspecialchars(addslashes($review['comment'])) ?>')">Chỉnh sửa</button>
                                            <form method="POST" class="delete-comment-form" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <input type="hidden" name="delete_review_id" value="<?= $review['id'] ?>">
                                                <button type="submit" class="delete-comment-btn">Xóa</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="reply-comment-btn" onclick="showReplyForm(<?= $review['id'] ?>)">Trả lời</button>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" class="edit-comment-form" id="edit-form-<?= $review['id'] ?>" style="display: none;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <textarea name="edit_comment" required><?= htmlspecialchars($review['comment']) ?></textarea>
                                        <input type="hidden" name="edit_review_id" value="<?= $review['id'] ?>">
                                        <button type="submit">Lưu</button>
                                        <button type="button" onclick="hideEditForm(<?= $review['id'] ?>)">Hủy</button>
                                    </form>
                                    <form method="POST" class="reply-input" id="reply-form-<?= $review['id'] ?>" style="display: none;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <textarea name="reply_content" placeholder="Viết trả lời của bạn..." required></textarea>
                                        <input type="hidden" name="reply_to_review_id" value="<?= $review['id'] ?>">
                                        <button type="submit" class="post-reply-btn">Đăng</button>
                                    </form>
                                <?php endif; ?>
                                <?php if (!empty($replies[$review['id']])): ?>
                                    <div class="replies">
                                        <?php foreach ($replies[$review['id']] as $reply): ?>
                                            <div class="reply">
                                                <div class="reply-header">
                                                    <span class="username"><?= htmlspecialchars($reply['username']) ?></span>
                                                    <span class="date"><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></span>
                                                </div>
                                                <div class="reply-content"><?= nl2br(htmlspecialchars($reply['content'])) ?></div>
                                                <?php if (isset($_SESSION['user_id'])): ?>
                                                    <button class="dots-menu-btn" onclick="toggleReplyActionsMenu(<?= $reply['id'] ?>)">...</button>
                                                    <div class="actions-menu" id="reply-actions-menu-<?= $reply['id'] ?>">
                                                        <?php if ($reply['user_id'] == $_SESSION['user_id']): ?>
                                                            <button class="edit-reply-btn" onclick="showEditReplyForm(<?= $reply['id'] ?>, '<?= htmlspecialchars(addslashes($reply['content'])) ?>')">Chỉnh sửa</button>
                                                            <form method="POST" class="delete-reply-form" onsubmit="return confirm('Bạn có chắc muốn xóa trả lời này?');" style="display: inline;">
                                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                <input type="hidden" name="delete_reply_id" value="<?= $reply['id'] ?>">
                                                                <button type="submit" class="delete-reply-btn">Xóa</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <button class="reply-comment-btn" onclick="showReplyForm(<?= $review['id'] ?>, <?= $reply['id'] ?>)">Trả lời</button>
                                                        <?php endif; ?>
                                                    </div>
                                                    <form method="POST" class="edit-reply-form" id="edit-reply-form-<?= $reply['id'] ?>" style="display: none;">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <textarea name="edit_reply_content" required><?= htmlspecialchars($reply['content']) ?></textarea>
                                                        <input type="hidden" name="edit_reply_id" value="<?= $reply['id'] ?>">
                                                        <button type="submit">Lưu</button>
                                                        <button type="button" onclick="hideEditReplyForm(<?= $reply['id'] ?>)">Hủy</button>
                                                    </form>
                                                    <form method="POST" class="reply-input" id="reply-form-<?= $review['id'] ?>-<?= $reply['id'] ?>" style="display: none;">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <textarea name="reply_content" placeholder="Viết trả lời của bạn..." required></textarea>
                                                        <input type="hidden" name="reply_to_review_id" value="<?= $review['id'] ?>">
                                                        <button type="submit" class="post-reply-btn">Đăng</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="info-section">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p><strong>Danh mục:</strong> <?= htmlspecialchars($product['category']) ?></p>
            <p class="price"><?= number_format($product['price'], 0, ',', '.') ?>₫</p>
            <p class="stock">Còn hàng: <?= htmlspecialchars($product['stock']) ?> sản phẩm</p>
            <form method="POST" action="add_to_cart.php" class="cart-form">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                <input type="hidden" name="product_img" value="<?= htmlspecialchars($product['product_image']) ?>">
                <div class="quantity-box">
                    <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                    <input type="text" name="product_qty" id="product_qty" value="1" readonly>
                    <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                </div>
                <div class="actions">
                    <button type="button" class="btn cart" onclick="openCartModal('<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>, '<?= htmlspecialchars($product['product_image']) ?>', <?= $product['id'] ?>)">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                    <button type="submit" form="buyNowForm" class="btn buy"><i class="fas fa-credit-card"></i> Mua ngay</button>
                </div>
            </form>
            <form method="POST" action="checkout.php" id="buyNowForm">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                <input type="hidden" name="product_option" id="buyNowOption" value="">
                <input type="hidden" name="product_img" value="<?= htmlspecialchars($product['product_image']) ?>">
                <input type="hidden" name="product_qty" id="buy_now_qty" value="1">
            </form>
            <div class="payment-methods">
                <p><strong>Phương thức thanh toán:</strong></p>
                <img src="img/visa.png" alt="Visa">
                <img src="img/momo.png" alt="MoMo">
                <img src="img/vnpay.png" alt="VNPay">
                <img src="img/mastercard.png" alt="Mastercard">
            </div>
        </div>
    </div>
    <div class="cart-modal" id="cartModal">
        <div class="cart-modal-content">
            <span class="close-btn" onclick="closeCartModal()">×</span>
            <div class="modal-header">
                <img id="modalProductImg" src="<?= htmlspecialchars($product['product_image']) ?>" alt="Sản phẩm" class="modal-img">
                <div class="product-info">
                    <h3 id="modalTitle"><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="modal-price" id="modalPrice"><?= number_format($product['price'], 0, ',', '.') ?>₫</p>
                    <p class="modal-stock">Kho: <?= htmlspecialchars($product['stock']) ?></p>
                </div>
            </div>
            <form method="POST" action="add_to_cart.php" id="addToCartForm">
                <input type="hidden" name="product_name" id="modalProductName" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" name="product_price" id="modalProductPrice" value="<?= $product['price'] ?>">
                <input type="hidden" name="product_quantity" id="modalProductQty" value="1">
                <input type="hidden" name="product_option" id="modalProductOption">
                <div class="modal-options">
                    <h4>Phân Loại</h4>
                    <div class="option-btn-group" id="variant-options"></div>
                </div>
                <div class="modal-qty-control">
                    <h4>Số lượng</h4>
                    <div class="quantity-box">
                        <button type="button" class="qty-btn-modal" onclick="decreaseQty()">−</button>
                        <input type="text" id="quantity" value="1" readonly>
                        <button type="button" class="qty-btn-modal" onclick="increaseQty()">+</button>
                    </div>
                </div>
                <button type="submit" class="modal-action-btn">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
    <script>
        // Hàm cho menu ba chấm của bình luận
        function toggleActionsMenu(reviewId) {
            const menu = document.getElementById(`actions-menu-${reviewId}`);
            const isActive = menu.classList.contains('active');
            // Đóng tất cả menu
            document.querySelectorAll('.actions-menu').forEach(m => m.classList.remove('active'));
            // Mở/đóng menu hiện tại
            if (!isActive) {
                menu.classList.add('active');
            }
        }

        // Hàm cho menu ba chấm của trả lời
        function toggleReplyActionsMenu(replyId) {
            const menu = document.getElementById(`reply-actions-menu-${replyId}`);
            const isActive = menu.classList.contains('active');
            // Đóng tất cả menu
            document.querySelectorAll('.actions-menu').forEach(m => m.classList.remove('active'));
            // Mở/đóng menu hiện tại
            if (!isActive) {
                menu.classList.add('active');
            }
        }

        // Đóng menu khi nhấn ra ngoài
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dots-menu-btn') && !e.target.closest('.actions-menu')) {
                document.querySelectorAll('.actions-menu').forEach(m => m.classList.remove('active'));
            }
        });

        // Hàm cho chỉnh sửa bình luận
        function showEditForm(reviewId, comment) {
            document.getElementById(`edit-form-${reviewId}`).style.display = 'flex';
            document.getElementById(`actions-menu-${reviewId}`).classList.remove('active');
        }

        function hideEditForm(reviewId) {
            document.getElementById(`edit-form-${reviewId}`).style.display = 'none';
        }

        // Hàm cho chỉnh sửa trả lời
        function showEditReplyForm(replyId, content) {
            document.getElementById(`edit-reply-form-${replyId}`).style.display = 'flex';
            document.getElementById(`reply-actions-menu-${replyId}`).classList.remove('active');
        }

        function hideEditReplyForm(replyId) {
            document.getElementById(`edit-reply-form-${replyId}`).style.display = 'none';
        }

        // Hàm cho trả lời
        function showReplyForm(reviewId, replyId = null) {
            const formId = replyId ? `reply-form-${reviewId}-${replyId}` : `reply-form-${reviewId}`;
            document.getElementById(formId).style.display = 'flex';
            if (replyId) {
                document.getElementById(`reply-actions-menu-${replyId}`).classList.remove('active');
            } else {
                document.getElementById(`actions-menu-${reviewId}`).classList.remove('active');
            }
        }

        function hideReplyForm(reviewId, replyId = null) {
            const formId = replyId ? `reply-form-${reviewId}-${replyId}` : `reply-form-${reviewId}`;
            document.getElementById(formId).style.display = 'none';
        }

        // Giữ nguyên các hàm JavaScript khác
        const productVariants = {
            1: [
                { name: '130g trắng', img: 'assets/images/hatomogi_trang.jpg', price: 40000 },
                { name: '130g xanh', img: 'assets/images/hatomogi_xanh.jpg', price: 40000 }
            ],
            2: [
                { name: '88ml', img: 'assets/images/product2.jpg', price: 88000 },
                { name: '236ml', img: 'assets/images/product2.jpg', price: 284000 }
            ],
            'p3': [
                { name: 'Vàng', img: 'assets/images/p31.jpg', price: 295000 },
                { name: 'Đen', img: 'assets/images/p32.jpg', price: 295000 },
                { name: 'Trắng', img: 'assets/images/p33.jpg', price: 295000 }
            ],
            'p4': [
                { name: 'A01#', img: 'assets/images/p41.jpg', price: 36300 },
                { name: 'A02#', img: 'assets/images/p42.jpg', price: 39600 }
            ],
            'p5': [
                { name: '10ml', img: 'assets/images/p5.jpg', price: 311000 },
                { name: '30ml', img: 'assets/images/p5.jpg', price: 651000 }
            ],
            'p6': [
                { name: 'JUICY 20 + Glas 16', img: 'assets/images/p61.jpg', price: 269000 },
                { name: 'JUICY 23 + Glas 16', img: 'assets/images/p62.jpg', price: 269000 }
            ],
            'p7': [
                { name: '60ml', img: 'assets/images/p7.jpg', price: 65600 }
            ],
            'p8': [
                { name: 'Sạch da giảm nhờn', img: 'assets/images/p81.jpg', price: 254000 },
                { name: 'Dịu nhẹ da nhạy', img: 'assets/images/p82.jpg', price: 254000 },
                { name: 'Dành cho da sạm', img: 'assets/images/p83.jpg', price: 254000 }
            ],
            'p9': [
                { name: 'NTT + SRM', img: 'assets/images/p9.jpg', price: 165000 },
                { name: 'NTT + KD', img: 'assets/images/p9.jpg', price: 120000 },
                { name: 'SRM + KD', img: 'assets/images/p9.jpg', price: 139000 },
                { name: 'NTT + TONER + SRM + KD', img: 'assets/images/p9.jpg', price: 304000 }
            ],
            'p10': [
                { name: 'Taupe', img: 'assets/images/p101.jpg', price: 264000 },
                { name: 'Best ever', img: 'assets/images/p102.jpg', price: 264000 }
            ]
        };

        function changeImage(imgElement) {
            document.getElementById('mainProductImage').src = imgElement.src;
            document.querySelectorAll('.thumbnail-list img').forEach(img => img.classList.remove('active'));
            imgElement.classList.add('active');
        }

        function openCartModal(name, price, img, id) {
            const modal = document.getElementById('cartModal');
            modal.style.display = 'flex';

            document.getElementById('modalTitle').textContent = name;
            document.getElementById('modalProductImg').src = img;
            document.getElementById('modalProductName').value = name;
            document.getElementById('modalProductQty').value = 1;
            document.getElementById('quantity').value = 1;

            const optionGroup = document.getElementById('variant-options');
            optionGroup.innerHTML = '';

            const variants = productVariants[id] || [];
            if (variants.length > 0) {
                variants.forEach((variant, index) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `option-btn ${index === 0 ? 'active' : ''}`;
                    btn.textContent = variant.name;
                    btn.setAttribute('data-img', variant.img);
                    btn.setAttribute('data-price', variant.price);
                    btn.onclick = () => selectOption(btn);
                    optionGroup.appendChild(btn);
                });

                const firstVariant = variants[0];
                document.getElementById('modalPrice').textContent = firstVariant.price.toLocaleString('vi-VN') + '₫';
                document.getElementById('modalProductPrice').value = firstVariant.price;
                document.getElementById('modalProductOption').value = firstVariant.name;
                document.getElementById('buyNowOption').value = firstVariant.name;
            } else {
                optionGroup.innerHTML = '<p>Không có phân loại</p>';
                document.getElementById('modalPrice').textContent = price.toLocaleString('vi-VN') + '₫';
                document.getElementById('modalProductPrice').value = price;
                document.getElementById('modalProductOption').value = '';
                document.getElementById('buyNowOption').value = '';
            }
        }

        function closeCartModal() {
            document.getElementById('cartModal').style.display = 'none';
        }

        function selectOption(btn) {
            btn.parentNode.querySelectorAll('.option-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const img = btn.getAttribute('data-img');
            const price = parseInt(btn.getAttribute('data-price'));
            document.getElementById('modalProductImg').src = img;
            document.getElementById('modalPrice').textContent = price.toLocaleString('vi-VN') + '₫';
            document.getElementById('modalProductPrice').value = price;
            document.getElementById('modalProductOption').value = btn.textContent.trim();
            document.getElementById('buyNowOption').value = btn.textContent.trim();
        }

        function decreaseQty() {
            const qtyInput = document.getElementById('quantity');
            let val = parseInt(qtyInput.value);
            if (val > 1) val--;
            qtyInput.value = val;
            document.getElementById('modalProductQty').value = val;
        }

        function increaseQty() {
            const qtyInput = document.getElementById('quantity');
            let val = parseInt(qtyInput.value);
            val++;
            qtyInput.value = val;
            document.getElementById('modalProductQty').value = val;
        }

        function changeQty(change) {
            const qtyInput = document.getElementById('product_qty');
            let val = parseInt(qtyInput.value) || 1;
            val += change;
            if (val < 1) val = 1;
            qtyInput.value = val;
            document.getElementById('buy_now_qty').value = val;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const thumbnailList = document.getElementById('thumbnailList');
            const variants = productVariants[<?= $product['id'] ?>] || [];
            const defaultImage = '<?= htmlspecialchars($product['product_image']) ?>';

            if (variants.length > 0) {
                variants.forEach((variant, index) => {
                    const img = document.createElement('img');
                    img.src = variant.img;
                    img.alt = variant.name;
                    img.className = index === 0 ? 'active' : '';
                    img.onclick = () => changeImage(img);
                    thumbnailList.appendChild(img);
                });
            } else {
                const img = document.createElement('img');
                img.src = defaultImage;
                img.alt = '<?= htmlspecialchars($product['name']) ?>';
                img.className = 'active';
                img.onclick = () => changeImage(img);
                thumbnailList.appendChild(img);
            }

            const stars = document.querySelectorAll('.star-rating .star');
            const ratingInput = document.getElementById('rating');
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    ratingInput.value = value;
                    stars.forEach(s => {
                        const sValue = parseInt(s.getAttribute('data-value'));
                        s.classList.toggle('filled', sValue <= value);
                    });
                });
                star.addEventListener('mouseover', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    stars.forEach(s => {
                        const sValue = parseInt(s.getAttribute('data-value'));
                        s.classList.toggle('filled', sValue <= value);
                    });
                });
                star.addEventListener('mouseout', () => {
                    const value = parseInt(ratingInput.value);
                    stars.forEach(s => {
                        const sValue = parseInt(s.getAttribute('data-value'));
                        s.classList.toggle('filled', sValue <= value);
                    });
                });
            });
        });
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>

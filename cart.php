<?php
session_start();

// Kiểm tra xem giỏ hàng có tồn tại không
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Giỏ hàng của bạn đang trống.";
    exit;
}

// Tính tổng giá giỏ hàng
$total_cart_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_price += $item['total_price']; // Cộng tổng giá từng sản phẩm
}

// Giảm giá
if (isset($_SESSION['discount_amount'])) {
    $total_cart_price -= $_SESSION['discount_amount']; // Áp dụng giảm giá nếu có
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cart.css">
    <title>Giỏ hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .cart-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
        }
        .total-price {
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h1>Giỏ hàng của bạn</h1>
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Biến thể</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tổng giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['variant_color']) ?>, <?= htmlspecialchars($item['variant_size']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['product_price'] + $item['variant_price'], 0, ',', '.') ?> VND</td>
                        <td><?= number_format($item['total_price'], 0, ',', '.') ?> VND</td>
                        <td>
                            <form method="POST" action="remove_from_cart.php">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                <button type="submit">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Giỏ hàng của bạn hiện tại không có sản phẩm nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Form nhập mã giảm giá -->
    <div class="discount-code">
        <h3>Nhập Mã Giảm Giá:</h3>
        <form method="POST" action="apply_discount.php">
            <input type="text" name="discount_code" placeholder="Nhập mã giảm giá" required>
            <button type="submit">Áp dụng</button>
        </form>
    </div>

    <div class="total">
        <h2>Tổng cộng: <?= number_format($total_cart_price, 0, ',', '.') ?> VND</h2>

        <?php if (isset($_SESSION['discount_amount'])): ?>
            <h3>Giảm giá: -<?= number_format($_SESSION['discount_amount'], 0, ',', '.') ?> VND</h3>
            <h2>Tổng sau khi giảm giá: <?= number_format($total_cart_price, 0, ',', '.') ?> VND</h2>
        <?php endif; ?>
    </div>

    <a href="checkout.php" class="btn">Thanh toán</a>
    <a href="index.php" class="btn">Tiếp tục mua sắm</a>
</div>

</body>
</html>

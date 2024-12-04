<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int) $_POST['product_id'];
    $variant_id = (int) $_POST['variant_id'];

    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['product_id'] === $product_id && $item['variant_id'] === $variant_id) {
                // Xóa sản phẩm khỏi giỏ hàng
                unset($_SESSION['cart'][$index]);
                // Sắp xếp lại mảng để tránh lỗ hổng index
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
    }
}

// Chuyển hướng về trang giỏ hàng
header('Location: cart.php');
exit;

<?php
session_start();

// Kiểm tra dữ liệu được gửi từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $product_id = (int) $_POST['product_id'];
    $variant_id = (int) $_POST['variant']; // Ensure this matches the form field name
    $quantity = (int) $_POST['quantity'];

    // Kết nối cơ sở dữ liệu
    $conn = new mysqli('localhost', 'root', '', 'addproducts');
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy thông tin sản phẩm
    $product_query = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
    $product_query->bind_param("i", $product_id);
    $product_query->execute();
    $product_result = $product_query->get_result();
    $product = $product_result->fetch_assoc();

    if (!$product) {
        die("Sản phẩm không tồn tại.");
    }

    // Lấy giá sản phẩm
    $product_price = $product['price'];

    // Lấy thông tin biến thể
    $variant_query = $conn->prepare("SELECT color, size, price FROM product_variants WHERE id = ?");
    $variant_query->bind_param("i", $variant_id);
    $variant_query->execute();
    $variant_result = $variant_query->get_result();
    $variant = $variant_result->fetch_assoc();

    if (!$variant) {
        die("Biến thể không tồn tại.");
    }

    // Lấy giá biến thể
    $variant_price = $variant['price'];

    // Tính toán giá tổng (giá sản phẩm + giá biến thể)
    $total_price = ($product_price + $variant_price) * $quantity;

    // Kiểm tra xem giỏ hàng đã tồn tại chưa
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] === $product_id && $item['variant_id'] === $variant_id) {
            $item['quantity'] += $quantity; // Cộng dồn số lượng
            $item['total_price'] = ($item['product_price'] + $item['variant_price']) * $item['quantity']; // Cập nhật tổng giá
            $found = true;
            break;
        }
    }

    // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'variant_id' => $variant_id,
            'quantity' => $quantity,
            'product_name' => $product['name'],
            'variant_color' => $variant['color'],
            'variant_size' => $variant['size'],
            'product_price' => $product_price, // Lưu giá sản phẩm
            'variant_price' => $variant_price, // Lưu giá biến thể
            'total_price' => $total_price // Lưu giá tổng
        ];
    }

    // Đóng kết nối
    $product_query->close();
    $variant_query->close();
    $conn->close();

    // Chuyển hướng về trang giỏ hàng
    header('Location: cart.php');
    exit;
}
?>

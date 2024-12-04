<?php

$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// Xử lý xóa sản phẩm
if (isset($_GET['delete_product_id'])) {
    $product_id = $_GET['delete_product_id'];

    // Xóa các bản ghi liên quan trong bảng `product_variants`
$stmt = $conn->prepare("DELETE FROM product_variants WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

// Xóa ảnh phụ của sản phẩm
$stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

// Xóa sản phẩm từ bảng `products`
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();

echo "<script>
alert('Xóa sản phẩm thành công!');
window.location.href = 'view_products.php';
</script>";

    
}
<?php
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$products = $conn->query("SELECT * FROM products");


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="add.css">
</head>

<body>
    <table border="1">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Mô tả</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Ảnh Sản phẩm</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $products->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td>
                    <!-- Hiển thị ảnh chính -->
                    <div class="product-images">
                        <img src="<?= htmlspecialchars($product['main_image']) ?>" class="main-image" alt="Ảnh chính">
                        <hr>
                        <!-- Hiển thị ảnh phụ -->
                        <?php
                            $product_id = $product['id'];
                            $image_query = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id");
                            if ($image_query->num_rows > 0): ?>
                        <div class="supplementary-images">
                            <?php while ($image = $image_query->fetch_assoc()): ?>
                            <img src="<?= htmlspecialchars($image['image_path']) ?>" class="supplementary-image"
                                alt="Ảnh phụ">
                            <a href="delete_image.php?id=<?= $image['id'] ?>" class="delete-image">Xóa</a>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p>Không có ảnh phụ</p>
                        <?php endif; ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td><?= htmlspecialchars($product['quantity']) ?></td>
                <td><?= number_format($product['price'], 0, ',', '.') ?> VND</td>



                <td>
                    <a href="edit_product.php?edit_product_id=<?= $product['id'] ?>">Sửa</a>
                    <a href="delete_product.php?delete_product_id=<?= $product['id'] ?>">Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

</html>
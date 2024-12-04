<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'addproducts');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy id sản phẩm từ URL
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Truy vấn thông tin chi tiết sản phẩm
$product_query = $conn->prepare("SELECT * FROM products WHERE id = ?");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

// Kiểm tra nếu có sản phẩm với id này
if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    echo "Sản phẩm không tồn tại.";
    exit;
}

// Truy vấn ảnh phụ của sản phẩm
$image_query = $conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
$image_query->bind_param("i", $product_id);
$image_query->execute();
$image_result = $image_query->get_result();

// Truy vấn các biến thể của sản phẩm
$variant_query = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ?");
$variant_query->bind_param("i", $product_id);
$variant_query->execute();
$variant_result = $variant_query->get_result();

// Đóng kết nối cơ sở dữ liệu
$product_query->close();
$image_query->close();
$variant_query->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 24px;
            color: #333;
        }

        .product-details {
            background-color: #fff;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-details img.product-image {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            display: block;
            border-radius: 8px;
        }

        .product-details h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .product-details p {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        .supplementary-images {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .supplementary-images img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-top: 20px;
            text-align: center;
        }

        form label {
            font-size: 16px;
            color: #333;
            margin-right: 10px;
        }

        form select, form input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #e60023;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        form button:hover {
            background-color: #cc0020;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-size: 16px;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Chi tiết sản phẩm</h1>

    <!-- Hiển thị thông tin sản phẩm -->
    <div class="product-details">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <img src="<?= htmlspecialchars($product['main_image']) ?>" alt="Product Image" class="product-image">
        <p><strong>Mô tả:</strong> <?= htmlspecialchars($product['description']) ?></p>
        <p><strong>Số lượng:</strong> <?= htmlspecialchars($product['quantity']) ?></p>
    </div>

    <!-- Hiển thị ảnh phụ -->
    <h3>Ảnh phụ</h3>
    <div class="supplementary-images">
        <?php while ($image = $image_result->fetch_assoc()): ?>
            <img src="<?= htmlspecialchars($image['image_path']) ?>" class="supplementary-image" alt="Ảnh phụ">
        <?php endwhile; ?>
    </div>

    <form method="POST" action="add_to_cart.php">
    <!-- Form để chọn biến thể và thêm vào giỏ hàng -->
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <p><strong>Giá:</strong> <?= number_format($product['price'], 0, ',', '.') ?> VND</p>

        <label for="variant">Biến thể:</label>
        <select name="variant" id="variant">
            <?php while ($variant = $variant_result->fetch_assoc()): ?>
                <option value="<?= $variant['id'] ?>">
                    Màu: <?= htmlspecialchars($variant['color']) ?>, Kích thước: <?= htmlspecialchars($variant['size']) ?> 
                    (Giá: <?= number_format($variant['price'], 0, ',', '.') ?> VND)
                </option>
            <?php endwhile; ?>
        </select>

        <label for="quantity">Số lượng:</label>
        <button type="button" class="decrease">-</button>
        <input type="number" id="quantity" name="quantity" min="1" value="1" onchange="updateQuantity(this)">
        <button type="button" class="increase">+</button>

        <button type="submit">Thêm vào giỏ hàng</button>
    </form>

    <a href="index.php">Trở lại danh sách sản phẩm</a>

    <script>
        // Tăng số lượng
        document.querySelector('.increase').addEventListener('click', function() {
            let quantity = document.getElementById('quantity');
            quantity.value = parseInt(quantity.value) + 1;
        });

        // Giảm số lượng
        document.querySelector('.decrease').addEventListener('click', function() {
            let quantity = document.getElementById('quantity');
            if (quantity.value > 1) {
                quantity.value = parseInt(quantity.value) - 1;
            }
        });

        // Cập nhật số lượng khi thay đổi trực tiếp
        function updateQuantity(input) {
            if (input.value < 1) {
                input.value = 1;
            }
        }
    </script>
</body>
</html>

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
    <title>Danh Sách Sản Phẩm</title>
    <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: Arial, sans-serif;
                }

                body {
                    background-color: #f5f5f5;
                }

                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }

                .product-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                    gap: 20px;
                    padding: 20px 0;
                }

                .product-card {
                    background: white;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    transition: all 0.3s ease;
                    position: relative;
                }

                .product-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                }

                .product-image {
                    width: 100%;
                    height: 350px;
                    object-fit: cover;
                    display: block;
                    transition: transform 0.5s ease;
                }

                .product-card:hover .product-image {
                    transform: scale(1.05);
                }

                .product-info {
                    padding: 15px;
                    transform: translateY(0);
                    transition: transform 0.3s ease;
                }

                .product-title {
                    font-size: 14px;
                    color: #333;
                    margin-bottom: 10px;
                    transition: color 0.3s ease;
                }

                .product-card:hover .product-title {
                    color: #666;
                }

                .product-price {
                    font-size: 16px;
                    font-weight: bold;
                    color: #333;
                    margin-bottom: 15px;
                    transition: color 0.3s ease;
                }

                .color-options {
                    display: flex;
                    gap: 8px;
                    margin-bottom: 15px;
                    opacity: 0.9;
                    transition: opacity 0.3s ease;
                }

                .product-card:hover .color-options {
                    opacity: 1;
                }

                .color-option {
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    border: 1px solid #ddd;
                    cursor: pointer;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .color-option:hover {
                    transform: scale(1.2);
                    box-shadow: 0 0 5px rgba(0,0,0,0.2);
                }
                .add-to-buy,
                .add-to-cart {
                    background: #f5f5f5;
                    color: #333;
                    border: none;
                    padding: 10px;
                    width: 100%;
                    text-align: center;
                    cursor: pointer;
                    font-size: 14px;
                    border-radius: 4px;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }
                .add-to-buy,
                .add-to-cart:before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(
                        120deg,
                        transparent,
                        rgba(255,255,255,0.6),
                        transparent
                    );
                    transition: 0.5s;
                }
                .add-to-buy,
                .add-to-cart:hover:before {
                    left: 100%;
                }
                .add-to-buy,
                .add-to-cart:hover {
                    background: #e5e5e5;
                    transform: translateY(-2px);
                }

                /* Hiệu ứng loading cho sản phẩm */
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .product-card {
                    animation: fadeIn 0.5s ease forwards;
                }

                /* Hiệu ứng hover cho màu sắc */
                .color-option {
                    position: relative;
                }

                .color-option::after {
                    content: '';
                    position: absolute;
                    top: -2px;
                    left: -2px;
                    right: -2px;
                    bottom: -2px;
                    border-radius: 50%;
                    border: 2px solid transparent;
                    transition: all 0.3s ease;
                }

                .color-option:hover::after {
                    border-color: #333;
                    transform: scale(1.1);
                }

                @media (max-width: 768px) {
                    .product-grid {
                        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                    }
                }

                @media (max-width: 480px) {
                    .product-grid {
                        grid-template-columns: 1fr;
                    }
                }

            /* Phong cách cho modal */
        .modal {
            display: none; /* Ẩn modal mặc định */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); /* Nền mờ */
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Nút đóng */
        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #333;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-content .close:hover {
            color: #e60023;
        }

        /* Tiêu đề modal */
        .modal-content h4 {
            margin: 0 0 15px 0;
            font-size: 18px;
            text-align: center;
            color: #333;
        }

        /* Form trong modal */
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .modal-content label {
            font-size: 14px;
            color: #555;
        }

        .modal-content select,
        .modal-content input[type="number"] {
            padding: 6px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        .modal-content button {
            background-color: #e60023;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-content button:hover {
            background-color: #cc0020;
        }

        /* Hiệu ứng mở modal */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

    </style>
</head>
<body>
<div class="container">
    <div class="product-grid">
        <?php while ($product = $products->fetch_assoc()): ?>
        <div class="product-card">
            <a href="detail.php?id=<?= $product['id'] ?>"><img src="<?= htmlspecialchars($product['main_image']) ?>" alt="Product" class="product-image"></a>
            <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="product-price"><?= number_format($product['price'], 0, ',', '.') ?>₫</p>
                <div class="color-options">
                    <div class="color-option" style="background-color: #4A3434;"></div>
                    <div class="color-option" style="background-color: #1E2537;"></div>
                    <div class="color-option" style="background-color: #C5A17C;"></div>
                    <div class="color-option" style="background-color: #000000;"></div>
                    <div class="color-option" style="background-color: #f5f5f5;"></div>
                </div>
                <button class="add-to-cart" data-product-id="<?= $product['id'] ?>" data-modal-id="variantModal-<?= $product['id'] ?>">Thêm nhanh vào giỏ</button>

                <!-- Modal chọn biến thể -->
                <div id="variantModal-<?= $product['id'] ?>" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h4>Chọn biến thể</h4>
                        <form id="quickAddToCartForm" method="POST" action="add_to_cart.php">
                            <input type="hidden" name="product_id" id="product_id-<?= $product['id'] ?>" value="<?= $product['id'] ?>">
                            <select name="variant" id="variant-<?= $product['id'] ?>">
                                <!-- Các tùy chọn biến thể sẽ được thêm vào bằng JavaScript -->
                            </select>
                            <label for="quantity">Số lượng:</label>
                            <input type="number" name="quantity" id="quantity-<?= $product['id'] ?>" min="1"  max="100"  value="1" oninput="this.value = this.value > 100 ? 100 : Math.max(this.value, 1);"/>
                            <br>
                            <button type="submit">Thêm vào giỏ hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-modal-id');
            const modal = document.getElementById(modalId);

            // Lấy product_id từ nút
            const productId = this.getAttribute('data-product-id');
            const variantSelect = modal.querySelector(`#variant-${productId}`);

            // Gửi AJAX để lấy danh sách biến thể
            fetch(`get_variants.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    variantSelect.innerHTML = '';
                    data.variants.forEach(variant => {
                        const option = document.createElement('option');
                        option.value = variant.id;
                        option.textContent = ` ${variant.color} ${variant.size} `;
                        variantSelect.appendChild(option);
                    });
                    modal.style.display = 'flex';
                });

            // Đóng modal khi nhấn vào nút close hoặc bên ngoài modal
            const closeModal = modal.querySelector('.close');
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    });
});

</script>
</html>
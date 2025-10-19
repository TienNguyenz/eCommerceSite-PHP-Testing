<?php

namespace App;

class checkAddToCart
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addProduct($productId, $quantity, &$session, $postData)
    {
        // Kiểm tra nếu sản phẩm đã có trong giỏ
        $productIndex = array_search($productId, $session['cart_p_id']);
        if ($productIndex !== false) {
            return ['error_message' => 'This product is already added to the shopping cart.'];
        }

        // Kiểm tra sản phẩm có tồn tại trong cơ sở dữ liệu không
        $stmt = $this->pdo->prepare("SELECT * FROM tbl_product WHERE p_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            // Kiểm tra số lượng còn lại của sản phẩm
            if ($product['p_qty'] >= $quantity) {
                // Thêm sản phẩm vào giỏ
                $session['cart_p_id'][] = $productId;
                $session['cart_p_qty'][] = $quantity;
                $session['cart_p_name'][] = $postData['p_name'];
                $session['cart_p_featured_photo'][] = $postData['p_featured_photo'];
                $session['cart_p_current_price'][] = $postData['p_current_price'];
                return ['success_message' => 'Product is added to the cart successfully!'];
            } else {
                return ['error_message' => 'Only ' . $product['p_qty'] . ' items are available for ' . $postData['p_name']];
            }
        } else {
            return ['error_message' => 'Product not found.'];
        }
    }
}

?>

<?php

namespace App;

use PHPUnit\Framework\TestCase;
use App\Database;
use App\checkAddToCart;

class checkAddToCartTest extends TestCase
{
    private $cart;
    private $pdo;

    protected function setUp(): void
    {
        // Tạo session giả
        $_SESSION = []; // Xóa mọi dữ liệu trước đó trong session

        // Khởi tạo các giỏ hàng giả cần thiết cho kiểm tra
        $_SESSION['cart_p_id'] = []; // Mảng chứa ID sản phẩm
        $_SESSION['cart_p_name'] = []; // Mảng chứa tên sản phẩm
        $_SESSION['cart_p_qty'] = []; // Mảng chứa số lượng sản phẩm
        $_SESSION['cart_p_current_price'] = []; // Mảng chứa giá sản phẩm
        $_SESSION['cart_p_featured_photo'] = []; // Mảng chứa hình ảnh sản phẩm

        // Kết nối database
        $database = new Database();
        $this->pdo = $database->getConnection();
        $this->cart = new checkAddToCart($this->pdo);
    }

    public function testAddProductSuccess()
    {
        // Giả lập session chứa giỏ hàng
        $_SESSION['cart_p_id'] = [];
        $_SESSION['cart_p_name'] = [];
        $_SESSION['cart_p_qty'] = [];
        $_SESSION['cart_p_current_price'] = [];
        $_SESSION['cart_p_featured_photo'] = [];

        // Dữ liệu kiểm tra
        $productId = 83;
        $quantity = 2;
        $postData = [
            'p_current_price' => 19,
            'p_name' => "Men's Ultra Cotton T-Shirt, Multipack",
            'p_featured_photo' => 'product-featured-83.jpg'
        ];

        // Thêm sản phẩm vào giỏ hàng
        $result = $this->cart->addProduct($productId, $quantity, $_SESSION, $postData);

        // Kiểm tra sản phẩm đã được thêm vào giỏ hàng
        $this->assertArrayHasKey('success_message', $result);
        $this->assertEquals('Product is added to the cart successfully!', $result['success_message']);
    }

    public function testAddProductOutOfStock()
    {
        // Giả lập session chứa giỏ hàng
        $_SESSION['cart_p_id'] = [];
        $_SESSION['cart_p_name'] = [];
        $_SESSION['cart_p_qty'] = [];
        $_SESSION['cart_p_current_price'] = [];
        $_SESSION['cart_p_featured_photo'] = [];

        $productId = 83;
        $quantity = 9999;
        $postData = [
            'p_current_price' => 19,
            'p_name' => "Men's Ultra Cotton T-Shirt, Multipack",
            'p_featured_photo' => 'product-featured-83.jpg'
        ];

        // Thêm sản phẩm vào giỏ hàng
        $result = $this->cart->addProduct($productId, $quantity, $_SESSION, $postData);

        // Kiểm tra lỗi nếu số lượng vượt quá tồn kho
        $this->assertArrayHasKey('error_message', $result);
        $this->assertStringContainsString('Only', $result['error_message']);
    }

    public function testAddProductNotFound()
    {
        // Giả lập session chứa giỏ hàng
        $_SESSION['cart_p_id'] = [];
        $_SESSION['cart_p_name'] = [];
        $_SESSION['cart_p_qty'] = [];
        $_SESSION['cart_p_current_price'] = [];
        $_SESSION['cart_p_featured_photo'] = [];

        $productId = 99999; // ID không tồn tại trong database
        $quantity = 1;
        $postData = [
            'p_current_price' => 100,
            'p_name' => 'Sample Product',
            'p_featured_photo' => 'sample.jpg'
        ];

        // Thêm sản phẩm không tồn tại vào giỏ hàng
        $result = $this->cart->addProduct($productId, $quantity, $_SESSION, $postData);

        // Kiểm tra lỗi nếu sản phẩm không tìm thấy
        $this->assertArrayHasKey('error_message', $result);
        $this->assertEquals('Product not found.', $result['error_message']);
    }

    public function testAddProductAlreadyInCart()
    {
        // Giả lập session chứa giỏ hàng có sản phẩm đã có trong giỏ
        $_SESSION['cart_p_id'] = [83]; // Giả sử sản phẩm với ID 1 đã có trong giỏ
        $_SESSION['cart_p_qty'] = [2];
        $_SESSION['cart_p_name'] = ['Sample Product'];
        $_SESSION['cart_p_featured_photo'] = ['sample.jpg'];
        $_SESSION['cart_p_current_price'] = [19];

        $productId = 83; // Sản phẩm đã có trong giỏ
        $quantity = 2;
        $postData = [
            'p_current_price' => 19,
            'p_name' => "Men's Ultra Cotton T-Shirt, Multipack",
            'p_featured_photo' => 'product-featured-83.jpg'
        ];

        // Kiểm tra khi sản phẩm đã có trong giỏ
        $result = $this->cart->addProduct($productId, $quantity, $_SESSION, $postData);

        // Kiểm tra xem có thông báo lỗi không được thêm sản phẩm
        $this->assertArrayHasKey('error_message', $result);
        $this->assertEquals('This product is already added to the shopping cart.', $result['error_message']);
    }
}

?>

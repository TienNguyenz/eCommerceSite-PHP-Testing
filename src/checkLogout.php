<?php
namespace App;

define('BASE_URL', 'http://localhost/eCommerceSite-PHP/');

class checkLogout {
    public $redirectUrl = null;

    public function logout() {
        if (isset($_SESSION['customer'])) {
            unset($_SESSION['customer']);
        }

        // Thay vì header() khi kiểm tra, chỉ cần lưu giá trị chuyển hướng
        $this->redirectUrl = BASE_URL . 'login.php';
        // Gọi header() chỉ khi không ở trong môi trường kiểm thử
        if (php_sapi_name() !== 'cli') {
            header("Location: " . $this->redirectUrl);
            exit;
        }
    }
}

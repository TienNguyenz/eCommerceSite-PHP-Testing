<?php
use PHPUnit\Framework\TestCase;
use App\checkLogout;

class checkLogoutTest extends TestCase {

    public function setUp(): void {
        // Bắt đầu output buffering để tránh lỗi header đã gửi
        ob_start();
    }

    public function tearDown(): void {
        // Dọn dẹp và kết thúc output buffering
        ob_end_clean();
    }

    public function testLogoutClearsSession() {
        $logout = new checkLogout();
        $logout->logout();
        
        // Kiểm tra xem session đã được xóa
        $this->assertNull($_SESSION['customer'] ?? null);
    }

    public function testLogoutRedirectsToLogin() {
        $logout = new checkLogout();
        $logout->logout();
        
        // Kiểm tra redirect URL
        $this->assertEquals('http://localhost/eCommerceSite-PHP/login.php', $logout->redirectUrl);
    }
}

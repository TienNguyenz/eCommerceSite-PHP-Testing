<?php
use PHPUnit\Framework\TestCase;
use App\Database;
use App\UserAuth;

class UserAuthTest extends TestCase {
    private $userAuth;
    
    protected function setUp(): void {
        $database = new Database();
        $this->userAuth = new UserAuth($database);
    }
    
    // Test 1: Đăng nhập hợp lệ
    public function testCheckLoginValidUser() {
        $this->assertTrue($this->userAuth->checkLogin('testuser@example.com', "Password123"));
    }
    
    // Test 2: Sai mật khẩu
    public function testCheckLoginInvalidPassword() {
        $this->assertFalse($this->userAuth->checkLogin('testuser@example.com', 12));
    }
    
    // Test 3: Người dùng không tồn tại
    public function testCheckLoginUserNotFound() {
        $this->assertFalse($this->userAuth->checkLogin('nonExistentUser', 12));
    }
    
    // Test 4: Username trống
    public function testCheckLoginEmptyUsername() {
        $this->assertFalse($this->userAuth->checkLogin('', 12));
    }
    
    // Test 5: Mật khẩu trống
    public function testCheckLoginEmptyPassword() {
        // Mật khẩu bị bỏ trống
        $this->assertFalse($this->userAuth->checkLogin('ls17189a3.11@gmail.com', ''));
    }
    
    // Test 6: SQL Injection
    public function testCheckLoginSQLInjection() {
        $maliciousUsername = "' OR '1'='1";
        $this->assertFalse($this->userAuth->checkLogin($maliciousUsername, 123));
    }
}
?>

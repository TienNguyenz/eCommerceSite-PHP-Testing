<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\checkLogin;

class checkLoginTest extends TestCase {
    private $userAuth;
    
    protected function setUp(): void {
        $database = new Database();
        $this->userAuth = new checkLogin($database);
    }
    
    // Test 1: Đăng nhập hợp lệ
    public function testCheckLoginValidUser() {
        $result = $this->userAuth->checkLogin('testuser@example.com', 'Password123');
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }
    
    // Test 2: Sai mật khẩu
    public function testCheckLoginInvalidPassword() {
        $result = $this->userAuth->checkLogin('testuser@example.com', 'wrong_password');
        $this->assertFalse($result['success']);
        $this->assertEquals('Passwords do not match.', $result['error']);
    }
    
    // Test 3: Người dùng không tồn tại
    public function testCheckLoginUserNotFound() {
        $result = $this->userAuth->checkLogin('nonExistentUser@gmail.com', 'password');
        $this->assertFalse($result['success']);
        $this->assertEquals('Email address does not match.', $result['error']);
    }
    
    // Test 4: Username trống
    public function testCheckLoginEmptyUsername() {
        $result = $this->userAuth->checkLogin('', '123');
        $this->assertFalse($result['success']);
        $this->assertEquals('Email and/or Password can not be empty', $result['error']);
    }
    
    // Test 5: Mật khẩu trống
    public function testCheckLoginEmptyPassword() {
        $result = $this->userAuth->checkLogin('testuser@example.com', '');
        $this->assertFalse($result['success']);
        $this->assertEquals('Email and/or Password can not be empty', $result['error']);
    }
    
    // Test 6: SQL Injection
    public function testCheckLoginSQLInjection() {
        $maliciousUsername = "' OR '1'='1";
        $result = $this->userAuth->checkLogin($maliciousUsername, '123');
        $this->assertFalse($result['success']);
        $this->assertEquals('Email address does not match.', $result['error']);
    }
    
    // Test 7: Tài khoản bị khóa
    public function testCheckLoginInactiveAccount() {
        $result = $this->userAuth->checkLogin('testuser1@example.com', 'Password123');
        $this->assertFalse($result['success']);
        $this->assertEquals('Sorry! Your account is inactive. Please contact to the administrator.', $result['error']);
    }
}

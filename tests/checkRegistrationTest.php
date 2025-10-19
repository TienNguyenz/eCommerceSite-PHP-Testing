<?php

use PHPUnit\Framework\TestCase;
use App\checkRegistration;
use App\Database;

class checkRegistrationTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Kết nối đến cơ sở dữ liệu thực
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    protected function tearDown(): void
    {
        // Xóa bản ghi thử nghiệm sau mỗi test
        $this->pdo->exec("DELETE FROM tbl_customer WHERE cust_email = 'email@example.com'");
    }

    public function testSuccessfulRegistration()
    {
        // Dữ liệu thử nghiệm
        $_POST = [
            'form1' => true,
            'cust_name' => 'Tan Dat',
            'cust_email' => 'email@example.com',
            'cust_phone' => '123456789',
            'cust_address' => '123 Test St',
            'cust_city' => 'Test City',
            'cust_state' => 'Test State',
            'cust_zip' => '12345',
            'cust_password' => 'password123',
            'cust_re_password' => 'password123'
        ];

        // Khởi tạo đối tượng và xử lý đăng ký
        $registration = new checkRegistration();

        // Bắt đầu ghi đầu ra để kiểm tra nội dung mô phỏng email
        ob_start();
        $registration->handleRegistration();
        $emailOutput = ob_get_clean();

        // Kiểm tra dữ liệu trong cơ sở dữ liệu thực
        $statement = $this->pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email = ?");
        $statement->execute(['email@example.com']);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra kết quả
        $this->assertNotEmpty($user, 'User was not inserted into the database.');
        $this->assertEquals('Tan Dat', $user['cust_name']);
        $this->assertEquals('email@example.com', $user['cust_email']);
        $this->assertEquals('0', $user['cust_status'], 'User status is not unverified.');

        // Kiểm tra nội dung email mô phỏng
        $this->assertStringContainsString('Simulated email to email@example.com', $emailOutput);
        $this->assertStringContainsString('http://localhost/eCommerceSite-PHP/verify.php?email=email@example.com', $emailOutput);
    }

    public function testInvalidRegistrationEmptyName()
    {
        // Dữ liệu thử nghiệm với tên bị thiếu
        $_POST = [
            'form1' => true,
            'cust_name' => '',
            'cust_email' => 'email@example.com',
            'cust_phone' => '123456789',
            'cust_address' => '123 Test St',
            'cust_city' => 'Test City',
            'cust_state' => 'Test State',
            'cust_zip' => '12345',
            'cust_password' => 'password123',
            'cust_re_password' => 'password123'
        ];

        $registration = new checkRegistration();
        $registration->handleRegistration();

        // Kiểm tra thông báo lỗi
        $this->assertStringContainsString('Name is required', $registration->getErrorMessage());
    }

    public function testEmailAlreadyExists()
    {
        // Chèn trước một bản ghi với email đã tồn tại
        $this->pdo->exec("INSERT INTO tbl_customer (cust_name, cust_email, cust_phone, cust_address, cust_city, cust_state, cust_zip, cust_password, cust_token, cust_datetime, cust_timestamp, cust_status) 
            VALUES ('Existing User', 'existing@example.com', '123456789', 'Existing Address', 'Existing City', 'Existing State', '12345', 'testpassword', 'testtoken', NOW(), UNIX_TIMESTAMP(), 0)");

        // Dữ liệu thử nghiệm với email trùng lặp
        $_POST = [
            'form1' => true,
            'cust_name' => 'John Doe',
            'cust_email' => 'existing@example.com', // Email đã tồn tại
            'cust_phone' => '123456789',
            'cust_address' => '123 Test St',
            'cust_city' => 'Test City',
            'cust_state' => 'Test State',
            'cust_zip' => '12345',
            'cust_password' => 'password123',
            'cust_re_password' => 'password123'
        ];

        $registration = new checkRegistration();
        $registration->handleRegistration();

        // Kiểm tra thông báo lỗi
        $this->assertStringContainsString('Email already exists', $registration->getErrorMessage());

        // Xóa bản ghi thử nghiệm
        $this->pdo->exec("DELETE FROM tbl_customer WHERE cust_email = 'existing@example.com'");
    }
}

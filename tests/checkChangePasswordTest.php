<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\checkChangePassword;

class checkChangePasswordTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Kết nối cơ sở dữ liệu
        $database = new Database();
        $this->pdo = $database->getConnection();

        // Thêm khách hàng mẫu
        $this->pdo->exec("
            INSERT INTO tbl_customer (cust_id, cust_password, cust_status) 
            VALUES (999, '" . md5('original_password') . "', 1)
        ");

        // Giả lập SESSION
        $_SESSION['customer'] = [
            'cust_id' => 999,
            'cust_password' => md5('original_password')
        ];
    }

    protected function tearDown(): void
    {
        // Xóa dữ liệu mẫu
        $this->pdo->exec("DELETE FROM tbl_customer WHERE cust_id = 999");
    }

    public function testChangePasswordSuccess()
    {
        $changePassword = new checkChangePassword();
        $result = $changePassword->updatePassword($_SESSION['customer'], 'new_password', 'new_password');

        // Kiểm tra thông báo thành công
        $this->assertEmpty($result['error_message']);
        $this->assertStringContainsString('Password is updated successfully.', $result['success_message']);

        // Kiểm tra cơ sở dữ liệu
        $statement = $this->pdo->prepare("SELECT cust_password FROM tbl_customer WHERE cust_id = ?");
        $statement->execute([999]);
        $customer = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(md5('new_password'), $customer['cust_password']);
    }

    public function testChangePasswordEmptyFields()
    {
        $changePassword = new checkChangePassword();
        $result = $changePassword->updatePassword($_SESSION['customer'], '', '');

        // Kiểm tra thông báo lỗi
        $this->assertStringContainsString('Password cannot be empty.', $result['error_message']);
        $this->assertEmpty($result['success_message']);
    }

    public function testChangePasswordMismatch()
    {
        $changePassword = new checkChangePassword();
        $result = $changePassword->updatePassword($_SESSION['customer'], 'password1', 'password2');

        // Kiểm tra thông báo lỗi
        $this->assertStringContainsString('Passwords do not match.', $result['error_message']);
        $this->assertEmpty($result['success_message']);
    }
}

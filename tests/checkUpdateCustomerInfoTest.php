<?php

use PHPUnit\Framework\TestCase;
use App\checkUpdateCustomerInfo;
use App\Database;

class checkUpdateCustomerInfoTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Kết nối đến cơ sở dữ liệu
        $database = new Database();
        $this->pdo = $database->getConnection();

        // Thêm khách hàng giả để kiểm tra
        $this->pdo->exec("INSERT INTO tbl_customer (cust_id, cust_name, cust_cname, cust_email, cust_phone, cust_country, cust_address, cust_city, cust_state, cust_zip, cust_status) 
            VALUES (999, 'Original Name', 'Original Company', 'testuser@example.com', '123456789', '1', 'Original Address', 'Original City', 'Original State', '12345', 1)");

        // Thiết lập SESSION giả
        $_SESSION['customer'] = [
            'cust_id' => 999,
            'cust_name' => 'Original Name',
            'cust_cname' => 'Original Company',
            'cust_email' => 'testuser@example.com',
            'cust_phone' => '123456789',
            'cust_country' => '1',
            'cust_address' => 'Original Address',
            'cust_city' => 'Original City',
            'cust_state' => 'Original State',
            'cust_zip' => '12345'
        ];
    }

    protected function tearDown(): void
    {
        // Xóa khách hàng giả sau kiểm tra
        $this->pdo->exec("DELETE FROM tbl_customer WHERE cust_id = 999");

        // Xóa SESSION
        unset($_SESSION['customer']);
    }

    public function testUpdateCustomerInfo()
    {
        $update = new checkUpdateCustomerInfo();

        $postData = [
            'cust_name' => 'Updated Name',
            'cust_cname' => 'Updated Company',
            'cust_phone' => '987654321',
            'cust_country' => '2',
            'cust_address' => 'Updated Address',
            'cust_city' => 'Updated City',
            'cust_state' => 'Updated State',
            'cust_zip' => '54321'
        ];

        // Gọi hàm update
        $result = $update->update($_SESSION['customer'], $postData);

        // Kiểm tra kết quả
        $this->assertEmpty($result['error_message'], 'Error messages found: ' . $result['error_message']);
        $this->assertEquals('Profile updated successfully.', $result['success_message']);

        // Kiểm tra cơ sở dữ liệu
        $statement = $this->pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id = ?");
        $statement->execute([999]);
        $updatedCustomer = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Updated Name', $updatedCustomer['cust_name']);
        $this->assertEquals('Updated Company', $updatedCustomer['cust_cname']);
        $this->assertEquals('987654321', $updatedCustomer['cust_phone']);
        $this->assertEquals('2', $updatedCustomer['cust_country']);
        $this->assertEquals('Updated Address', $updatedCustomer['cust_address']);
        $this->assertEquals('Updated City', $updatedCustomer['cust_city']);
        $this->assertEquals('Updated State', $updatedCustomer['cust_state']);
        $this->assertEquals('54321', $updatedCustomer['cust_zip']);
    }

    public function testCannotUpdateUnactivatedAccount()
    {
        // Cập nhật trạng thái tài khoản thành chưa kích hoạt
        $this->pdo->exec("UPDATE tbl_customer SET cust_status = 0 WHERE cust_id = 999");

        $update = new checkUpdateCustomerInfo();

        $postData = [
            'cust_name' => 'Updated Name',
            'cust_cname' => 'Updated Company',
            'cust_phone' => '987654321',
            'cust_country' => '2',
            'cust_address' => 'Updated Address',
            'cust_city' => 'Updated City',
            'cust_state' => 'Updated State',
            'cust_zip' => '54321'
        ];

        // Gọi hàm update
        $result = $update->update($_SESSION['customer'], $postData);

        // Kiểm tra kết quả
        $this->assertStringContainsString('Sorry! Your account is inactive. Please contact to the administrator.', $result['error_message']);
        $this->assertEmpty($result['success_message']);

        // Kiểm tra cơ sở dữ liệu không thay đổi
        $statement = $this->pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id = ?");
        $statement->execute([999]);
        $customer = $statement->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Original Name', $customer['cust_name']);
        $this->assertEquals('Original Company', $customer['cust_cname']);
        $this->assertEquals('123456789', $customer['cust_phone']);
    }

}

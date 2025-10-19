<?php

namespace App;

use App\Database;

class checkUpdateCustomerInfo
{
    private $pdo;
    private $error_message = '';
    private $success_message = '';

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function update($sessionCustomer, $postData)
    {
        $valid = 1;

        // Kiểm tra trạng thái kích hoạt tài khoản
        $statement = $this->pdo->prepare("SELECT cust_status FROM tbl_customer WHERE cust_id = ?");
        $statement->execute([$sessionCustomer['cust_id']]);
        $customer = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($customer['cust_status'] == 0) {
            $this->error_message .= "Sorry! Your account is inactive. Please contact to the administrator.";
            $valid = 0;
        }

        // Kiểm tra tính hợp lệ của dữ liệu
        if (empty($postData['cust_name'])) {
            $valid = 0;
            $this->error_message .= "Name is required.";
        }

        if (empty($postData['cust_phone'])) {
            $valid = 0;
            $this->error_message .= "Phone is required.";
        }

        if (empty($postData['cust_address'])) {
            $valid = 0;
            $this->error_message .= "Address is required.";
        }

        if (empty($postData['cust_country'])) {
            $valid = 0;
            $this->error_message .= "Country is required.";
        }

        if (empty($postData['cust_city'])) {
            $valid = 0;
            $this->error_message .= "City is required.";
        }

        if (empty($postData['cust_state'])) {
            $valid = 0;
            $this->error_message .= "State is required.";
        }

        if (empty($postData['cust_zip'])) {
            $valid = 0;
            $this->error_message .= "Zip is required.";
        }

        if ($valid == 1) {
            // Cập nhật cơ sở dữ liệu
            $statement = $this->pdo->prepare("UPDATE tbl_customer SET cust_name=?, cust_cname=?, cust_phone=?, cust_country=?, cust_address=?, cust_city=?, cust_state=?, cust_zip=? WHERE cust_id=?");
            $statement->execute([
                strip_tags($postData['cust_name']),
                strip_tags($postData['cust_cname']),
                strip_tags($postData['cust_phone']),
                strip_tags($postData['cust_country']),
                strip_tags($postData['cust_address']),
                strip_tags($postData['cust_city']),
                strip_tags($postData['cust_state']),
                strip_tags($postData['cust_zip']),
                $sessionCustomer['cust_id']
            ]);

            // Cập nhật SESSION
            $_SESSION['customer']['cust_name'] = $postData['cust_name'];
            $_SESSION['customer']['cust_cname'] = $postData['cust_cname'];
            $_SESSION['customer']['cust_phone'] = $postData['cust_phone'];
            $_SESSION['customer']['cust_country'] = $postData['cust_country'];
            $_SESSION['customer']['cust_address'] = $postData['cust_address'];
            $_SESSION['customer']['cust_city'] = $postData['cust_city'];
            $_SESSION['customer']['cust_state'] = $postData['cust_state'];
            $_SESSION['customer']['cust_zip'] = $postData['cust_zip'];

            $this->success_message = "Profile updated successfully.";
        }

        return [
            'error_message' => $this->error_message,
            'success_message' => $this->success_message
        ];
    }
}

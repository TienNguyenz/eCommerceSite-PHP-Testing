<?php

namespace App;

use App\Database;

class checkChangePassword
{
    private $pdo;
    private $error_message = '';
    private $success_message = '';

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function updatePassword($sessionCustomer, $password, $rePassword)
    {
        $valid = 1;

        if (empty($password) || empty($rePassword)) {
            $valid = 0;
            $this->error_message .= "Password cannot be empty.";
        }

        if (!empty($password) && !empty($rePassword)) {
            if ($password !== $rePassword) {
                $valid = 0;
                $this->error_message .= "Passwords do not match.";
            }
        }

        if ($valid === 1) {
            // Cập nhật mật khẩu
            $hashedPassword = md5(strip_tags($password));
            $statement = $this->pdo->prepare("UPDATE tbl_customer SET cust_password=? WHERE cust_id=?");
            $statement->execute([$hashedPassword, $sessionCustomer['cust_id']]);

            $_SESSION['customer']['cust_password'] = $hashedPassword;

            $this->success_message = "Password is updated successfully.";
        }

        return [
            'error_message' => $this->error_message,
            'success_message' => $this->success_message
        ];
    }
}

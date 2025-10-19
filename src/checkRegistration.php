<?php

namespace App;

use App\Database;

class checkRegistration
{
    private $pdo;
    private $errorMessage = '';

    public function __construct()
    {
        // Khởi tạo đối tượng Database và lấy kết nối PDO
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // Lấy thông báo lỗi
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    // Hàm xử lý đăng ký người dùng
    public function handleRegistration()
    {
        if (isset($_POST['form1'])) {

            $valid = 1;
            $this->errorMessage = '';

            // Kiểm tra tên
            if (empty($_POST['cust_name'])) {
                $valid = 0;
                $this->errorMessage .= 'Name is required.';
            }

            // Kiểm tra email
            if (empty($_POST['cust_email'])) {
                $valid = 0;
                $this->errorMessage .= 'Email is required.';
            } else {
                if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
                    $valid = 0;
                    $this->errorMessage .= 'Invalid email format.';
                } else {
                    // Kiểm tra email đã tồn tại chưa
                    $statement = $this->pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email = ?");
                    $statement->execute([$_POST['cust_email']]);
                    $total = $statement->rowCount();
                    if ($total) {
                        $valid = 0;
                        $this->errorMessage .= 'Email already exists.';
                    }
                }
            }

            // Kiểm tra các trường khác
            if (empty($_POST['cust_phone'])) {
                $valid = 0;
                $this->errorMessage .= 'Phone is required.';
            }
            if (empty($_POST['cust_address'])) {
                $valid = 0;
                $this->errorMessage .= 'Address is required.';
            }
            if (empty($_POST['cust_city'])) {
                $valid = 0;
                $this->errorMessage .= 'City is required.';
            }
            if (empty($_POST['cust_state'])) {
                $valid = 0;
                $this->errorMessage .= 'State is required.';
            }
            if (empty($_POST['cust_zip'])) {
                $valid = 0;
                $this->errorMessage .= 'ZIP code is required.';
            }

            // Kiểm tra mật khẩu
            if (empty($_POST['cust_password']) || empty($_POST['cust_re_password'])) {
                $valid = 0;
                $this->errorMessage .= 'Password is required.';
            } else {
                if ($_POST['cust_password'] != $_POST['cust_re_password']) {
                    $valid = 0;
                    $this->errorMessage .= 'Passwords do not match.';
                }
            }

            // Nếu không có lỗi, tiến hành lưu dữ liệu và mô phỏng gửi email
            if ($valid == 1) {
                $token = md5(time());
                $cust_datetime = date('Y-m-d h:i:s');
                $cust_timestamp = time();

                // Lưu thông tin khách hàng vào cơ sở dữ liệu
                $statement = $this->pdo->prepare("INSERT INTO tbl_customer (
                                                    cust_name,
                                                    cust_email,
                                                    cust_phone,
                                                    cust_address,
                                                    cust_city,
                                                    cust_state,
                                                    cust_zip,
                                                    cust_password,
                                                    cust_token,
                                                    cust_datetime,
                                                    cust_timestamp,
                                                    cust_status
                                                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $statement->execute([
                    strip_tags($_POST['cust_name']),
                    strip_tags($_POST['cust_email']),
                    strip_tags($_POST['cust_phone']),
                    strip_tags($_POST['cust_address']),
                    strip_tags($_POST['cust_city']),
                    strip_tags($_POST['cust_state']),
                    strip_tags($_POST['cust_zip']),
                    md5($_POST['cust_password']),
                    $token,
                    $cust_datetime,
                    $cust_timestamp,
                    0 // Trạng thái chưa xác minh
                ]);

                // Mô phỏng gửi email
                $this->simulateEmail($_POST['cust_email'], $token);
            }
        }
    }

    // Hàm mô phỏng gửi email
    private function simulateEmail($email, $token)
    {
        $verify_link = 'http://localhost/eCommerceSite-PHP/verify.php?email=' . $email . '&token=' . $token;
        $message = "Simulated email to $email with verification link: $verify_link";

        echo $message;
    }
}

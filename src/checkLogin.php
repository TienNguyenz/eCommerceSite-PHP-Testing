<?php
namespace App;

class checkLogin {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function checkLogin($email, $password) {     
        $stmt = $this->db->prepare("SELECT * FROM tbl_customer WHERE cust_email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // var_dump($user);  // Debugging dòng này để kiểm tra kết quả trả về từ database
        
        if ($email == "" || $password == "") {
            return ['success' => false, 'error' => 'Email and/or Password can not be empty'];
        }
        
        if (!$user) {
            return ['success' => false, 'error' => 'Email address does not match.'];
        }

        if (md5($password) != $user['cust_password']) {
            return ['success' => false, 'error' => 'Passwords do not match.'];
        }

        if ($user['cust_status'] == 0) {
            return ['success' => false, 'error' => 'Sorry! Your account is inactive. Please contact to the administrator.'];
        }

        return ['success' => true, 'data' => $user];
    }
}
?>

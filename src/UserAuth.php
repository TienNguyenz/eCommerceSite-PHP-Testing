<?php
namespace App;

class UserAuth {
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
        
        if ($user && md5($password) === $user['cust_password']) {
            return true;
        }
        
        return false;
    }
}
?>

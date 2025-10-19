<?php
namespace App;

class checkSearch {

    private $pdo;
    private $searchResults = [];
    private $noResultsMessage = '';

    public function __construct(Database $database) {
        $this->pdo = $database->getConnection();
    }

    public function handleSearch() {
        if (!isset($_REQUEST['search_text']) || $_REQUEST['search_text'] == '') {
            // Thay vì điều hướng, bạn có thể trả về kết quả hay thông báo gì đó
            $this->noResultsMessage = 'Search text is empty, no redirection performed.';
            return; // Kết thúc hàm mà không làm gì thêm
        }
    
        $search_text = strip_tags($_REQUEST['search_text']);
        $search_text = '%' . $search_text . '%';
    
        // Thực hiện tìm kiếm trong cơ sở dữ liệu
        $statement = $this->pdo->prepare("SELECT * FROM tbl_product WHERE p_is_active=? AND p_name LIKE ?");
        $statement->execute([1, $search_text]);
        $this->searchResults = $statement->fetchAll(\PDO::FETCH_ASSOC);
    
        // Kiểm tra nếu không có kết quả
        if (empty($this->searchResults)) {
            $this->noResultsMessage = 'No result found';
        }
    }    

    public function getSearchResults() {
        return $this->searchResults;
    }

    public function getNoResultsMessage() {
        return $this->noResultsMessage;
    }
}

?>

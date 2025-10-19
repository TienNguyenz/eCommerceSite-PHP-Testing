<?php

use PHPUnit\Framework\TestCase;
use App\Database;
use App\checkSearch;

class checkSearchTest extends TestCase {

    private $database;
    private $pdo;

    protected function setUp(): void {
        // Kết nối với cơ sở dữ liệu thực tế
        $this->database = new Database();
        $this->pdo = $this->database->getConnection();
    }

    public function testSearchWithEmptyText() {
        // Giả lập request không có từ khóa tìm kiếm
        $_REQUEST['search_text'] = '';

        // Tạo đối tượng Search với đối tượng Database thực tế
        $search = new checkSearch($this->database);

        // Gọi hàm tìm kiếm
        $search->handleSearch();

        // Kiểm tra xem có chuyển hướng về trang chủ không
        $this->assertEmpty($search->getSearchResults()); // Không có kết quả tìm kiếm
        $this->assertEquals('Search text is empty, no redirection performed.', $search->getNoResultsMessage());
    }

    public function testSearchWithValidText() {
        // Giả lập request với từ khóa tìm kiếm
        $_REQUEST['search_text'] = 'shirt';
    
        // Tạo đối tượng Search với đối tượng Database thực tế
        $search = new checkSearch($this->database);
    
        // Gọi hàm tìm kiếm
        $search->handleSearch();
    
        // Lấy kết quả tìm kiếm
        $results = $search->getSearchResults();
    
        // Hiển thị ID, name và current_price ra terminal
        foreach ($results as $product) {
            echo "ID: " . $product['p_id'] . "\n";
            echo "Name: " . $product['p_name'] . "\n";
            echo "Price: " . $product['p_current_price'] . "\n\n";
        }
    
        // Kiểm tra xem có ít nhất 1 kết quả
        $this->assertGreaterThan(0, count($results)); // Kiểm tra có ít nhất 1 kết quả
        
        $found = false;
        foreach ($results as $product) {
            if (stripos($product['p_name'], $_REQUEST['search_text']) !== false) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'No product found containing the search term.');
    }          

    public function testSearchWithNoResults() {
        // Giả lập request với từ khóa tìm kiếm không có kết quả
        $_REQUEST['search_text'] = 'nonexistent';

        // Tạo đối tượng Search với đối tượng Database thực tế
        $search = new checkSearch($this->database);

        // Gọi hàm tìm kiếm
        $search->handleSearch();

        // Kiểm tra nếu không có kết quả
        $this->assertEmpty($search->getSearchResults());
        $this->assertEquals('No result found', $search->getNoResultsMessage());
    }
}

?>

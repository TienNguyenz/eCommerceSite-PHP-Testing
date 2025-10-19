from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options

# Cấu hình ChromeDriver
chrome_options = Options()
service = Service('C:/xampp/htdocs/Phpcode/eCommerceSite-PHP/drivers/chromedriver.exe')
driver = webdriver.Chrome(service=service, options=chrome_options)

# Mở trang web
driver.get('http://localhost/Phpcode/eCommerceSite-PHP/index.php')

# Chờ người dùng nhấn phím để tiếp tục
input("Nhấn Enter để đóng trình duyệt...")

# Đóng trình duyệt sau khi người dùng nhấn phím
driver.quit()

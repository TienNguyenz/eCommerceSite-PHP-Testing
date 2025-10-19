from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
import time

# Cấu hình ChromeDriver
chrome_options = Options()
chrome_options.add_argument("--start-maximized")  # Mở trình duyệt ở chế độ tối đa

service = Service('C:/xampp/htdocs/Phpcode/eCommerceSite-PHP/drivers/chromedriver-win64/chromedriver.exe')
driver = webdriver.Chrome(service=service, options=chrome_options)

# Mở trang web
driver.get('http://localhost/Phpcode/eCommerceSite-PHP/admin/login.php')

driver.find_element(By.NAME, "email").send_keys("admin@mail.com")
time.sleep(1)

driver.find_element(By.NAME, "password").send_keys("Password@123")
time.sleep(1)

driver.find_element(By.NAME, "form1").click()


# Chờ người dùng nhấn phím để tiếp tục
input("Nhấn Enter để đóng trình duyệt...")

# Đóng trình duyệt sau khi người dùng nhấn phím
driver.quit()

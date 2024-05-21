# from selenium import webdriver
# from selenium.webdriver.common.by import By
# from selenium.webdriver.support.ui import WebDriverWait
# from selenium.webdriver.support import expected_conditions as EC
# from selenium.webdriver.chrome.service import Service
# from webdriver_manager.chrome import ChromeDriverManager
# from selenium.webdriver.common.action_chains import ActionChains
# import time

# def extract_news():
#     options = webdriver.ChromeOptions()
#     options.page_load_strategy = 'eager'
#     options.add_experimental_option("prefs", {
#         "profile.default_content_setting_values.notifications": 2  # Block notifikasi
#     })

#     driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
#     driver.implicitly_wait(10)  # seconds

#     driver.get('https://search.kompas.com/search/?q=pemilu&submit=Submit#gsc.tab=0&gsc.q=pemilu&gsc.page=1')

#     try:
#         for page in range(1, 11):  # Mengatur untuk 10 halaman
#             print(f"Memproses halaman {page}")
            
#             news_links = WebDriverWait(driver, 10).until(
#                 EC.presence_of_all_elements_located((By.XPATH, '//*[@id="___gcse_0"]/div/div/div/div[5]/div[2]/div/div/div/div/div/div/div/a'))
#             )
            
#             total_news = len(news_links)
#             for i in range(total_news):
#                 news_links[i].click()
#                 headline = WebDriverWait(driver, 10).until(
#                     EC.visibility_of_element_located((By.XPATH, '/html/body/div[1]/div[4]/div[4]/div/h1'))
#                 )
#                 print(f"Berita {i+1} halaman {page}: {headline.text}")

#                 with open('headlines.txt', 'a') as file:
#                     file.write(f"Berita {i+1} halaman {page}: {headline.text}\n")

#                 driver.back()

#                 news_links = WebDriverWait(driver, 10).until(
#                     EC.presence_of_all_elements_located((By.XPATH, '//*[@id="___gcse_0"]/div/div/div/div[5]/div[2]/div/div/div/div/div/div/div/a'))
#                 )

#             # Klik untuk ke halaman berikutnya jika bukan halaman terakhir
#             if page < 10:
#                 next_page_button = WebDriverWait(driver, 10).until(
#                     EC.element_to_be_clickable((By.XPATH, f'//*[@id="___gcse_0"]/div/div/div/div[5]/div[2]/div/div/div[2]/div/div[{page+1}]'))
#                 )
#                 ActionChains(driver).move_to_element(next_page_button).click().perform()
#                 time.sleep(2)  # Biarkan halaman berikutnya memuat

#     finally:
#         driver.quit()

# extract_news()

import os
import shutil
import requests
import csv
from datetime import datetime

def crawling(value):
    try:
        # Membuat nama file CSV berdasarkan timestamp saat ini
        timestamp = datetime.now().strftime('%Y-%m-%d_%H-%M-%S')
        csv_filename = f'api_response_{value}_{timestamp}.csv'
        folder_name = 'data-crawling'

        # Endpoint API dan parameter
        api_url = "https://yudistira.turnbackhoax.id/api/antihoax/search/"
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        }
        data = {
            'key': 'API KEY',
            'method': 'content',
            'value': value,
            # 'limit': '1'
        }

        # Mengirim permintaan POST ke API
        response = requests.post(api_url, headers=headers, data=data)
        
        # Memeriksa apakah permintaan berhasil (status code 200)
        if response.status_code == 200:
            # Mengurai respons JSON
            response_data = response.json()
            
            # Membuat folder jika belum ada
            if not os.path.exists(folder_name):
                os.makedirs(folder_name)
            
            # Mendefinisikan path file CSV
            csv_path = os.path.join(folder_name, csv_filename)

            # Membuka file CSV untuk ditulis
            with open(csv_path, 'w', newline='', encoding='utf-8') as csvfile:
                # Mendefinisikan penulis CSV
                csv_writer = csv.writer(csvfile)
                
                # Menulis baris header
                csv_writer.writerow(response_data[0].keys())
                
                # Menulis baris data
                for item in response_data:
                    csv_writer.writerow(item.values())
            
            print(f"Data berhasil disimpan ke {csv_path}.")

            # Memindahkan file CSV ke folder 'data-crawling'
            shutil.move(csv_filename, folder_name)
            print(f"File CSV dipindahkan ke folder {folder_name}.")
        else:
            print(f"Kesalahan: Permintaan API gagal dengan status code {response.status_code}")
    except Exception as e:
        print(f"Kesalahan: {e}")

def main():
    crawling("pemilu")

if __name__ == "__main__":
    main()

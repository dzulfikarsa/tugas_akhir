import os
import shutil
import requests
import csv
from datetime import datetime

def scraping(value):
    try:
        # Generate CSV filename based on current timestamp
        timestamp = datetime.now().strftime('%Y-%m-%d_%H-%M-%S')
        csv_filename = f'api_response_{value}_{timestamp}.csv'
        folder_name = 'data-crawling'

        # API endpoint and parameters
        api_url = "https://yudistira.turnbackhoax.id/api/antihoax/search/"
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        }
        data = {
            'key': '528b20z21881dxz30b0ac2',
            'method': 'content',
            'value': value,
            'limit': '1'
        }

        # Send POST request to the API
        response = requests.post(api_url, headers=headers, data=data)
        
        # Check if the request was successful (status code 200)
        if response.status_code == 200:
            # Parse the JSON response
            response_data = response.json()
            
            # Create folder if it doesn't exist
            if not os.path.exists(folder_name):
                os.makedirs(folder_name)
            
            # Define CSV file path
            csv_path = os.path.join(folder_name, csv_filename)

            # Open CSV file for writing
            with open(csv_path, 'w', newline='', encoding='utf-8') as csvfile:
                # Define CSV writer
                csv_writer = csv.writer(csvfile)
                
                # Write header row
                csv_writer.writerow(response_data[0].keys())
                
                # Write data rows
                for item in response_data:
                    csv_writer.writerow(item.values())
            
            print(f"Data saved to {csv_path} successfully.")

            # Move CSV file to the data-crawling folder
            shutil.move(csv_filename, folder_name)
            print(f"CSV file moved to {folder_name} folder.")
        else:
            print(f"Error: API request failed with status code {response.status_code}")
    except Exception as e:
        print(f"Error: {e}")

def main():
    scraping("pemilu")

if __name__ == "__main__":
    main()
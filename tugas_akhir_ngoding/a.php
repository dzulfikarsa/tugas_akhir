import json
import mysql.connector
import math

def konek_db():
    """Membuat koneksi ke database MySQL."""
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",    
        database="deteksi_hoax"
    )

def ambil_data():
    """Mengambil data dari database."""
    conn = konek_db()
    cursor = conn.cursor()
    cursor.execute("SELECT id, teks FROM data_preprocessing")
    data = cursor.fetchall()
    cursor.close()
    conn.close()
    return data

def main():
    data = ambil_data()
    results = []
    for id, text in data:
        # Example: calculating word count as the score
        score = len(text.split())
        results.append({"id": id, "text": text, "score": score})
    
    print(json.dumps(results))  # Output the results as JSON

if __name__ == "__main__":
    main()

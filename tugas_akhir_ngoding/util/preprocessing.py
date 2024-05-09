import os
import json
import mysql.connector
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import re

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",    
    database="deteksi_hoax"
)

cursor = conn.cursor()

def case_folding(data):
    # Ubah teks menjadi huruf kecil
    for item in data:
        item['title'] = item['title'].lower()
    return data

def replacing_slangword(data, slangword):
    # Membuat kamus dari list slangword yang diberikan
    slang_dict = {slang[2]: slang[1] for slang in slangword}

    # Memproses setiap dictionary dalam list data
    for item in data:
        words = item['title'].split()
        # Mengganti setiap kata tidak baku dengan kata baku sesuai kamus
        replaced_words = [slang_dict.get(word, word) for word in words]
        item['title'] = ' '.join(replaced_words)
    
    return data

def cleansing(data):
    custom_words = ["[salah]", "hoaks!", "[hoaks]" ,"[klarifikasi]", "cek fakta: tidak benar ", "keliru,", "menyesatkan,", "disinformasi", "benar,", "belum ada bukti,", "(cek fakta debat)", "menyesatkan,", "[disinformasi]", "sebagian benar,"]
    for item in data:
        original_title = item['title'].lower()
        # Menghapus custom words
        for word in custom_words:
            original_title = original_title.replace(word.lower(), '')
        # Menghapus karakter non-alfabet
        cleaned_title = re.sub(r'[^a-z\s]', '', original_title)
        # Mengganti judul yang sudah dibersihkan
        item['title'] = re.sub(r'\s+', ' ', cleaned_title).strip()  # Menghapus spasi berlebih
    return data


def stopword_removal(data):
    factory = StopWordRemoverFactory()
    stopword_remover = factory.create_stop_word_remover()

    for item in data:
        cleaned_title = stopword_remover.remove(item['title'])
        item['title'] = cleaned_title

    return data
    
def stemming(data):
    factory = StemmerFactory()
    stemmer = factory.create_stemmer()
    for item in data:
        item['title'] = stemmer.stem(item['title'])

    return data
    
    # return tokenization(stemmed_text)

def preprocessing(data):
    cursor.execute("SELECT * FROM slangword")
    slangword = cursor.fetchall()

    hasil = case_folding(data)
    hasil = replacing_slangword(hasil, slangword)
    hasil = cleansing(hasil)
    hasil = stopword_removal(hasil)
    hasil = stemming(hasil)
    return hasil

# def main():
#     data = [{"id":"16754","title":"Hoaks! Jokowi perintahkan pendemo Pemilu 2024 ditangkap"},{"id":"16797","title":"[SALAH] \ufffdKEJAHATAN KECURANGAN TSM PEMILU 14 FEBRUARI 2024 Yang Di TUTUPI\ufffd"},{"id":"16804","title":"Hoaks! Massa membakar Gedung Bawaslu tolak hasil Pemilu 2024"},{"id":"16811","title":"Keliru, Video Perayaan Kemenangan Pendukung Anies-Muhaimin Setelah Memperoleh 49 Persen Real Count"},{"id":"16814","title":"[SALAH]: Demo menolak Jokowi berkuasa"},{"id":"16819","title":"[KLARIFIKASI] Penyebab Kebakaran Asrama Polisi di Aceh Belum Diketahui"},{"id":"16840","title":"[HOAKS] Video Adian Napitupulu Dihalang-halangi Saat Sidak ke Kantor KPU"},{"id":"16932","title":"[SALAH] Harga Beras Naik Karena Diborong PDIP untuk Kampanye"},{"id":"16982","title":"Demo Ricuh Dalam Video Ini Desak Hak Angket"},{"id":"16985","title":"[KLARIFIKASI] Video Demo Ricuh di DPR pada 2019, Bukan Maret 2024"},{"id":"16995","title":"Hoaks! Risma beberkan Jokowi gunakan bansos Rp400 triliun untuk kemenangan Prabowo-Gibran"},{"id":"17000","title":"[SALAH] Keputusan KPU Batal Secara Hukum"}]

#     hasil = preprocessing(data)

# Print current working directory
print("Current Working Directory:", os.getcwd())

# Menggunakan raw string untuk path
file_path = r'C:\xampp\htdocs\tugas_akhir\tugas_akhir_ngoding\util\data.json'
# file_path = r'util\data.json'

if os.path.exists(file_path):
    with open(file_path, 'r') as file:
        data = json.load(file)
    # print("Data loaded successfully:", data)
else:
    print(f"File not found: {file_path}")
    data = []  # Inisialisasi 'data' sebagai list kosong untuk menghindari NameError

processed_data = preprocessing(data)  # Proses title

# Memasukkan hasil preprocessing ke tabel data_preprocessing
for item in processed_data:
    original_id = item['id']
    # Mengambil data dari kolom 'title' daripada 'content'
    title = item['title']  # Ubah 'content' menjadi 'title' di sini
    
    label = item['status']  # Sesuaikan ini dengan logika penentuan label Anda
    print(original_id, title, label)
    # Siapkan SQL untuk memasukkan data yang telah diproses ke database
    sql = "INSERT INTO data_preprocessing (id, teks, label) VALUES (%s, %s, %s)"
    cursor.execute(sql, (original_id, title, label))

# Commit transaksi ke database dan tutup cursor
conn.commit()
cursor.close()
conn.close()

print("Data processed successfully")
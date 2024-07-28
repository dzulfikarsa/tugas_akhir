import os
import json
import mysql.connector
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import re
from functools import lru_cache

""" Inisialisasi pembuat stemmer dan remover untuk preprocessing teks Bahasa Indonesia """
factory = StemmerFactory()
stemmer = factory.create_stemmer()

factory = StopWordRemoverFactory()
stopword_remover = factory.create_stop_word_remover()

@lru_cache(maxsize=10000)  # Cache hingga 10,000 kata unik
def cached_stem(word):
    return stemmer.stem(word) # Mengembalikan bentuk dasar kata

def case_folding(text):
    """ Ubah semua teks menjadi huruf kecil """
    text = text.lower()
    return text

def replacing_slangword(text, slangword):
    """ Mengubah slang kata menjadi kata baku berdasarkan kamus slang yang diberikan """
    # Membuat dictionary dari data slangword yang diambil dari database
    slang_dict = {slang[2]: slang[1] for slang in slangword}
    # slang_dict = {}
    # for slang in slangword:
    #     key = slang[2]
    #     value = slang[1]
    #     slang_dict[key] = value
    
    # Proses penggantian kata dilakukan sekali jalan
    return ' '.join(slang_dict.get(word, word) for word in text.split())

def cleansing(text):    
    """ Hapus karakter non-alfabet dan spasi berlebih, hanya menyisakan kata-kata dan spasi tunggal """
    text = re.sub(r'[^a-z\s]+|\s+', ' ', text).strip() # Hapus semua yang bukan huruf atau spasi berlebih
    return text

def stopword_removal(text):
    """ Hapus stopword dari teks menggunakan remover yang telah diinisialisasi """
    text = stopword_remover.remove(text) # Gunakan remover yang telah diinisialisasi untuk menghapus stopword
    return text
    
def stemming(text):
    """ Ubah kata-kata dalam teks ke bentuk dasar menggunakan fungsi stemming """
    # Langsung menggunakan comprehension dan join dalam satu baris
    return ' '.join(cached_stem(word) for word in text.split()) # Gunakan fungsi stem yang sudah di-cache untuk efisiensi

    # words = text.split()  # Memisahkan teks menjadi kata-kata
    # stemmed_words = []  # Inisialisasi daftar untuk menyimpan kata-kata yang sudah di-stem atau yang dikecualikan

    # for word in words:
    #     if word not in exclude_list:
    #         # Jika kata tidak ada dalam daftar kecualian, lakukan stemming
    #         stemmed_word = cached_stem(word)
    #     else:
    #         # Jika kata ada dalam daftar kecualian, gunakan kata asli
    #         stemmed_word = word
    #     stemmed_words.append(stemmed_word)  # Tambahkan kata yang sudah diolah ke daftar

    # # Gabungkan semua kata yang telah diproses kembali menjadi satu string
    # stemmed_text = ' '.join(stemmed_words)
    # return stemmed_text
    
def preprocessing(data, cursor):
    """ Proses semua data; melakukan query untuk slangwords, lalu melakukan preprocessing """
    cursor.execute("SELECT * FROM slangword") #ambil semua dari table slangword
    slangword = cursor.fetchall()
    
    for baris in data:
        hasil = case_folding(baris['title']) # Lakukan case folding
        hasil = cleansing(hasil) # Lakukan pembersihan
        hasil = stopword_removal(hasil) # Hapus stopwords
        hasil = replacing_slangword(hasil, slangword) # Ganti slang words
        hasil = stemming(hasil) # Lakukan stemming
        baris['title'] = hasil # Simpan hasil akhir kembali ke data

    return data

if __name__ == "__main__":
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",    
        database="deteksi_hoax"
    )

    cursor = conn.cursor()

    print("Current Working Directory:", os.getcwd())

    """ Memeriksa keberadaan file dan membuka file jika ada """
    file_path = r'C:\xampp\htdocs\tugas_akhir\tugas_akhir_ngoding\util\data.json'

    if os.path.exists(file_path):
        with open(file_path, 'r') as file:
            data = json.load(file)
        # print("Data loaded successfully:", data)
    else:
        print(f"File not found: {file_path}")
        data = []  # Jika tidak ada file, inisialisasi data sebagai list kosong

    processed_data = preprocessing(data, cursor)  # Proses title
    insert_query = "INSERT INTO data_preprocessing (id_preprocessing, teks, label) VALUES (%s, %s, %s)" #
    insert_values = [] # Inisialisasi list untuk nilai yang akan di-insert

    for item in processed_data:
        original_id = item['id_raw']
        title = item['title']
        label = item['status']
        insert_values.append((original_id, title, label))

        # Lakukan batch insert
        if len(insert_values) >= 100:  # Misalnya batch size adalah 100
            cursor.executemany(insert_query, insert_values)
            insert_values = [] # Kosongkan list setelah insert

    # Insert sisa data jika ada
    if insert_values:
        cursor.executemany(insert_query, insert_values)

    conn.commit()
    cursor.close()
    conn.close()
    print("Sukses")
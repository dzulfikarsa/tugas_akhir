import mysql.connector # Mengimpor library mysql.connector untuk operasi database
from tf_idf import tf_idf # Mengimpor fungsi tf_idf dari modul tf_idf.py
import json # Mengimpor modul json untuk memanipulasi data JSON

def konek_db():
    """Membuat koneksi ke database MySQL untuk memulai sesi dengan database."""
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",    
        database="deteksi_hoax"
    )

def ambil_data():
    """Mengambil data dari database yang akan diproses oleh model."""
    conn = konek_db()  # Membuka koneksi ke database
    cursor = conn.cursor()  # Membuat cursor database
    cursor.execute("SELECT * FROM data_training")  # Eksekusi query untuk mengambil data training
    data = cursor.fetchall()  # Ambil semua hasil dari query
    cursor.close()  # Tutup cursor
    conn.close()  # Tutup koneksi database
    return data

def save_to_json(data, filename):
    """Menyimpan data ke dalam file JSON. Digunakan untuk menyimpan model."""
    with open(filename, 'w') as f:
        json.dump(data, f, indent=4)

def hitung_kemunculan_global(data):
    """Menghitung kata unik dalam dataset untuk persiapan perhitungan TF-IDF."""
    global_kemunculan = [] # List untuk menyimpan kata unik
    for baris in data:
        kata_kalimat = baris[2].split()  # Memisahkan teks menjadi kata-kata
        for kata in kata_kalimat:
            if kata not in global_kemunculan: # Jika kata belum ada di list
                global_kemunculan.append(kata) # Tambahkan kata ke list
    return global_kemunculan

def hitung_probabilitas_prior(data):
    """Menghitung probabilitas prior, yaitu probabilitas munculnya kelas Hoax dan Non-Hoax."""
    jumlah_hoax = 0 # Jumlah data berlabel Hoax
    jumlah_non_hoax = 0 # Jumlah data berlabel Non-Hoax
    for row in data:
        if row[3] == 0:
            jumlah_hoax += 1
        else:
            jumlah_non_hoax += 1
    probabilitas_prior_class_0 = jumlah_hoax / len(data)  # Hitung probabilitas untuk Hoax
    probabilitas_prior_class_1 = jumlah_non_hoax / len(data) # Hitung probabilitas untuk Non-Hoax
    return probabilitas_prior_class_0, probabilitas_prior_class_1

def hitung_probabilitas_likelihood(total_tf_idf, total_idf, data):
    """Menghitung probabilitas likelihood, yang merupakan bagian penting dari classifier Naive Bayes."""
    total_idf_unik = {}
    total_idf_keseluruhan = 0

    kata_unik = hitung_kemunculan_global(data)
    bobot_kata_tf_idf_per_kelas = {
        0: {},
        1: {}
    }

    total_tf_idf_per_kelas = {
        0: 0,
        1: 0
    }

    # Akumulasi skor TF-IDF untuk setiap kelas
    for sublist in total_tf_idf:
        for document_id, word, tf_idf_score, label in sublist:
            if label in bobot_kata_tf_idf_per_kelas:
                if word in bobot_kata_tf_idf_per_kelas[label]:
                    bobot_kata_tf_idf_per_kelas[label][word] += tf_idf_score
                else:
                    bobot_kata_tf_idf_per_kelas[label][word] = tf_idf_score
                total_tf_idf_per_kelas[label] += tf_idf_score

    # Hitung total IDF untuk seluruh kata
    total_idf_keseluruhan = 0
    unique_words = set() 
    for sublist in total_idf:
        for index, kata, idf_value, flag in sublist:
            unique_words.add((kata, idf_value))

    for kata, idf_value in unique_words:
        total_idf_keseluruhan += idf_value

    # Menghitung likelihood untuk setiap kata
    probabilitas_likelihood = {
        0: {},
        1: {}
    }

    for label in [0, 1]:
        for word in kata_unik:
            # Add 1 to the TF-IDF score of the word (Laplace smoothing)
            hasil = (bobot_kata_tf_idf_per_kelas[label].get(word, 0) + 1) / (total_tf_idf_per_kelas[label] + total_idf_keseluruhan)
            # print(word, label, bobot_kata_tf_idf_per_kelas[label].get(word, 0), total_tf_idf_per_kelas[label], total_idf_keseluruhan)
            # print()
            # Add total IDF to the total TF-IDF of the class (Laplace smoothing in the denominator)
            probabilitas_likelihood[label][word] = hasil
    return probabilitas_likelihood

def naive_bayes():
    """Fungsi utama untuk menjalankan model Naive Bayes."""
    data = ambil_data() # Mengambil data dari database
    data = [
        (row[0], row[1], row[2], 0 if row[3] == 'Hoax' else 1) 
        for row in data
    ]
    data_teks = [row[2] for row in data] # Mengekstrak teks dari data

    hasil_tf_idf, hasil_idf = tf_idf(data) # Mendapatkan hasil TF-IDF dan IDF
    probabilitas_prior_class_0, probabilitas_prior_class_1 = hitung_probabilitas_prior(data) # Menghitung probabilitas prior

    probabilitas_likelihood = hitung_probabilitas_likelihood(hasil_tf_idf, hasil_idf, data) # Menghitung probabilitas likelihood
    save_to_json((probabilitas_prior_class_0, probabilitas_prior_class_1,probabilitas_likelihood), 'model.json') # Menyimpan model dalam format JSON

    return probabilitas_likelihood

def main():
    """Fungsi utama yang mengeksekusi model Naive Bayes."""
    hasil = naive_bayes() # Menjalankan Naive Bayes dan mendapatkan hasil
    print(json.dumps(hasil))  # Mencetak hasil dalam format JSON
    
if __name__ == '__main__':
    main()
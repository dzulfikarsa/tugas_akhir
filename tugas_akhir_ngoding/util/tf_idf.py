import math
import json

def hitung_kemunculan_global(kalimat_list):
    """
    Fungsi ini menghitung berapa kali setiap kata muncul di semua kalimat yang diberikan.
    Ini penting karena akan membantu kita mengerti seberapa umum setiap kata itu di semua data kita.

    - `global_kemunculan` adalah kamus untuk menyimpan jumlah kemunculan setiap kata.
    - `total_kata` adalah jumlah total kata yang ada di semua kalimat.
    - Fungsi ini mengembalikan kamus kemunculan kata, total kata, dan jumlah kalimat.
    """
    global_kemunculan = {} # Inisialisasi dictionary untuk menyimpan frekuensi kemunculan kata
    total_kata = 0 # Inisialisasi counter untuk total kata
    for kalimat in kalimat_list: # Loop melalui setiap kalimat
        kata_kalimat = kalimat[2].split() # Memisahkan kalimat menjadi kata-kata
        total_kata += len(kata_kalimat) # Menambahkan jumlah kata di kalimat ke total
        for kata in kata_kalimat: # Loop melalui setiap kata
            if kata in global_kemunculan:
                global_kemunculan[kata] += 1 # Tambahkan satu jika kata sudah ada di dictionary
            else:
                global_kemunculan[kata] = 1 # Atur ke satu jika kata belum ada di dictionary
    return global_kemunculan, total_kata, len(kalimat_list) # Mengembalikan hasil 

def hitung_tf(data):
    """
    Menghitung frekuensi istilah (TF) untuk setiap kata di setiap kalimat.
    Ini menunjukkan seberapa sering kata muncul dalam kalimat relatif terhadap jumlah kata di kalimat tersebut.
    
    - `hasil_tf` adalah list yang akan menyimpan frekuensi istilah untuk setiap kata dalam setiap kalimat.
    """
    hasil_tf  = [] # Inisialisasi list untuk menyimpan hasil TF dari setiap kalimat
    for index, (id, real_text, clean_text, label) in enumerate(data): # Loop melalui data
        hasil_tf_per_kalimat = [] # List untuk TF di kalimat saat ini
        kata_unik = set(clean_text.split()) # Mendapatkan set kata unik dalam kalimat
        kumpulan_kata = clean_text.split() # Mendapatkan list kata dalam kalimat
        for kata in kata_unik: # Loop melalui setiap kata unik
            hasil = kumpulan_kata.count(kata) / len(kumpulan_kata) # Hitung TF
            hasil_tf_per_kalimat.append((index, kata, hasil, label)) # Simpan hasil TF
        hasil_tf.append(hasil_tf_per_kalimat) # Tambahkan hasil per kalimat ke hasil global
    return hasil_tf # Kembalikan hasil TF

def hitung_idf(global_kemunculan, total_kalimat, data):
    """
    Menghitung Inverse Document Frequency (IDF) untuk setiap kata unik.
    Ini membantu kita mengukur seberapa penting kata tersebut dengan melihat seberapa jarang kata itu muncul di semua dokumen.
    
    - `hasil_idf` adalah list yang akan menyimpan nilai IDF untuk setiap kata.
    """
    hasil_idf = [] # Inisialisasi list untuk menyimpan hasil IDF
    for index, (id, real_text, clean_text, label) in enumerate(data): # Loop melalui data
        hasil_idf_per_kalimat = [] # List untuk IDF di kalimat saat ini
        kata_unik = set(clean_text.split()) # Mendapatkan set kata unik dalam kalimat
        for kata in kata_unik: # Loop melalui setiap kata unik
            hasil = math.log10(total_kalimat / global_kemunculan[kata]) # Hitung IDF
            hasil_idf_per_kalimat.append((index, kata, hasil, label)) # Simpan hasil IDF
        hasil_idf.append(hasil_idf_per_kalimat) # Tambahkan hasil per kalimat ke hasil global
    return hasil_idf # Kembalikan hasil IDF

def hitung_bobot(tf, idf, data):
    """
    Menghitung bobot TF-IDF dengan mengalikan nilai TF dan IDF.
    Bobot ini menunjukkan pentingnya kata dalam konteks keseluruhan dataset.

    - `hasil_tf_idf` adalah list yang menyimpan bobot TF-IDF untuk setiap kata dalam setiap kalimat.
    """
    hasil_tf_idf = [] # Inisialisasi list untuk menyimpan hasil TF-IDF
    idf_map = {(item[0], item[1], item[3]): item[2] for sublist in idf for item in sublist} # Buat kamus IDF
    for sublist in tf: # perulangan melalui data TF
        new_sublist = [] # List baru untuk TF-IDF
        for item in sublist: # perulangan melalui setiap item TF
            index, kata, tf_score, label = item
            idf_score = idf_map.get((index, kata, label), 0) # Dapatkan skor IDF
            tf_idf_score = tf_score * idf_score # Hitung skor TF-IDF
            new_sublist.append((index, kata, tf_idf_score, label)) # simpan hasil
        hasil_tf_idf.append(new_sublist) # Tambahkan ke hasil global
    return hasil_tf_idf # Kembalikan hasil TF-IDF

def save_to_json(data, file_path):
    """
    Menyimpan data ke dalam format JSON. Ini berguna untuk keperluan arsip atau analisis lebih lanjut.
    
    - `file_path` adalah lokasi di mana file JSON akan disimpan.
    """
    with open(file_path, 'w') as file: 
        json.dump(data, file, indent=4)

def tf_idf(data):
    """
    Fungsi utama untuk menghitung dan menyimpan hasil TF-IDF.
    Proses ini mencakup penghitungan global, TF, dan IDF, dan akhirnya menyimpan hasil ke file JSON.
    """
    global_kemunculan, total_kata, total_kalimat = hitung_kemunculan_global(data) # Hitung kemunculan global
    tf = hitung_tf(data) # Hitung TF
    idf = hitung_idf(global_kemunculan, total_kalimat, data) # Hitung IDF
    bobot_kata = hitung_bobot(tf, idf, data) # Hitung bobot TF-IDF
    save_to_json(bobot_kata, 'tf_idf.json') # Simpan hasil ke file JSON
    return bobot_kata, idf # kembalikan hasil

def main():
    """
    Fungsi utama yang menyiapkan data dan memulai proses perhitungan TF-IDF.
    Data ini berisi contoh-contoh kalimat dengan label 'Hoax' atau 'Non-Hoax'.
    """
    data_list = [(1, 'prabowo capai persen suara hasil pemilu taiwan', 'prabowo capai persen suara hasil pemilu taiwan', 'Hoax'), 
                 (2, 'yusril sebut prabowo gibran diskualifikasi', 'yusril sebut prabowo gibran diskualifikasi', 'Hoax'), 
                 (3, 'kades ntb amuk massa laku curang pemilu', 'kades ntb amuk massa laku curang pemilu', 'Hoax'), 
                 (4, 'masa tenang pemilu apk jalan kota bogor tertib', 'masa tenang pemilu apk jalan kota bogor tertib', 'Non-Hoax'), 
                 (5, 'surat suara pemilu coblos ilegal malaysia', 'surat suara pemilu coblos ilegal malaysia', 'Non-Hoax'), 
                 (6, 'bawaslu sebut tps indonesia sulit jangkau', 'bawaslu sebut tps indonesia sulit jangkau', 'Non-Hoax')]
    testing = tf_idf(data_list)

if __name__ == "__main__":
    main()

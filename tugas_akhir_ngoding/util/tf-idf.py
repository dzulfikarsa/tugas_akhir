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
    cursor.execute("SELECT real_text FROM data_training")
    data = [row[0] for row in cursor.fetchall()]
    cursor.close()
    conn.close()
    return data

def hitung_kata_per_kalimat(kalimat_list):
    return [len(kalimat.split()) for kalimat in kalimat_list]

def hitung_kemunculan_global(kalimat_list):
    """Menghitung kemunculan global dari setiap kata."""
    global_kemunculan = {}
    total_kata = 0
    # print("\nKata per Kalimat dan Kemunculan Global:")
    for kalimat in kalimat_list:
        kata_kalimat = kalimat.split()
        total_kata += len(kata_kalimat)
        for kata in kata_kalimat:
            if kata in global_kemunculan:
                global_kemunculan[kata] += 1
            else:
                global_kemunculan[kata] = 1
        # print(f"Kalimat: '{kalimat}' -> Kata: {kata_kalimat}")
    return global_kemunculan, total_kata, len(kalimat_list)

def hitung_tf(global_kemunculan, total_kata, data):
    hasil_tf  = []
    # return {kata: jumlah / total_kata for kata, jumlah in global_kemunculan.items()}
    for index, kalimat in enumerate(data):
        hasil_tf_per_kalimat = []
        kata_unik = set()
        kumpulan_kata = kalimat.split()
        kata_unik.update(kumpulan_kata)
        for kata in kata_unik:
            hasil = kalimat.count(kata) / len(kumpulan_kata)
            hasil_tf_per_kalimat.append((index, kata, hasil))
        hasil_tf.append(hasil_tf_per_kalimat)
    return hasil_tf

def hitung_idf(global_kemunculan, total_kalimat, data):
    # return {kata: math.log(total_kalimat / jumlah) for kata, jumlah in global_kemunculan.items()}
    hasil_idf = []
    for index, kalimat in enumerate(data):
        hasil_idf_per_kalimat = []
        kata_unik = set()
        kumpulan_kata = kalimat.split()
        kata_unik.update(kumpulan_kata)
        for kata in kata_unik:
            hasil = math.log10(total_kalimat / global_kemunculan[kata])
            hasil_idf_per_kalimat.append((index, kata, hasil))
        hasil_idf.append(hasil_idf_per_kalimat)
    return hasil_idf

def hitung_bobot(tf, idf, data):
    # return {kata: tf[kata] * idf[kata] for kata in tf}
    hasil_tf_idf = []
    idf_map = {(item[0], item[1]): item[2] for sublist in idf for item in sublist}
    for sublist in tf:
        # Membuat sublist baru untuk hasil tf-idf
        new_sublist = []
        
        for item in sublist:
            index, kata, tf_score = item
            
            # Cari idf score yang sesuai
            idf_score = idf_map.get((index, kata), 0)
            
            # Hitung tf-idf dan simpan dalam sublist baru
            tf_idf_score = tf_score * idf_score
            new_sublist.append((index, kata, tf_idf_score))
        
        # Tambahkan sublist hasil ke dalam list hasil tf-idf
        hasil_tf_idf.append(new_sublist)
    
    return hasil_tf_idf

        
        

def hitung_bobot_per_kalimat(kalimat_list, bobot_kata, n_frequency):
    bobot_per_kalimat = []
    for kalimat, nf in zip(kalimat_list, n_frequency):
        kata_kalimat = kalimat.split()
        total_bobot = sum(bobot_kata.get(kata, 0) for kata in kata_kalimat)
        bobot_per_kalimat.append(total_bobot / nf)
    return bobot_per_kalimat

def hitung_n_frequency(kata_per_kalimat, minThreshold):
    return [max(minThreshold, jumlah_kata) for jumlah_kata in kata_per_kalimat]

def tf_idf():
    hasil = []
    kalimat = ambil_data()

    # Melanjutkan dengan proses selanjutnya tanpa case folding
    global_kemunculan, total_kata, total_kalimat = hitung_kemunculan_global(kalimat)
    kata_per_kalimat = hitung_kata_per_kalimat(kalimat)

    tf = hitung_tf(global_kemunculan, total_kata, kalimat)
    # print(type(tf))
    idf = hitung_idf(global_kemunculan, total_kalimat, kalimat)
    # print(idf)
    bobot_kata = hitung_bobot(tf, idf, kalimat)
    
    return json.dumps(bobot_kata)

def main():
    testing = tf_idf()
    print(testing)    


if __name__ == "__main__":
    main()


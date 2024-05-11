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
    cursor.execute("SELECT teks FROM data_preprocessing")
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

def hitung_tf(global_kemunculan, total_kata):
    return {kata: jumlah / total_kata for kata, jumlah in global_kemunculan.items()}

def hitung_idf(global_kemunculan, total_kalimat):
    return {kata: math.log2(total_kalimat / jumlah) for kata, jumlah in global_kemunculan.items()}

def hitung_bobot(tf, idf):
    return {kata: tf[kata] * idf[kata] for kata in tf}

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
    # print("\nKalimat Asli dari Database:")
    # for idx, k in enumerate(kalimat, start=1):
    #     print(f"Kalimat {idx}: {k}")

    # Melanjutkan dengan proses selanjutnya tanpa case folding
    global_kemunculan, total_kata, total_kalimat = hitung_kemunculan_global(kalimat)
    tf = hitung_tf(global_kemunculan, total_kata)
    idf = hitung_idf(global_kemunculan, total_kalimat)
    bobot_kata = hitung_bobot(tf, idf)
    kata_per_kalimat = hitung_kata_per_kalimat(kalimat)
    minThreshold = total_kata / total_kalimat
    n_frequency = hitung_n_frequency(kata_per_kalimat, minThreshold)
    bobot_per_kalimat = hitung_bobot_per_kalimat(kalimat, bobot_kata, n_frequency)

    # print("\nNilai Bobot untuk Setiap Kalimat:")
    # for idx, bobot in enumerate(bobot_per_kalimat, start=1):
    #     print(f"Bobot = {bobot}")

    max_weight = max(bobot_per_kalimat)
    max_index = bobot_per_kalimat.index(max_weight)

    for teks, bobot in zip(kalimat, bobot_per_kalimat):
        hasil.append((teks, bobot))

    return json.dumps(hasil)
    # print(f"\nKalimat dengan Bobot Tertinggi:")
    # print(f"Kalimat {max_index + 1}: Bobot = {max_weight} -> '{kalimat[max_index]}'")

def main():
    testing = tf_idf()
    print(testing)


if __name__ == "__main__":
    main()


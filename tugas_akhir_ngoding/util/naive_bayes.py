import mysql.connector
from tf_idf import tf_idf
import json
import pickle

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
    cursor.execute("SELECT * FROM data_training")
    data = cursor.fetchall()
    cursor.close()
    conn.close()
    return data

def save_with_pickle(data, filename):
    with open(filename, 'wb') as f:
        pickle.dump(data, f)

def hitung_kemunculan_global(data):
    global_kemunculan = []
    # print("\nKata per Kalimat dan Kemunculan Global:")
    for baris in data:
        kata_kalimat = baris[2].split()
        for kata in kata_kalimat:
            if kata not in global_kemunculan:
                global_kemunculan.append(kata)
        # print(f"Kalimat: '{kalimat}' -> Kata: {kata_kalimat}")
    return global_kemunculan


def hitung_probabilitas_prior(data):
    jumlah_hoax = 0
    jumlah_non_hoax = 0
    for row in data:
        if row[3] == 0:
            jumlah_hoax += 1
        else:
            jumlah_non_hoax += 1
    probabilitas_prior_class_0 = jumlah_hoax / len(data)
    probabilitas_prior_class_1 = jumlah_non_hoax / len(data)
    return probabilitas_prior_class_0, probabilitas_prior_class_1

def hitung_probabilitas_likelihood(total_tf_idf, total_idf, data):
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

    for sublist in total_tf_idf:
        for document_id, word, tf_idf_score, label in sublist:
            if label in bobot_kata_tf_idf_per_kelas:
                if word in bobot_kata_tf_idf_per_kelas[label]:
                    bobot_kata_tf_idf_per_kelas[label][word] += tf_idf_score
                else:
                    bobot_kata_tf_idf_per_kelas[label][word] = tf_idf_score
                total_tf_idf_per_kelas[label] += tf_idf_score
    for sublist in total_idf:
        for document_id, word, idf_value, label in sublist:
            if word not in total_idf_unik:
                total_idf_unik[word] = idf_value
            else:
                assert total_idf_unik[word] == idf_value
            total_idf_keseluruhan += idf_value

    #mulai perhitungan probabilitas likelihood
    probabilitas_likelihood = {
        0: {},
        1: {}
    }

    for label in [0, 1]:
        for word in kata_unik:
            # Add 1 to the TF-IDF score of the word (Laplace smoothing)
            hasil = (bobot_kata_tf_idf_per_kelas[label].get(word, 0) + 1) / (total_tf_idf_per_kelas[label] + total_idf_keseluruhan)
            # Add total IDF to the total TF-IDF of the class (Laplace smoothing in the denominator)
            probabilitas_likelihood[label][word] = hasil
    # print(probabilitas_likelihood)
    # print(total_tf_idf)
    # print(total_tf_idf_per_kelas)
    # print(total_idf_keseluruhan)
    return probabilitas_likelihood

def naive_bayes():
    data = ambil_data()
    data = [
        (row[0], row[1], row[2], 0 if row[3] == 'Hoax' else 1) 
        for row in data
    ]
    data_teks = [row[2] for row in data]

    hasil_tf_idf, hasil_idf = tf_idf(data)
    probabilitas_prior_class_0, probabilitas_prior_class_1 = hitung_probabilitas_prior(data)

    probabilitas_likelihood = hitung_probabilitas_likelihood(hasil_tf_idf, hasil_idf, data)
    save_with_pickle(probabilitas_likelihood, 'model.pkl')
    return probabilitas_likelihood

def main():
    hasil = naive_bayes()
    print(json.dumps(hasil))
    
if __name__ == '__main__':
    main()
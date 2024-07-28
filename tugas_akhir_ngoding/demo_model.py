import sys # Mengimpor modul sys untuk mengakses argumen baris perintah
import json # Mengimpor modul json untuk memanipulasi data JSON
import math # Mengimpor modul math untuk fungsi matematika
from util.preprocessing import preprocessing # Mengimpor fungsi preprocessing dari modul util
import mysql.connector 

# Fungsi untuk memuat model dari file JSON
def load_model(filename):
    with open(filename, 'r') as file: # Membuka file JSON
        data = json.load(file) # Membaca data JSON
        prior_hoax = data[0] # Mengambil nilai prior hoax
        prior_non_hoax = data[1] # Mengambil nilai prior non-hoax
        likelihood_hoax = data[2]["0"] #Mengambil nilai likelihood hoax
        likelihood_non_hoax = data[2]["1"] # Mengambil nilai likelihood non-hoax
    
    return prior_hoax, prior_non_hoax, likelihood_hoax, likelihood_non_hoax

# Fungsi untuk menghitung probabilitas teks sebagai hoax atau non-hoax
def calculate_probabilities(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax):
    words = text.lower().split() # Memecah teks menjadi kata-kata dan mengubahnya menjadi huruf kecil
    log_prob_hoax = math.log(prior_hoax) # Menghitung log probabilitas prior hoax
    log_prob_non_hoax = math.log(prior_non_hoax) # Menghitung log probabilitas prior non-hoax
    
    for word in words:  # Iterasi setiap kata dalam teks
        log_prob_hoax += math.log(likelihood_hoax.get(word, 1e-10)) # Menambahkan log likelihood hoax
        log_prob_non_hoax += math.log(likelihood_non_hoax.get(word, 1e-10)) # Menambahkan log likelihood non-hoax
    
    return log_prob_hoax, log_prob_non_hoax

# Fungsi untuk mengklasifikasikan teks
def classify_text(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax):
    log_prob_hoax, log_prob_non_hoax = calculate_probabilities(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax)
    
    if log_prob_hoax > log_prob_non_hoax: # Membandingkan probabilitas log hoax dan non-hoax
        return 'Hoax'
    else:
        return 'Non-Hoax' # Mengembalikan Non-Hoax jika tidak

#bagian utama yang dieksekusi jika script ini dijalankan sebagai script utama
if __name__ == "__main__":  
    conn = mysql.connector.connect( # Membuat koneksi ke database MySQL
        host="localhost",
        user="root",
        password="",    
        database="deteksi_hoax"
    )

    cursor = conn.cursor()  # Membuat kursor untuk operasi database
    prior_hoax, prior_non_hoax, likelihood_hoax, likelihood_non_hoax = load_model("model.json") # memuat file model
    text = sys.argv[1] # Mengambil teks dari argumen baris perintah
    data = [{
        "title": text,
    }]
    classification = classify_text(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax) #mengklasifikasikan teks
    print(classification) # mencetak hasil klasifikasi

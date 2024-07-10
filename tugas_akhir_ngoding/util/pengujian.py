import mysql.connector
import json

""" Membuat koneksi ke database MySQL. """
db = mysql.connector.connect(
    host="localhost",        # atau alamat IP server database
    user="root",    # ganti dengan username database Anda
    password="",# ganti dengan password database Anda
    database="deteksi_hoax" # ganti dengan nama database Anda
)

cursor = db.cursor(dictionary=True)

""" Memuat model prediksi dari file JSON. """
with open('model.json', 'r') as file:
    data = json.load(file)

prior_class_0 = data[0]
prior_class_1 = data[1]
likelihoods = data[2]

likelihoods_0 = likelihoods["0"]
likelihoods_1 = likelihoods["1"]

""" Mengambil data testing dari database untuk evaluasi. """
cursor.execute("SELECT id_testing, real_text, clean_text, label FROM data_testing")
data_testing = cursor.fetchall()

results = []

""" Menghitung posterior untuk setiap entri data testing berdasarkan model. """
for row in data_testing:
    words = row['clean_text'].split()  # Asumsikan clean_text sudah dalam bentuk kata-kata terpisah
    prob_0 = prior_class_0
    prob_1 = prior_class_1
    
    for word in words:
        if word in likelihoods_0:
            prob_0 *= likelihoods_0[word]
        if word in likelihoods_1:
            prob_1 *= likelihoods_1[word]
    
    """ Menentukan label prediksi berdasarkan probabilitas yang lebih tinggi. """
    predicted_label = 'hoax' if prob_0 > prob_1 else 'non-hoax'
    
    """ Menyimpan hasil ke dalam daftar untuk evaluasi lebih lanjut. """
    results.append({
        "id": row['id_testing'],
        "real_text": row['real_text'],
        "label": row['label'],
        "predicted_label": predicted_label
    })

""" Menyimpan hasil prediksi ke dalam file JSON. """
with open('prediction_results.json', 'w') as outfile:
    json.dump(results, outfile, indent=4)

""" Memuat hasil prediksi dari file JSON untuk analisis lebih lanjut. """
with open('prediction_results.json', 'r') as file:
    predictions = json.load(file)

# Inisialisasi confusion matrix components
TP = TN = FP = FN = 0

""" Menghitung komponen confusion matrix berdasarkan hasil prediksi. """
for result in predictions:
    actual = result['label'].lower()
    predicted = result['predicted_label'].lower()

    if actual == 'non-hoax' and predicted == 'non-hoax':
        TN += 1
    elif actual == 'hoax' and predicted == 'hoax':
        TP += 1
    elif actual == 'non-hoax' and predicted == 'hoax':
        FP += 1
    elif actual == 'hoax' and predicted == 'non-hoax':
        FN += 1

""" Menghitung metrik evaluasi berdasarkan komponen confusion matrix. """
accuracy = (TP + TN) / (TP + TN + FP + FN)
precision = TP / (TP + FP) if (TP + FP) != 0 else 0
recall = TP / (TP + FN) if (TP + FN) != 0 else 0

""" Menyimpan metrik evaluasi ke dalam file JSON untuk dokumentasi dan analisis lebih lanjut. """
confusion_matrix = {
    "TP (True Positive)": TP,
    "TN (True Negative)": TN,
    "FP (False Positive)": FP,
    "FN (False Negative)": FN,
    "Accuracy": accuracy,
    "Precision": precision,
    "Recall": recall
}

# Langkah 4: Menyimpan data ke JSON
with open('confusion_matrix.json', 'w') as outfile:
    json.dump(confusion_matrix, outfile, indent=4)

# Tutup koneksi database
db.close()

print("sukses")
import mysql.connector
import json

# Langkah 1: Koneksi ke Database
db = mysql.connector.connect(
    host="localhost",        # atau alamat IP server database
    user="root",    # ganti dengan username database Anda
    password="",# ganti dengan password database Anda
    database="deteksi_hoax" # ganti dengan nama database Anda
)

cursor = db.cursor(dictionary=True)

# Langkah 2: Memuat model JSON
with open('model.json', 'r') as file:
    data = json.load(file)

prior_class_0 = data[0]
prior_class_1 = data[1]
likelihoods = data[2]

likelihoods_0 = likelihoods["0"]
likelihoods_1 = likelihoods["1"]

# Langkah 3: Mengambil Data Testing dari Database
cursor.execute("SELECT id_testing, real_text, clean_text, label FROM data_testing")
data_testing = cursor.fetchall()

results = []

# Langkah 4: Menghitung posterior untuk setiap entri dalam data testing
for row in data_testing:
    words = row['clean_text'].split()  # Asumsikan clean_text sudah dalam bentuk kata-kata terpisah
    prob_0 = prior_class_0
    prob_1 = prior_class_1
    
    for word in words:
        if word in likelihoods_0:
            prob_0 *= likelihoods_0[word]
        if word in likelihoods_1:
            prob_1 *= likelihoods_1[word]
    
    # Menentukan label prediksi
    predicted_label = 'hoax' if prob_0 > prob_1 else 'non-hoax'
    
    # Menyimpan hasil
    results.append({
        "id": row['id_testing'],
        "real_text": row['real_text'],
        "label": row['label'],
        "predicted_label": predicted_label
    })

# Langkah 5: Menyimpan hasil sebagai JSON
with open('prediction_results.json', 'w') as outfile:
    json.dump(results, outfile, indent=4)

# Langkah 1: Memuat hasil prediksi dari JSON
with open('prediction_results.json', 'r') as file:
    predictions = json.load(file)

# Inisialisasi confusion matrix components
TP = TN = FP = FN = 0

# Langkah 2: Menghitung komponen confusion matrix
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

# Langkah 3: Menghitung metrik evaluasi
accuracy = (TP + TN) / (TP + TN + FP + FN)
precision = TP / (TP + FP) if (TP + FP) != 0 else 0
recall = TP / (TP + FN) if (TP + FN) != 0 else 0

# Menyiapkan dictionary untuk JSON
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
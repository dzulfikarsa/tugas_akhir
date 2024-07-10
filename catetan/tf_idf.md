# Penjelasan Kode TF-IDF

## Mengimpor Pustaka
```python
import math
import json
```

# Menghitung kemunculan global setiap kata di seluruh dokumen.

```python
def hitung_kemunculan_global(kalimat_list):
    global_kemunculan = {}
    total_kata = 0
    for kalimat in kalimat_list:
        kata_kalimat = kalimat[2].split()
        total_kata += len(kata_kalimat)
        for kata in kata_kalimat:
            if kata in global_kemunculan:
                global_kemunculan[kata] += 1
            else:
                global_kemunculan[kata] = 1
    return global_kemunculan, total_kata, len(kalimat_list)
```

# Menghitung Frekuensi Istilah (Term Frequency - TF) untuk setiap kata unik di setiap dokumen.

```python
def hitung_tf(data):
    hasil_tf = []
    for index, (id, real_text, clean_text, label) in enumerate(data):
        hasil_tf_per_kalimat = []
        kata_unik = set(clean_text.split())
        for kata in kata_unik:
            hasil = clean_text.count(kata) / len(clean_text.split())
            hasil_tf_per_kalimat.append((index, kata, hasil, label))
        hasil_tf.append(hasil_tf_per_kalimat)
    return hasil_tf
```

#  Menghitung Inverse Document Frequency (IDF) yang mengukur seberapa umum atau jarang kata tersebut muncul di seluruh dokumen.

```python
hasil_idf = []
    for index, (id, real_text, clean_text, label) in enumerate(data):
        hasil_idf_per_kalimat = []
        kata_unik = set(clean_text.split())
        for kata in kata_unik:
            hasil = math.log10(total_kalimat / global_kemunculan[kata])
            hasil_idf_per_kalimat.append((index, kata, hasil, label))
        hasil_idf.append(hasil_idf_per_kalimat)
    return hasil_idf
```

# Menggabungkan TF dan IDF untuk mendapatkan bobot TF-IDF, yang mengukur kepentingan kata dalam dokumen relatif terhadap keseluruhan korpus.

```python
def hitung_bobot(tf, idf, data):
    """
    Menggabungkan TF dan IDF untuk mendapatkan bobot TF-IDF, yang mengukur kepentingan kata dalam dokumen relatif terhadap keseluruhan korpus.
    """
    hasil_tf_idf = []
    idf_map = {(item[0], item[1], item[3]): item[2] for sublist in idf for item in sublist}
    for sublist in tf:
        new_sublist = []
        for item in sublist:
            index, kata, tf_score, label = item
            idf_score = idf_map.get((index, kata, label), 0)
            tf_idf_score = tf_score * idf_score
            new_sublist.append((index, kata, tf_idf_score, label))
        hasil_tf_idf.append(new_sublist)
    return hasil_tf_idf
```

# Gabungin TF dan IDF buat dapetin skor TF-IDF, yang ngasih tau pentingnya kata dalam konteks dokumen dan keseluruhan korpus.

```python
def hitung_bobot(tf, idf, data):
    hasil_tf_idf = []
    idf_map = {(item[0], item[1], item[3]): item[2] for sublist in idf for item in sublist}
```

# Ini cara kita mengukur seberapa penting kata itu dalam dokumen, dengan mempertimbangkan frekuensi kata di dokumen itu sendiri dan di semua dokumen.

```python
def save_to_json(data, file_path):
    """
    Simpen hasil ke dalam file JSON.
    """
    with open(file_path, 'w') as file:
        json.dump(data, file, indent=4)
```

#   

```python
def tf_idf(data):

    # Melanjutkan dengan proses selanjutnya tanpa case folding
    global_kemunculan, total_kata, total_kalimat = hitung_kemunculan_global(data) 
    # kata_per_kalimat = hitung_kata_per_kalimat(data)

    tf = hitung_tf(data)
    idf = hitung_idf(global_kemunculan, total_kalimat, data)
    bobot_kata = hitung_bobot(tf, idf, data)
    save_to_json(bobot_kata, 'tf_idf.json')
    return bobot_kata, idf
```
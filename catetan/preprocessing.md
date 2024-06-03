# Menggunakan library yang diperlukan untuk operasi file, regex, dan konektivitas database.
```python
import os
import json
import mysql.connector
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
import re
```

# Inisialisasi koneksi ke database.
```python
cursor = conn.cursor()
```

# Inisialisasi library untuk stemming dan penghapusan stopword.
```python
factory = StemmerFactory()
stemmer = factory.create_stemmer()

factory = StopWordRemoverFactory()
stopword_remover = factory.create_stop_word_remover()
```

# inisialisasi path file data.json
```python
file_path = r'C:\xampp\htdocs\tugas_akhir\tugas_akhir_ngoding\util\data.json'
```

# jika ada filenya
```python
if os.path.exists(file_path):
with open(file_path, 'r') as file:
```
# masukan data json ke variable data
```python
data = json.load(file)
```
# jika tidak ada filenya
```python
else:
print(f"File not found: {file_path}")
```
# isi variable data dengan list kosong
```python
data = []
```

# jalankan fungsi preprocessing dengan parameter data, simpan ke dalam variable processed_data
```python
processed_data = preprocessing(data)
```
# fungsi case folding dengan paratemer text
```python
def case_folding(text):
```
# Ubah teks menjadi huruf kecil
```python
text = text.lower()
return text
```

# fungsi slangword dengan parameter text dan slangword

```python
def replacing_slangword(text, slangword):
```

# kode untuk membuat kamus dictionary slangword
```python
# Pembuatan dictionary dari list slangword yang sudah ada
slang_dict = {slang[2]: slang[1] for slang in slangword}

#kode di atas adalah singkatan dari kode di bawah

slang_dict = {}
for slang in slangword:
    key = slang[2]
    value = slang[1]
    slang_dict[key] = value
```
# proses untuk merubah kata slang

```python
# Proses penggantian kata dilakukan sekali jalan
return ' '.join(slang_dict.get(word, word) for word in text.split())

#kode di atas adalah singkatan dari kode di bawah
words = text.split()
translated_words = []
for word in words:
    translated_word = slang_dict.get(word, word)
    translated_words.append(translated_word)
translated_text = ' '.join(translated_words)
return translated_text
```
# fungsi cleansing dengan parameter text 
```python
def cleansing(text):    
```
# proses untuk menghilangkan karakter-karakter non-alfabet dan juga menghilangkan kelebihan spasi
```python
text = re.sub(r'[^a-z\s]+|\s+', ' ', text).strip()
return text
```

# fungsi stopword dengan parameter text
```python
def stopword_removal(text):
```

# proses untuk menghapus stopwords dari sebuah teks
```python
text = stopword_remover.remove(text)
return text
```

# fungsi stemming dengan parameter text dan exclude_list
```python
def stemming(text, exclude_list):
```
# proses untuk melakukan "stemming" pada teks dengan pengecualian tertentu
```python
# Langsung menggunakan comprehension dan join dalam satu baris
return ' '.join(cached_stem(word) if word not in exclude_list else word for word in text.split())

#kode di atas adalah singkatan dari kode di bawah
words = text.split()  # Memisahkan teks menjadi kata-kata
stemmed_words = []  # Inisialisasi daftar untuk menyimpan kata-kata yang sudah di-stem atau yang dikecualikan

for word in words:
    if word not in exclude_list:
        # Jika kata tidak ada dalam daftar kecualian, lakukan stemming
        stemmed_word = cached_stem(word)
    else:
        # Jika kata ada dalam daftar kecualian, gunakan kata asli
        stemmed_word = word
    stemmed_words.append(stemmed_word)  # Tambahkan kata yang sudah diolah ke daftar

# Gabungkan semua kata yang telah diproses kembali menjadi satu string
stemmed_text = ' '.join(stemmed_words)
return stemmed_text
```
# fungsi preprocessing dengan parameter data
```python 
def preprocessing(data):
	#mengambil semua data dari table slangword	
    cursor.execute("SELECT * FROM slangword")
	# masukan ke variable slangword	
    slangword = cursor.fetchall()
```
# perulangan untuk setiap baris di dalam data
```python
for baris in data:
    # lakukan fungsi case_folding dengan parameter baris dengan kata kunci 'title', kemudian simpan hasilnya di variable hasil
    hasil = case_folding(baris['title'])
	# lakukan fungsi cleansing dengan parameter hasil, variable tersebut didapat dari fungsi case folding kemudian simpan hasilnya di variable hasil
    hasil = cleansing(hasil)
	# lakukan fungsi stopword dengan parameter hasil, variable tersebut didapat dari fungsi cleansing kemudian simpan hasilnya di variable hasil
    hasil = stopword_removal(hasil)
	# lakukan fungsi slangword dengan parameter hasil dan slangword, variable tersebut didapat dari fungsi stopword dan slangword kemudian simpan hasilnya di variable hasil
    hasil = replacing_slangword(hasil, slangword)
	# lakukan fungsi stemming dengan parameter hasil, variable tersebut didapat dari fungsi slangword, kemudian simpan hasilnya di variable hasil
    hasil = stemming(hasil)
	# masukan data yang sudah dipreprocessing di variable hasil ke variable baris['title']
    baris['title'] = hasil
```


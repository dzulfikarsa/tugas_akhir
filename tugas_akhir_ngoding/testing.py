from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import MultinomialNB
from sklearn.metrics import accuracy_score

# Contoh data teks dan labelnya
data = [
    ("Saya suka bermain sepak bola", "positif"),
    ("Saya membenci bangun pagi", "negatif"),
    ("Saya senang makan pizza", "positif"),
    ("Saya sedih mendengar berita itu", "negatif"),
    ("Bermain musik membuat saya bahagia", "positif"),
    ("Saya kesal terjebak macet", "negatif")
]

# Memisahkan teks dan label
texts = [text for text, label in data]
labels = [label for text, label in data]

# Menerapkan TF-IDF
vectorizer = TfidfVectorizer()
X = vectorizer.fit_transform(texts)
print(X)

# Memisahkan data menjadi data latih dan data uji
X_train, X_test, y_train, y_test = train_test_split(X, labels, test_size=0.5, random_state=42)

# Membangun dan melatih model Naive Bayes
model = MultinomialNB()
model.fit(X_train, y_train)

# Menguji model
y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)

# print("Akurasi model: {:.2f}%".format(accuracy * 100))

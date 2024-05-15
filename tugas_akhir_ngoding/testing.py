import pickle

def load_from_pickle(filename):
    # Memuat data dari file pickle
    with open(filename, 'rb') as f:
        data = pickle.load(f)
    return data

# Contoh memuat probabilities dari model.pkl
probabilities = load_from_pickle('model.pkl')

# Mencetak data yang dimuat
print(probabilities)

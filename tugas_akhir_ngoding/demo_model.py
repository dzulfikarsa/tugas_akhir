import sys
import json
import math
from util.preprocessing import preprocessing
import mysql.connector


def load_model(filename):
    with open(filename, 'r') as file:
        data = json.load(file)
        prior_hoax = data[0]
        prior_non_hoax = data[1]
        likelihood_hoax = data[2]["0"]
        likelihood_non_hoax = data[2]["1"]
    
    return prior_hoax, prior_non_hoax, likelihood_hoax, likelihood_non_hoax

def calculate_probabilities(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax):
    words = text.lower().split()
    log_prob_hoax = math.log(prior_hoax)
    log_prob_non_hoax = math.log(prior_non_hoax)
    
    for word in words:
        log_prob_hoax += math.log(likelihood_hoax.get(word, 1e-10))
        log_prob_non_hoax += math.log(likelihood_non_hoax.get(word, 1e-10))
    
    return log_prob_hoax, log_prob_non_hoax

def classify_text(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax):
    log_prob_hoax, log_prob_non_hoax = calculate_probabilities(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax)
    
    if log_prob_hoax > log_prob_non_hoax:
        return 'Hoax'
    else:
        return 'Non-Hoax'

if __name__ == "__main__":
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",    
        database="deteksi_hoax"
    )

    cursor = conn.cursor()
    prior_hoax, prior_non_hoax, likelihood_hoax, likelihood_non_hoax = load_model("model.json")
    text = sys.argv[1]
    data = [{
        "title": text,
    }]
    classification = classify_text(text, likelihood_hoax, likelihood_non_hoax, prior_hoax, prior_non_hoax)
    print(classification)

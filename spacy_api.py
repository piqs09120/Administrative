from flask import Flask, request, jsonify
import spacy

app = Flask(__name__)
nlp = spacy.load("en_core_web_sm")

@app.route('/classify', methods=['POST'])
def classify():
    data = request.get_json()
    text = data.get('text', '')
    doc = nlp(text)
    # Example: Just return named entities for now
    entities = [{"text": ent.text, "label": ent.label_} for ent in doc.ents]

    # Simple rule-based document classification
    lowered = text.lower()
    if any(word in lowered for word in ["contract", "agreement", "party", "witnesseth"]):
        category = "Contract"
    elif any(word in lowered for word in ["memorandum", "memo"]):
        category = "Memorandum"
    elif any(word in lowered for word in ["non-disclosure", "nda", "confidentiality"]):
        category = "Non-Disclosure Agreement (NDA)"
    elif any(word in lowered for word in ["legal notice", "notice is hereby given", "hereby notified"]):
        category = "Legal Notice"   
    elif any(word in lowered for word in ["policy", "regulation", "guideline"]):
        category = "Policy Document"
    else:
        category = "Uncategorized"

    return jsonify({"entities": entities, "category": category})

if __name__ == '__main__':
    app.run(port=5050) 
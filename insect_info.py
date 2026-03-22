import sys
import os
from transformers import T5Tokenizer, T5ForConditionalGeneration

# Model path
MODEL_PATH = "/opt/models/hub/models--google--flan-t5-base/snapshots/7bcac572ce56db69c1ea7c8af255c5d7c9672fc2"

def generate_insect_info(insect_name):
    try:
        # Load model and tokenizer
        tokenizer = T5Tokenizer.from_pretrained(MODEL_PATH, local_files_only=True)
        model = T5ForConditionalGeneration.from_pretrained(MODEL_PATH, local_files_only=True)

        # Generate information about habitat
        prompt_habitat = f"Where does a {insect_name} live?"
        input_ids = tokenizer(prompt_habitat, return_tensors="pt").input_ids
        outputs = model.generate(input_ids, max_length=100)
        habitat = tokenizer.decode(outputs[0], skip_special_tokens=True)

        # Generate information about diet
        prompt_diet = f"What does a {insect_name} eat?"
        input_ids = tokenizer(prompt_diet, return_tensors="pt").input_ids
        outputs = model.generate(input_ids, max_length=100)
        diet = tokenizer.decode(outputs[0], skip_special_tokens=True)

        # Generate information about danger
        prompt_danger = f"Is a {insect_name} dangerous to humans?"
        input_ids = tokenizer(prompt_danger, return_tensors="pt").input_ids
        outputs = model.generate(input_ids, max_length=100)
        danger = tokenizer.decode(outputs[0], skip_special_tokens=True)

        # Generate interesting facts
        prompt_facts = f"What are interesting facts about {insect_name}?"
        input_ids = tokenizer(prompt_facts, return_tensors="pt").input_ids
        outputs = model.generate(input_ids, max_length=150)
        facts = tokenizer.decode(outputs[0], skip_special_tokens=True)

        # Helper to ensure dot at the end
        def ensure_dot(text):
            text = text.strip()
            if not text.endswith('.'):
                text += '.'
            return text

        # Output results with dots
        print(f"HABITAT: {ensure_dot(habitat)}")
        print(f"DIET: {ensure_dot(diet)}")
        print(f"DANGER: {ensure_dot(danger)}")
        print(f"FACTS: {ensure_dot(facts)}")

    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python insect_info.py <insect_name>")
    else:
        insect_name = sys.argv[1]
        generate_insect_info(insect_name)

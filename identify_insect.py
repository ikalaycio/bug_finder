import sys
import os
import torch
from PIL import Image
from transformers import EfficientNetImageProcessor, EfficientNetForImageClassification

# Model path
MODEL_PATH = "/opt/models/hub/models--google--efficientnet-b0/snapshots/3d8d75d0812b65064f0151b456f869c6344bf20c"

def identify_insect(image_path):
    if not os.path.exists(image_path):
        print(f"Error: Image not found at {image_path}")
        return

    try:
        # Load model and processor
        processor = EfficientNetImageProcessor.from_pretrained(MODEL_PATH, local_files_only=True)
        model = EfficientNetForImageClassification.from_pretrained(MODEL_PATH, local_files_only=True)

        # Load and process image
        image = Image.open(image_path).convert("RGB")
        inputs = processor(images=image, return_tensors="pt")

        # Inference
        with torch.no_grad():
            outputs = model(**inputs)
            logits = outputs.logits
            predicted_class_idx = logits.argmax(-1).item()
            label = model.config.id2label[predicted_class_idx]
            
        print(label)
    except Exception as e:
        print(f"Error: {str(e)}")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python identify_insect.py <image_path>")
    else:
        identify_insect(sys.argv[1])

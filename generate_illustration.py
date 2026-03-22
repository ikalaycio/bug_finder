import sys
import os
import torch
from diffusers import AmusedPipeline

# Model path
MODEL_PATH = "/opt/models/hub/models--amused--amused-256/snapshots/09b6259bf96dbe6d70a852b70812420fe02df55e"

def generate_illustration(insect_name, output_path):
    try:
        # Load the pipeline
        pipe = AmusedPipeline.from_pretrained(
            MODEL_PATH, 
            local_files_only=True
        )
        pipe.to("cuda" if torch.cuda.is_available() else "cpu")

        # Create prompt
        prompt = f"A cute, simple, and colorful cartoon illustration of a {insect_name}, high quality, vector art style, white background"

        # Generate image
        image = pipe(prompt).images[0]

        # Save image
        image.save(output_path)
        print(f"Illustration generated successfully at {output_path}")

    except Exception as e:
        print(f"Error generating illustration: {str(e)}")

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python generate_illustration.py <insect_name> <output_path>")
    else:
        insect_name = sys.argv[1]
        output_path = sys.argv[2]
        generate_illustration(insect_name, output_path)

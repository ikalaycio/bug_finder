import sys
import os
import torch
from transformers import AutoProcessor, MusicgenForConditionalGeneration
import scipy.io.wavfile as wavfile

# Model path
MODEL_PATH = "/opt/models/hub/models--facebook--musicgen-small/snapshots/4c8334b02c6ec4e8664a91979669a501ec497792"

def load_txt_content(txt_path):
    """Load and parse the txt file content"""
    with open(txt_path, 'r') as f:
        lines = f.readlines()
    
    insect_name = ""
    danger_line = ""
    
    for line in lines:
        line = line.strip()
        if line.startswith("Identified Insect:"):
            insect_name = line.split(":", 1)[1].strip()
        elif line.startswith("DANGER:"):
            danger_line = line.split(":", 1)[1].strip().lower()
    
    return insect_name, danger_line

def is_dangerous(danger_text):
    """Determine if the insect is dangerous based on the danger text"""
    dangerous_keywords = ['dangerous', 'venomous', 'poisonous', 'harmful', 'deadly', 'toxic', 'aggressive', 'killer', 'sting', 'bite', 'painful', 'carnivore', 'predator']
    
    danger_text = danger_text.lower()
    for keyword in dangerous_keywords:
        if keyword in danger_text:
            return True
    return False

def generate_music(txt_path, duration_seconds, output_path):
    if not os.path.exists(txt_path):
        print(f"Error: TXT file not found at {txt_path}")
        return
    
    # Load and parse txt content
    insect_name, danger_line = load_txt_content(txt_path)
    
    if not insect_name:
        print("Error: Could not find insect name in txt file")
        return
    
    is_danger = is_dangerous(danger_line)
    
    if is_danger:
        print(f"Detected dangerous insect: {insect_name}")
        print("Generating scary and dangerous music...")
        prompt = f"Scary horror movie soundtrack, tense and ominous atmosphere, dark orchestral music, dangerous insect theme, {duration_seconds} seconds"
    else:
        print(f"Detected non-dangerous insect: {insect_name}")
        print("Generating relaxing music...")
        prompt = f"Peaceful nature ambient music, gentle and calm atmosphere, relaxing forest sounds, {duration_seconds} seconds"
    
    try:
        # Load model and processor
        processor = AutoProcessor.from_pretrained(MODEL_PATH, local_files_only=True)
        model = MusicgenForConditionalGeneration.from_pretrained(MODEL_PATH, local_files_only=True)
        
        # Process the prompt
        inputs = processor(
            text=[prompt],
            padding=True,
            return_tensors="pt",
        )
        
        # Calculate number of tokens needed for duration
        # MusicGen generates at 50 tokens per second
        max_new_tokens = int(duration_seconds * 50)
        
        # Generate music
        with torch.no_grad():
            audio_values = model.generate(
                **inputs,
                max_new_tokens=max_new_tokens,
                do_sample=True,
                guidance_scale=3.0,
            )
        
        # Get audio array and sampling rate
        sampling_rate = model.config.audio_encoder.sampling_rate
        audio = audio_values[0, 0].cpu().numpy()
        
        # Save to file
        wavfile.write(output_path, rate=sampling_rate, data=audio)
        print(f"Music generated successfully at {output_path}")
        
    except Exception as e:
        print(f"Error generating music: {str(e)}")

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print("Usage: python generate_music.py <txt_path> <duration_seconds> <output_path>")
    else:
        txt_path = sys.argv[1]
        duration_seconds = int(sys.argv[2])
        output_path = sys.argv[3]
        generate_music(txt_path, duration_seconds, output_path)

import sys
import os
import subprocess

# Piper paths
PIPER_BINARY = "/opt/piper/piper"
PIPER_MODEL = "/opt/piper/models/en_US-lessac-medium.onnx"

def generate_speech(text, output_path):
    if not os.path.exists(PIPER_BINARY):
        print(f"Error: Piper binary not found at {PIPER_BINARY}")
        return
    if not os.path.exists(PIPER_MODEL):
        print(f"Error: Piper model not found at {PIPER_MODEL}")
        return

    try:
        # Construct the command
        # echo "text" | piper --model model.onnx --output_file output.wav
        command = [
            PIPER_BINARY,
            "--model", PIPER_MODEL,
            "--output_file", output_path
        ]
        
        process = subprocess.Popen(command, stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
        stdout, stderr = process.communicate(input=text)
        
        if process.returncode == 0:
            print(f"Speech generated successfully at {output_path}")
        else:
            print(f"Error generating speech: {stderr}")
            
    except Exception as e:
        print(f"Exception occurred: {str(e)}")

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python generate_speech.py <text> <output_path>")
    else:
        text = sys.argv[1]
        output_path = sys.argv[2]
        generate_speech(text, output_path)

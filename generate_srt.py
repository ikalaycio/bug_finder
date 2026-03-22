import sys
import os
import wave
import contextlib

def get_audio_duration(audio_path):
    with contextlib.closing(wave.open(audio_path, 'r')) as f:
        frames = f.getnframes()
        rate = f.getframerate()
        duration = frames / float(rate)
        return duration

def format_timestamp(seconds):
    hours = int(seconds // 3600)
    minutes = int((seconds % 3600) // 60)
    secs = int(seconds % 60)
    millis = int((seconds % 1) * 1000)
    return f"{hours:02}:{minutes:02}:{secs:02},{millis:03}"

def generate_srt(txt_path, audio_path, srt_path):
    if not os.path.exists(txt_path) or not os.path.exists(audio_path):
        print(f"Error: Required files missing. TXT: {txt_path}, Audio: {audio_path}")
        return

    with open(txt_path, 'r') as f:
        lines = f.readlines()

    # Include ALL lines from TXT (including "Identified Insect:")
    info_lines = [line.strip() for line in lines if line.strip()]
    
    if not info_lines:
        print("No information lines found in TXT.")
        return

    duration = get_audio_duration(audio_path)
    # Calculate duration per line based on word count for better timing
    total_words = sum(len(line.split()) for line in info_lines)
    
    with open(srt_path, 'w') as f:
        current_time = 0.0
        for i, line in enumerate(info_lines):
            word_count = len(line.split())
            # Duration proportional to word count, with minimum 1 second per line
            line_duration = max(1.0, (word_count / total_words) * duration)
            start_time = current_time
            end_time = min(current_time + line_duration, duration)
            
            f.write(f"{i+1}\n")
            f.write(f"{format_timestamp(start_time)} --> {format_timestamp(end_time)}\n")
            f.write(f"{line}\n\n")
            
            current_time = end_time
            # Ensure we don't exceed total duration
            if current_time >= duration:
                break

    print(f"SRT generated successfully at {srt_path}")

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print("Usage: python generate_srt.py <txt_path> <audio_path> <srt_path>")
    else:
        generate_srt(sys.argv[1], sys.argv[2], sys.argv[3])

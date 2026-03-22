# Bug Finder Application

A comprehensive PHP CLI and Python application for identifying insects from images, generating detailed information, creating multimedia content, and producing videos.

## Features

- **Insect Identification**: Uses EfficientNet-B0 to identify insects from images
- **Information Generation**: Uses Flan-T5 to generate habitat, diet, danger level, and facts
- **Text-to-Speech**: Converts information to speech using Piper
- **Subtitle Generation**: Creates SRT files synchronized with speech
- **Music Generation**: Generates background music based on insect danger level using MusicGen
- **Cartoon Illustration**: Creates cute cartoon images using AMUSED-256
- **Video Creation**: Combines all elements into MP4 videos
- **File Management**: Manage and delete generated files

## Requirements

- PHP CLI
- Python 3
- FFmpeg
- Models (pre-downloaded in /opt/models):
  - EfficientNet-B0 (insect identification)
  - Flan-T5-base (information generation)
  - Piper (text-to-speech)
  - MusicGen-small (music generation)
  - AMUSED-256 (image generation)

## Available Commands

### 1. Bug Finder (`bug_finder.php`)

Identifies insects from images and generates detailed information.

```bash
php bug_finder.php
```

**Workflow:**
1. Select an image from the directory
2. Identify the insect using EfficientNet-B0
3. Generate detailed information (habitat, diet, danger, facts) using Flan-T5
4. Save information to TXT file
5. Convert text to speech using Piper

**Output Files:**
- `{insect_name}.txt` - Information file
- `{insect_name}.wav` - Speech audio

---

### 2. Music Generator (`music_generator.php`)

Generates background music based on insect danger level.

```bash
php music_generator.php
```

**Workflow:**
1. Select a TXT file
2. Enter music duration in seconds
3. Generate music (scary for dangerous insects, relaxing for safe ones)

**Output Files:**
- `{insect_name}_music.wav` - Background music

---

### 3. SRT Generator (`srt_generator.php`)

Creates subtitle files from TXT and speech audio.

```bash
php srt_generator.php
```

**Workflow:**
1. Select a TXT file
2. System suggests matching subtitle file
3. Select a subtitle file
4. Generate synchronized SRT file

**Output Files:**
- `{insect_name}.srt` - Subtitle file

---

### 4. Illustration Generator (`illustration_generator.php`)

Creates cute cartoon illustrations of insects.

```bash
php illustration_generator.php
```

**Workflow:**
1. Select an image
2. Identify the insect
3. Generate cartoon illustration using AMUSED-256
4. Option to override or create new version if file exists

**Output Files:**
- `{insect_name}_illustration.png` - Cartoon image

---

### 5. Video Generator (`video_generator.php`)

Creates MP4 videos combining image, speech, music, and subtitles.

```bash
php video_generator.php
```

**Workflow:**
1. Select an image for the video
2. Select a speech WAV file
3. Select a music WAV file (loops if shorter than speech)
4. Select a subtitle SRT file (system suggests matching subtitle)
5. Option to override or create new version if file exists
6. Generate final MP4 video

**Output Files:**
- `{image_name}.mp4` - Final video

---

### 6. Auto Generator (`auto_generator.php`)

Fully automated workflow that generates everything in one command.

```bash
php auto_generator.php
```

**Workflow:**
1. Select an image
2. Enter music duration in seconds
3. Automatically execute all steps:
   - Identify insect
   - Generate information
   - Save TXT file
   - Generate cartoon illustration
   - Generate speech (includes full TXT content)
   - Generate SRT subtitles
   - Generate background music
   - Create MP4 video
   - Zip all files into an archive

**Output Files:**
- `{insect_name}.txt` - Information file
- `{insect_name}_illustration.png` - Cartoon image
- `{insect_name}.wav` - Speech audio
- `{insect_name}.srt` - Subtitle file
- `{insect_name}_music.wav` - Background music
- `{insect_name}.mp4` - Final video
- `{insect_name}_package.zip` - Archive of all files

---

### 7. File Manager (`file_manager.php`)

Manage and delete generated files (system files are protected).

```bash
php file_manager.php
```

**Workflow:**
1. View list of deletable files (excludes .php and .py files)
2. Select files by:
   - Entering numbers separated by commas (e.g., "1,3,5")
   - Typing 'all' to select all files
   - Typing 'q' to quit
3. Confirm deletion
4. Files are permanently deleted

**Protected Files:**
- All .php files (system commands)
- All .py files (Python scripts)

---

## File Structure

```
bug_finder/
├── bug_finder.php              # Main insect identification
├── music_generator.php         # Background music generation
├── srt_generator.php          # Subtitle generation
├── illustration_generator.php  # Cartoon image generation
├── video_generator.php        # Video creation
├── auto_generator.php         # Fully automated workflow
├── file_manager.php           # File deletion management
├── identify_insect.py         # Insect identification (EfficientNet-B0)
├── insect_info.py             # Information generation (Flan-T5)
├── generate_speech.py         # Text-to-speech (Piper)
├── generate_srt.py            # SRT subtitle generation
├── generate_music.py          # Music generation (MusicGen)
├── generate_illustration.py   # Image generation (AMUSED-256)
└── README.md                  # This file
```

## Model Paths

Models are pre-downloaded and available offline at:
- `/opt/models/hub/models--google--efficientnet-b0/`
- `/opt/models/hub/models--google--flan-t5-base/`
- `/opt/models/hub/models--facebook--musicgen-small/`
- `/opt/models/hub/models--amused--amused-256/`
- `/opt/piper/models/en_US-lessac-medium.onnx`

## Usage Examples

### Quick Start - Full Automation
```bash
php auto_generator.php
# Select image → Enter duration → All files generated automatically
```

### Step-by-Step Workflow
```bash
# 1. Identify insect and generate info
php bug_finder.php

# 2. Create cartoon illustration
php illustration_generator.php

# 3. Generate background music
php music_generator.php

# 4. Create subtitles
php srt_generator.php

# 5. Combine into video
php video_generator.php
```

### Clean Up Files
```bash
php file_manager.php
# Select files to delete → Confirm → Files removed
```

## Notes

- Video duration matches speech length exactly
- Music loops if shorter than speech
- Subtitles are synchronized with speech timing
- System files (.php, .py) cannot be deleted via file manager
- Generated files are saved in the current directory
- Archives are created as .zip (or .tar.gz if zip unavailable)
# bug_finder
# bug_finder

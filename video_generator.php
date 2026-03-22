<?php

function listFilesByExt($dir, $extensions) {
    $files = scandir($dir);
    $filteredFiles = [];
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $extensions)) {
            $filteredFiles[] = $file;
        }
    }
    return $filteredFiles;
}

echo "Welcome to Video Generator!\n";
$currentDir = getcwd();

// Step 1: List and select an image
$imgExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
$images = listFilesByExt($currentDir, $imgExtensions);

if (empty($images)) {
    echo "No images found in the current directory.\n";
    exit;
}

echo "Please select an image for the video:\n";
foreach ($images as $index => $image) {
    echo ($index + 1) . ". $image\n";
}

$imgSelection = -1;
while ($imgSelection < 1 || $imgSelection > count($images)) {
    echo "Enter your selection (1-" . count($images) . "): ";
    $input = trim(fgets(STDIN));
    if (is_numeric($input)) {
        $imgSelection = (int)$input;
    }
}
$selectedImage = $images[$imgSelection - 1];

// Step 2: List and select a speech wav
$wavFiles = listFilesByExt($currentDir, ['wav']);

if (empty($wavFiles)) {
    echo "No wav files found in the current directory.\n";
    exit;
}

echo "\nPlease select a speech wav file:\n";
foreach ($wavFiles as $index => $wav) {
    echo ($index + 1) . ". $wav\n";
}

$wavSelection = -1;
while ($wavSelection < 1 || $wavSelection > count($wavFiles)) {
    echo "Enter your selection (1-" . count($wavFiles) . "): ";
    $input = trim(fgets(STDIN));
    if (is_numeric($input)) {
        $wavSelection = (int)$input;
    }
}
$selectedWav = $wavFiles[$wavSelection - 1];

// Step 3: List and select a music wav
$musicFiles = listFilesByExt($currentDir, ['wav']);
echo "\nPlease select a music wav file (loops if shorter than speech):\n";
foreach ($musicFiles as $index => $music) {
    echo ($index + 1) . ". $music\n";
}

$musicSelection = -1;
while ($musicSelection < 1 || $musicSelection > count($musicFiles)) {
    echo "Enter your selection (1-" . count($musicFiles) . "): ";
    $input = trim(fgets(STDIN));
    if (is_numeric($input)) {
        $musicSelection = (int)$input;
    }
}
$selectedMusic = $musicFiles[$musicSelection - 1];

// Step 4: List and select a subtitle file
$srtFiles = listFilesByExt($currentDir, ['srt']);
$speechBaseName = pathinfo($selectedWav, PATHINFO_FILENAME);
$suggestedSrt = $speechBaseName . ".srt";

echo "\nPlease select a subtitle file:\n";
foreach ($srtFiles as $index => $srt) {
    $suggestion = ($srt === $suggestedSrt) ? " [SUGGESTED]" : "";
    echo ($index + 1) . ". $srt$suggestion\n";
}

$srtSelection = -1;
while ($srtSelection < 1 || $srtSelection > count($srtFiles)) {
    echo "Enter your selection (1-" . count($srtFiles) . "): ";
    $input = trim(fgets(STDIN));
    if (is_numeric($input)) {
        $srtSelection = (int)$input;
    }
}
$selectedSrt = $srtFiles[$srtSelection - 1];

// Step 5: Handle output filename and versioning
$baseName = pathinfo($selectedImage, PATHINFO_FILENAME);
$outputFilename = $baseName . ".mp4";

if (file_exists($outputFilename)) {
    echo "\nThe file $outputFilename already exists. Would you like to:\n";
    echo "1. Override it\n";
    echo "2. Create a new version\n";
    echo "Enter your choice (1-2): ";
    $choice = trim(fgets(STDIN));
    if ($choice === "2") {
        $counter = 1;
        while (file_exists($baseName . "_v$counter.mp4")) {
            $counter++;
        }
        $outputFilename = $baseName . "_v$counter.mp4";
    }
}

echo "\nGenerating video $outputFilename...\n";

// FFmpeg command:
// - loop image
// - loop music
// - add speech
// - scale image (divisible by 2)
// - apply subtitles (must escape path for ffmpeg filter)
// - mix audio (speech + music)
// - shortest duration (matches speech length)
$escapedSrt = str_replace([':', '\\'], ['\\:', '/'], $selectedSrt);
$command = "ffmpeg -y -loop 1 -i " . escapeshellarg($selectedImage) . 
           " -stream_loop -1 -i " . escapeshellarg($selectedMusic) . 
           " -i " . escapeshellarg($selectedWav) . 
           " -filter_complex \"[0:v]scale='if(gt(iw,ih),-2,min(iw,1280))':'if(gt(iw,ih),min(ih,720),-2)',format=yuv420p,subtitles=" . $escapedSrt . "[v];" .
           " [1:a][2:a]amix=inputs=2:duration=shortest[a]\" " .
           " -map \"[v]\" -map \"[a]\" -c:v libx264 -tune stillimage -c:a aac -b:a 192k -shortest " . 
           escapeshellarg($outputFilename);

$output = [];
$returnVar = 0;
exec($command . " 2>&1", $output, $returnVar);

if ($returnVar === 0) {
    echo "Video generated successfully: $outputFilename\n";
} else {
    echo "Error generating video:\n";
    echo implode("\n", $output);
}

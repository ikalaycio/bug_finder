<?php

function listImages($dir) {
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $files = scandir($dir);
    $images = [];
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $extensions)) {
            $images[] = $file;
        }
    }
    return $images;
}

echo "========================================\n";
echo "  BUG FINDER AUTO GENERATOR\n";
echo "========================================\n\n";

$currentDir = getcwd();
$images = listImages($currentDir);

if (empty($images)) {
    echo "No images found in the current directory.\n";
    exit;
}

echo "Step 1: Select an image\n";
echo "------------------------\n";
foreach ($images as $index => $image) {
    echo ($index + 1) . ". $image\n";
}

$selection = -1;
while ($selection < 1 || $selection > count($images)) {
    echo "Enter your selection (1-" . count($images) . "): ";
    $input = trim(fgets(STDIN));
    if (is_numeric($input)) {
        $selection = (int)$input;
    }
}

$selectedImage = $images[$selection - 1];
echo "Selected: $selectedImage\n\n";

// Ask for music duration
echo "How many seconds of music do you want to generate? ";
$musicDuration = -1;
while ($musicDuration < 1) {
    $durationInput = trim(fgets(STDIN));
    if (is_numeric($durationInput) && (int)$durationInput > 0) {
        $musicDuration = (int)$durationInput;
    } else {
        echo "Please enter a valid number of seconds: ";
    }
}
echo "Music duration: $musicDuration seconds\n\n";

// Step 2: Identify insect
echo "========================================\n";
echo "Step 2: Identifying insect from image...\n";
echo "========================================\n";
$command = "python3 identify_insect.py " . escapeshellarg($selectedImage);
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

if ($returnVar !== 0) {
    echo "Error identifying insect.\n";
    exit;
}

$insectName = trim(implode("\n", $output));
echo "Identified Insect: $insectName\n\n";

// Step 3: Generate detailed information
echo "========================================\n";
echo "Step 3: Generating detailed information...\n";
echo "========================================\n";
$infoCommand = "python3 insect_info.py " . escapeshellarg($insectName);
$infoOutput = [];
$infoReturnVar = 0;
exec($infoCommand, $infoOutput, $infoReturnVar);

if ($infoReturnVar !== 0) {
    echo "Error generating information.\n";
    exit;
}

$infoContent = "";
echo "=== Insect Information ===\n";
foreach ($infoOutput as $line) {
    echo $line . "\n";
    $infoContent .= $line . "\n";
}
echo "\n";

// Step 4: Save TXT file
echo "========================================\n";
echo "Step 4: Saving information to TXT file...\n";
echo "========================================\n";
$filenameBase = str_replace(' ', '_', strtolower($insectName));
$txtFilename = $filenameBase . ".txt";
$fileContent = "Identified Insect: $insectName\n$infoContent";
file_put_contents($txtFilename, $fileContent);
echo "Saved: $txtFilename\n\n";

// Step 5: Generate cartoon illustration
echo "========================================\n";
echo "Step 5: Generating cartoon illustration...\n";
echo "========================================\n";
$illustrationFilename = $filenameBase . "_illustration.png";
$illustrationCommand = "python3 generate_illustration.py " . escapeshellarg($insectName) . " " . escapeshellarg($illustrationFilename);
$illustrationOutput = [];
$illustrationReturnVar = 0;
exec($illustrationCommand, $illustrationOutput, $illustrationReturnVar);

if ($illustrationReturnVar === 0) {
    echo "Saved: $illustrationFilename\n\n";
} else {
    echo "Error generating illustration.\n";
    print_r($illustrationOutput);
    exit;
}

// Step 6: Generate speech (includes full TXT content)
echo "========================================\n";
echo "Step 6: Converting text to speech...\n";
echo "========================================\n";
$speechFilename = $filenameBase . ".wav";
$speechCommand = "python3 generate_speech.py " . escapeshellarg($fileContent) . " " . escapeshellarg($speechFilename);
$speechOutput = [];
$speechReturnVar = 0;
exec($speechCommand, $speechOutput, $speechReturnVar);

if ($speechReturnVar === 0) {
    echo "Saved: $speechFilename\n\n";
} else {
    echo "Error generating speech.\n";
    print_r($speechOutput);
    exit;
}

// Step 7: Generate SRT file
echo "========================================\n";
echo "Step 7: Generating SRT subtitle file...\n";
echo "========================================\n";
$srtFilename = $filenameBase . ".srt";
$srtCommand = "python3 generate_srt.py " . escapeshellarg($txtFilename) . " " . escapeshellarg($speechFilename) . " " . escapeshellarg($srtFilename);
$srtOutput = [];
$srtReturnVar = 0;
exec($srtCommand, $srtOutput, $srtReturnVar);

if ($srtReturnVar === 0) {
    echo "Saved: $srtFilename\n\n";
} else {
    echo "Error generating SRT.\n";
    print_r($srtOutput);
    exit;
}

// Step 8: Generate music
echo "========================================\n";
echo "Step 8: Generating background music...\n";
echo "========================================\n";
$musicFilename = $filenameBase . "_music.wav";
$musicCommand = "python3 generate_music.py " . escapeshellarg($txtFilename) . " " . $musicDuration . " " . escapeshellarg($musicFilename);
$musicOutput = [];
$musicReturnVar = 0;
exec($musicCommand, $musicOutput, $musicReturnVar);

if ($musicReturnVar === 0) {
    echo "Saved: $musicFilename\n\n";
} else {
    echo "Error generating music.\n";
    print_r($musicOutput);
    exit;
}

// Step 9: Generate final video
echo "========================================\n";
echo "Step 9: Creating final MP4 video...\n";
echo "========================================\n";
$videoFilename = $filenameBase . ".mp4";

// Handle versioning if file exists
if (file_exists($videoFilename)) {
    $counter = 1;
    while (file_exists($filenameBase . "_v$counter.mp4")) {
        $counter++;
    }
    $videoFilename = $filenameBase . "_v$counter.mp4";
}

// FFmpeg command with illustration, music, speech, and subtitles
$escapedSrt = str_replace([':', '\\'], ['\\:', '/'], $srtFilename);
$command = "ffmpeg -y -loop 1 -i " . escapeshellarg($illustrationFilename) . 
           " -stream_loop -1 -i " . escapeshellarg($musicFilename) . 
           " -i " . escapeshellarg($speechFilename) . 
           " -filter_complex \"[0:v]scale='if(gt(iw,ih),-2,min(iw,1280))':'if(gt(iw,ih),min(ih,720),-2)',format=yuv420p,subtitles=" . $escapedSrt . "[v];" .
           " [1:a][2:a]amix=inputs=2:duration=shortest[a]\" " .
           " -map \"[v]\" -map \"[a]\" -c:v libx264 -tune stillimage -c:a aac -b:a 192k -shortest " . 
           escapeshellarg($videoFilename);

$videoOutput = [];
$videoReturnVar = 0;
exec($command . " 2>&1", $videoOutput, $videoReturnVar);

if ($videoReturnVar === 0) {
    echo "Saved: $videoFilename\n\n";
} else {
    echo "Error generating video.\n";
    echo implode("\n", $videoOutput);
    exit;
}

// Step 10: Zip all generated files
echo "========================================\n";
echo "Step 10: Zipping all generated files...\n";
echo "========================================\n";
$zipFilename = $filenameBase . "_package.zip";
$zipCommand = "zip " . escapeshellarg($zipFilename) . " " .
              escapeshellarg($txtFilename) . " " .
              escapeshellarg($illustrationFilename) . " " .
              escapeshellarg($speechFilename) . " " .
              escapeshellarg($srtFilename) . " " .
              escapeshellarg($musicFilename) . " " .
              escapeshellarg($videoFilename);
$zipOutput = [];
$zipReturnVar = 0;
exec($zipCommand . " 2>&1", $zipOutput, $zipReturnVar);

if ($zipReturnVar === 0) {
    echo "Saved: $zipFilename\n\n";
} else {
    echo "Error creating zip file. Trying tar.gz...\n";
    $tarFilename = $filenameBase . "_package.tar.gz";
    $tarCommand = "tar -czf " . escapeshellarg($tarFilename) . " " .
                  escapeshellarg($txtFilename) . " " .
                  escapeshellarg($illustrationFilename) . " " .
                  escapeshellarg($speechFilename) . " " .
                  escapeshellarg($srtFilename) . " " .
                  escapeshellarg($musicFilename) . " " .
                  escapeshellarg($videoFilename);
    $tarOutput = [];
    $tarReturnVar = 0;
    exec($tarCommand . " 2>&1", $tarOutput, $tarReturnVar);
    
    if ($tarReturnVar === 0) {
        echo "Saved: $tarFilename\n\n";
    } else {
        echo "Error creating archive.\n";
    }
}

// Summary
echo "========================================\n";
echo "  AUTOMATION COMPLETE!\n";
echo "========================================\n";
echo "Generated files:\n";
echo "  - $txtFilename\n";
echo "  - $illustrationFilename\n";
echo "  - $speechFilename\n";
echo "  - $srtFilename\n";
echo "  - $musicFilename\n";
echo "  - $videoFilename (FINAL VIDEO)\n";
echo "  - $zipFilename (or tar.gz) (ARCHIVE)\n";
echo "\n";

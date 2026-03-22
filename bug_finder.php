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

echo "Welcome to Bug Finder!\n";
$currentDir = getcwd();
$images = listImages($currentDir);

if (empty($images)) {
    echo "No images found in the current directory.\n";
    exit;
}

echo "Please select an image from the list:\n";
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
echo "Analyzing $selectedImage...\n";

// Run Python script
$command = "python3 identify_insect.py " . escapeshellarg($selectedImage);
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

if ($returnVar !== 0) {
    echo "Error running identification script.\n";
    print_r($output);
    exit;
}

$result = trim(implode("\n", $output));
echo "Identified Insect: $result\n\n";

// Get detailed information about the insect
echo "Generating detailed information...\n\n";
$infoCommand = "python3 insect_info.py " . escapeshellarg($result);
$infoOutput = [];
$infoReturnVar = 0;
exec($infoCommand, $infoOutput, $infoReturnVar);

$infoContent = "";
if ($infoReturnVar === 0) {
    echo "=== Insect Information ===\n";
    foreach ($infoOutput as $line) {
        echo $line . "\n";
        $infoContent .= $line . "\n";
    }
    echo "\n";
} else {
    echo "Error generating information.\n";
    print_r($infoOutput);
}

// Save result to file
$filenameBase = str_replace(' ', '_', strtolower($result));
$txtFilename = $filenameBase . ".txt";
$fileContent = "Identified Insect: $result\n$infoContent";
file_put_contents($txtFilename, $fileContent);
echo "Result saved to $txtFilename\n";

// Convert information to speech using Piper (now includes full TXT content)
echo "Converting information to speech...\n";
$wavFilename = $filenameBase . ".wav";
$speechCommand = "python3 generate_speech.py " . escapeshellarg($fileContent) . " " . escapeshellarg($wavFilename);
$speechOutput = [];
$speechReturnVar = 0;
exec($speechCommand, $speechOutput, $speechReturnVar);

if ($speechReturnVar === 0) {
    echo "Speech saved to $wavFilename\n";
} else {
    echo "Error generating speech.\n";
    print_r($speechOutput);
}

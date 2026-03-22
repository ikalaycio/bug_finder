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

echo "Welcome to Illustration Generator!\n";
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
$baseName = pathinfo($selectedImage, PATHINFO_FILENAME);
$outputFilename = $baseName . "_illustration.png";

if (file_exists($outputFilename)) {
    echo "The file $outputFilename already exists. Would you like to:\n";
    echo "1. Override it\n";
    echo "2. Create a new version\n";
    echo "Enter your choice (1-2): ";
    $choice = trim(fgets(STDIN));
    if ($choice === "2") {
        $counter = 1;
        while (file_exists($baseName . "_illustration_v$counter.png")) {
            $counter++;
        }
        $outputFilename = $baseName . "_illustration_v$counter.png";
    }
}

echo "Analyzing $selectedImage to identify insect...\n";

// We first need to identify the insect to provide a good prompt
$idCommand = "python3 identify_insect.py " . escapeshellarg($selectedImage);
$idOutput = [];
$idReturnVar = 0;
exec($idCommand, $idOutput, $idReturnVar);

if ($idReturnVar !== 0) {
    echo "Error identifying insect.\n";
    exit;
}
$insectName = trim(implode("\n", $idOutput));
echo "Identified as: $insectName. Generating illustration...\n";

// Run illustration generation
$genCommand = "python3 generate_illustration.py " . escapeshellarg($insectName) . " " . escapeshellarg($outputFilename);
$genOutput = [];
$genReturnVar = 0;
exec($genCommand, $genOutput, $genReturnVar);

if ($genReturnVar === 0) {
    echo "Illustration saved to $outputFilename\n";
} else {
    echo "Error generating illustration.\n";
    print_r($genOutput);
}
